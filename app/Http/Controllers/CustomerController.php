<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData($request);
        }

        $user = Auth::user();
        if ($user && $user->isSuperAdmin()) {
            $staffs = User::staffOnly()->orderBy('name')->get();
        } else {
            $staffs = User::where('id', Auth::id())->get();
        }

        $allUsers = User::orderBy('name')->get();

        return view('customers.view', compact('staffs', 'allUsers'));
    }

    public function listData(Request $request = null)
    {
        $request = $request ?? request();

        $query = Customer::forUser(Auth::user());

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->input('customer_type'));
        }

        if ($request->filled('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->input('created_by'));
        }

        $filterType = $request->input('filter_type');
        $date       = $request->input('date');
        $month      = $request->input('month');
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');

        if ($filterType === 'daily' && !empty($date)) {
            $query->whereDate('created_at', $date);
        } elseif ($filterType === 'weekly') {
            $refDate = !empty($startDate) ? Carbon::parse($startDate) : Carbon::today();
            $query->whereBetween('created_at', [
                $refDate->copy()->startOfWeek(),
                $refDate->copy()->endOfWeek(),
            ]);
        } elseif ($filterType === 'monthly' && !empty($month)) {
            [$year, $selectedMonth] = array_pad(explode('-', $month), 2, null);
            $query->whereYear('created_at', $year ?: date('Y'))
                ->whereMonth('created_at', $selectedMonth ?: date('m'));
        } elseif ($filterType === 'custom') {
            if (!empty($startDate)) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }

        $customers = (clone $query)->with(['creator:id,name', 'owner:id,name', 'assignedBy:id,name'])
            ->orderBy('customer_id', 'DESC')
            ->get();

        $baseCountQuery = Customer::forUser(Auth::user());

        if ($request->filled('status') && $request->input('status') !== '') {
            $baseCountQuery->where('status', $request->input('status'));
        }

        if ($request->filled('created_by')) {
            $baseCountQuery->where('created_by', $request->input('created_by'));
        }

        if ($filterType === 'daily' && !empty($date)) {
            $baseCountQuery->whereDate('created_at', $date);
        } elseif ($filterType === 'weekly') {
            $refDate = !empty($startDate) ? Carbon::parse($startDate) : Carbon::today();
            $baseCountQuery->whereBetween('created_at', [
                $refDate->copy()->startOfWeek(),
                $refDate->copy()->endOfWeek(),
            ]);
        } elseif ($filterType === 'monthly' && !empty($month)) {
            [$year, $selectedMonth] = array_pad(explode('-', $month), 2, null);
            $baseCountQuery->whereYear('created_at', $year ?: date('Y'))
                ->whereMonth('created_at', $selectedMonth ?: date('m'));
        } elseif ($filterType === 'custom') {
            if (!empty($startDate)) {
                $baseCountQuery->whereDate('created_at', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $baseCountQuery->whereDate('created_at', '<=', $endDate);
            }
        }

        $resellcount = (clone $baseCountQuery)->where('customer_type', 'reseller')->count();
        $user        = (clone $baseCountQuery)->where('customer_type', 'user')->count();
        $staffcount  = (clone $baseCountQuery)->whereNotNull('created_by')->count();

        return response()->json([
            'status'      => true,
            'data'        => $customers,
            'resellcount' => $resellcount,
            'user'        => $user,
            'staffcount'  => $staffcount
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
            'pincode'          => ['nullable', 'string', 'max:20'],
            'owner_by'         => ['nullable', 'exists:users,id'],
            'assign_by'        => ['nullable', 'exists:users,id'],
            'status'           => ['required', 'in:0,1'],
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
            'customer_type'    => ['required', 'in:user,reseller'],
            'name'             => ['required', 'string', 'max:255'],
            'company_name'     => ['nullable', 'string', 'max:255'],
            'mobile'           => ['required', 'string', 'max:20', Rule::unique('customers', 'mobile')->ignore($customer->customer_id, 'customer_id')->withoutTrashed()],
            'email'            => ['nullable', 'email', 'max:255'],
            'alternate_mobile' => ['nullable', 'string', 'max:20'],
            'address'          => ['nullable', 'string'],
            'city'             => ['nullable', 'string', 'max:100'],
            'state'            => ['nullable', 'string', 'max:100'],
            'country'          => ['nullable', 'string', 'max:100'],
            'pincode'          => ['nullable', 'string', 'max:20'],
            'owner_by'         => ['nullable', 'exists:users,id'],
            'assign_by'        => ['nullable', 'exists:users,id'],
            'status'           => ['required', 'in:0,1'],
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
     * Requirement 14: Import Customer Data Option (Excel / CSV)
     */
    public function import(Request $request)
    {
        $file = $request->file('excel_file') ?? $request->file('csv_file');

        if (!$file) {
            return response()->json(['status' => false, 'message' => 'Please upload an Excel or CSV file.'], 422);
        }

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet   = $spreadsheet->getActiveSheet();
            $rows        = $worksheet->toArray(null, true, true, true);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Unable to parse uploaded file: ' . $e->getMessage()], 422);
        }

        if (empty($rows)) {
            return response()->json(['status' => false, 'message' => 'Uploaded file is empty.'], 422);
        }

        // Header row
        $headerRow = array_shift($rows);
        $headerMap = [];
        foreach ($headerRow as $colLetter => $colName) {
            if (!empty($colName)) {
                $normalized = strtolower(trim(str_replace([' ', '_', '-'], '', (string)$colName)));
                $headerMap[$normalized] = $colLetter;
            }
        }

        $importedCount = 0;
        $skippedCount  = 0;
        $errors        = [];
        $rowNum        = 1;

        foreach ($rows as $row) {
            $rowNum++;

            $name            = isset($headerMap['name']) ? trim((string)($row[$headerMap['name']] ?? '')) : '';
            $mobile          = isset($headerMap['mobile']) ? trim((string)($row[$headerMap['mobile']] ?? '')) : '';
            $email           = isset($headerMap['email']) ? trim((string)($row[$headerMap['email']] ?? '')) : '';
            $companyName     = isset($headerMap['companyname']) ? trim((string)($row[$headerMap['companyname']] ?? '')) : '';
            $customerType    = isset($headerMap['customertype']) ? strtolower(trim((string)($row[$headerMap['customertype']] ?? 'user'))) : 'user';
            $alternateMobile = isset($headerMap['alternatemobile']) ? trim((string)($row[$headerMap['alternatemobile']] ?? '')) : '';
            $address         = isset($headerMap['address']) ? trim((string)($row[$headerMap['address']] ?? '')) : '';
            $city            = isset($headerMap['city']) ? trim((string)($row[$headerMap['city']] ?? '')) : '';
            $state           = isset($headerMap['state']) ? trim((string)($row[$headerMap['state']] ?? '')) : '';
            $country         = isset($headerMap['country']) ? trim((string)($row[$headerMap['country']] ?? '')) : '';
            $pincode         = isset($headerMap['pincode']) ? trim((string)($row[$headerMap['pincode']] ?? '')) : '';

            if (empty($name) && empty($mobile)) {
                continue; // Skip blank rows
            }

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
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Name', 'Mobile', 'Email', 'Company Name', 'Customer Type', 'Alternate Mobile', 'Address', 'City', 'State', 'Country', 'Pincode'];
        $sheet->fromArray([$headers], null, 'A1');

        $sampleData = [
            ['John Doe', '9876543210', 'john@example.com', 'Acme Corp', 'user', '9876543211', '123 Main St', 'Chennai', 'Tamil Nadu', 'India', '600001'],
            ['Jane Smith', '9123456789', 'jane@example.com', 'Global Resellers', 'reseller', '', '456 Tech Park', 'Bangalore', 'Karnataka', 'India', '560001']
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'customer_import_sample.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
