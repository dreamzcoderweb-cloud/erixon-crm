<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData();
        }

        return view('customers.view');
    }

    public function listData()
    {
        $customers = Customer::forUser(Auth::user())
            ->with('creator:id,name')
            ->orderBy('customer_id', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $customers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_type' => ['required', 'in:user,reseller'],
            'name'          => ['required', 'string', 'max:255'],
            'company_name'  => ['nullable', 'string', 'max:255'],
            'mobile'        => ['required', 'string', 'max:20', Rule::unique('customers', 'mobile')->withoutTrashed()],
            'email'         => ['nullable', 'email', 'max:255'],
            'alternate_mobile' => ['nullable', 'string', 'max:20'],
            'address'       => ['nullable', 'string'],
            'city'          => ['nullable', 'string', 'max:100'],
            'state'         => ['nullable', 'string', 'max:100'],
            'country'       => ['nullable', 'string', 'max:100'],
            'pincode'       => ['nullable', 'string', 'max:20'],
            'status'        => ['required', 'in:0,1'],
        ]);

        $validated['created_by'] = Auth::id();

        $customer = Customer::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Customer created successfully.',
            'data'    => $customer
        ]);
    }

    public function edit($id)
    {
        $customer = Customer::forUser(Auth::user())->find($id);
        if (!$customer) {
            return response()->json([
                'status'  => false,
                'message' => 'Customer not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $customer
        ]);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::forUser(Auth::user())->find($id);
        if (!$customer) {
            return response()->json([
                'status'  => false,
                'message' => 'Customer not found.'
            ], 404);
        }

        $validated = $request->validate([
            'customer_type' => ['required', 'in:user,reseller'],
            'name'          => ['required', 'string', 'max:255'],
            'company_name'  => ['nullable', 'string', 'max:255'],
            'mobile'        => ['required', 'string', 'max:20', Rule::unique('customers', 'mobile')->ignore($customer->customer_id, 'customer_id')->withoutTrashed()],
            'email'         => ['nullable', 'email', 'max:255'],
            'alternate_mobile' => ['nullable', 'string', 'max:20'],
            'address'       => ['nullable', 'string'],
            'city'          => ['nullable', 'string', 'max:100'],
            'state'         => ['nullable', 'string', 'max:100'],
            'country'       => ['nullable', 'string', 'max:100'],
            'pincode'       => ['nullable', 'string', 'max:20'],
            'status'        => ['required', 'in:0,1'],
        ]);

        $customer->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Customer updated successfully.',
            'data'    => $customer
        ]);
    }

    public function destroy($id)
    {
        $customer = Customer::forUser(Auth::user())->find($id);
        if (!$customer) {
            return response()->json([
                'status'  => false,
                'message' => 'Customer not found.'
            ], 404);
        }

        $customer->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Customer deleted successfully.'
        ]);
    }

    public function changeStatus(Request $request, $id)
    {
        $customer = Customer::forUser(Auth::user())->find($id);
        if (!$customer) {
            return response()->json([
                'status'  => false,
                'message' => 'Customer not found.'
            ], 404);
        }

        $customer->status = $customer->status == 1 ? 0 : 1;
        $customer->save();

        return response()->json([
            'status'  => true,
            'message' => 'Customer status updated successfully.',
            'new_status' => $customer->status
        ]);
    }

    /**
     * Requirement 11: Find customer by Phone number, Mail ID, or Name
     */
    public function search(Request $request)
    {
        $term = trim($request->input('term', $request->input('q', '')));

        $query = Customer::forUser(Auth::user())->where('status', 1);

        if (!empty($term)) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('mobile', 'LIKE', "%{$term}%")
                  ->orWhere('email', 'LIKE', "%{$term}%")
                  ->orWhere('company_name', 'LIKE', "%{$term}%");
            });
        }

        $customers = $query->orderBy('name', 'ASC')->limit(20)->get();

        return response()->json([
            'status' => true,
            'data'   => $customers
        ]);
    }

    /**
     * Requirement 14: Import Customer Data Option (CSV file)
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:10240'],
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return response()->json(['status' => false, 'message' => 'Unable to read the uploaded CSV file.'], 422);
        }

        $header = fgetcsv($handle, 1000, ',');
        if (!$header) {
            fclose($handle);
            return response()->json(['status' => false, 'message' => 'Uploaded CSV file is empty.'], 422);
        }

        // Normalize header keys
        $headerMap = [];
        foreach ($header as $index => $colName) {
            $normalized = strtolower(trim(str_replace([' ', '_', '-'], '', $colName)));
            $headerMap[$normalized] = $index;
        }

        $importedCount = 0;
        $skippedCount  = 0;
        $errors        = [];
        $rowNum        = 1;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $rowNum++;

            $name           = isset($headerMap['name']) ? trim($row[$headerMap['name']] ?? '') : ($row[0] ?? '');
            $mobile         = isset($headerMap['mobile']) ? trim($row[$headerMap['mobile']] ?? '') : ($row[1] ?? '');
            $email          = isset($headerMap['email']) ? trim($row[$headerMap['email']] ?? '') : ($row[2] ?? '');
            $companyName    = isset($headerMap['companyname']) ? trim($row[$headerMap['companyname']] ?? '') : ($row[3] ?? '');
            $customerType   = isset($headerMap['customertype']) ? strtolower(trim($row[$headerMap['customertype']] ?? 'user')) : 'user';
            $alternateMobile = isset($headerMap['alternatemobile']) ? trim($row[$headerMap['alternatemobile']] ?? '') : '';
            $address        = isset($headerMap['address']) ? trim($row[$headerMap['address']] ?? '') : '';
            $city           = isset($headerMap['city']) ? trim($row[$headerMap['city']] ?? '') : '';
            $state          = isset($headerMap['state']) ? trim($row[$headerMap['state']] ?? '') : '';
            $country        = isset($headerMap['country']) ? trim($row[$headerMap['country']] ?? '') : '';
            $pincode        = isset($headerMap['pincode']) ? trim($row[$headerMap['pincode']] ?? '') : '';

            if (empty($name) || empty($mobile)) {
                $skippedCount++;
                $errors[] = "Row {$rowNum}: Missing name or mobile number.";
                continue;
            }

            // Check duplicate by mobile
            $existing = Customer::where('mobile', $mobile)->withTrashed()->first();
            if ($existing) {
                $skippedCount++;
                $errors[] = "Row {$rowNum}: Customer with mobile '{$mobile}' already exists.";
                continue;
            }

            if (!in_array($customerType, ['user', 'reseller'])) {
                $customerType = 'user';
            }

            Customer::create([
                'customer_type'    => $customerType,
                'name'             => $name,
                'mobile'           => $mobile,
                'email'            => !empty($email) ? $email : null,
                'company_name'     => !empty($companyName) ? $companyName : null,
                'alternate_mobile' => !empty($alternateMobile) ? $alternateMobile : null,
                'address'          => !empty($address) ? $address : null,
                'city'             => !empty($city) ? $city : null,
                'state'            => !empty($state) ? $state : null,
                'country'          => !empty($country) ? $country : null,
                'pincode'          => !empty($pincode) ? $pincode : null,
                'status'           => 1,
                'created_by'       => Auth::id(),
            ]);

            $importedCount++;
        }

        fclose($handle);

        return response()->json([
            'status'         => true,
            'message'        => "Customer import complete. Imported: {$importedCount}, Skipped: {$skippedCount}.",
            'imported_count' => $importedCount,
            'skipped_count'  => $skippedCount,
            'errors'         => $errors,
        ]);
    }

    public function downloadSampleCsv()
    {
        $csvContent = "Name,Mobile,Email,Company Name,Customer Type,Alternate Mobile,Address,City,State,Country,Pincode\n";
        $csvContent .= "John Doe,9876543210,john@example.com,Acme Corp,user,9876543211,123 Main St,Chennai,Tamil Nadu,India,600001\n";
        $csvContent .= "Jane Smith,9123456789,jane@example.com,Global Resellers,reseller,,456 Tech Park,Bangalore,Karnataka,India,560001\n";

        return response($csvContent, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customer_import_sample.csv"',
        ]);
    }
}
