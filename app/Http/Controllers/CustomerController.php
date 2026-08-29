<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Models\CustomerCustomField;
use App\Models\LeadSetting;
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
        $customFields = CustomerCustomField::where('status', 1)->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();

        $standardFields = [
            'customer_type'    => 'Type',
            'name'             => 'Name',
            'company_name'     => 'Company Name',
            'mobile'           => 'Mobile',
            'email'            => 'Email',
            'alternate_mobile' => 'Alternate Mobile',
            'address'          => 'Address',
            'city'             => 'City',
            'state'            => 'State',
            'country'          => 'Country',
            'pincode'          => 'Pincode',
            'created_at'       => 'Created At',
            'created_by'       => 'Created By',
            'status'           => 'Status',
        ];

        $allAvailableFieldsMap = [];
        foreach ($standardFields as $key => $label) {
            $allAvailableFieldsMap[$key] = [
                'key'   => $key,
                'label' => $label,
                'type'  => 'standard',
            ];
        }

        foreach ($customFields as $cf) {
            $allAvailableFieldsMap[$cf->field_name] = [
                'key'   => $cf->field_name,
                'label' => $cf->field_label,
                'type'  => 'custom',
                'field' => $cf,
            ];
        }

        $setting = LeadSetting::getSettings();
        $savedColumns = $setting->customer_list_columns;

        if (empty($savedColumns) || !is_array($savedColumns)) {
            $savedColumns = array_keys($allAvailableFieldsMap);
        } else {
            $savedColumns = array_values(array_filter($savedColumns, function ($key) use ($allAvailableFieldsMap) {
                return isset($allAvailableFieldsMap[$key]);
            }));
        }

        $visibleColumns = [];
        foreach ($savedColumns as $colKey) {
            if (isset($allAvailableFieldsMap[$colKey])) {
                $visibleColumns[] = $allAvailableFieldsMap[$colKey];
            }
        }

        return view('customers.view', compact('staffs', 'allUsers', 'customFields', 'visibleColumns'));
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
        [$customRules, $customAttributes] = $this->getCustomFieldsRules();

        $baseRules = [
            'customer_type'    => ['required', 'in:user,reseller'],
            'name'             => ['required', 'string', 'max:255'],
            'company_name'     => ['nullable', 'string', 'max:255'],
            'mobile'           => ['required', 'string', 'max:20', Rule::unique('customers', 'mobile')->withoutTrashed()],
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
        ];

        $rules = array_merge($baseRules, $customRules);
        $validated = $request->validate($rules, [], $customAttributes);

        $validated['created_by']    = Auth::id();
        $validated['custom_fields'] = $this->processCustomFieldsPayload($validated['custom_fields'] ?? []);

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

        [$customRules, $customAttributes] = $this->getCustomFieldsRules();

        $baseRules = [
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
        ];

        $rules = array_merge($baseRules, $customRules);
        $validated = $request->validate($rules, [], $customAttributes);

        $validated['custom_fields'] = $this->processCustomFieldsPayload($validated['custom_fields'] ?? []);

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
        $query = $request->input('q', '');

        if (strlen($query) < 1) {
            return response()->json([
                'status' => true,
                'data'   => []
            ]);
        }

        $customers = Customer::forUser(Auth::user())
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('mobile', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('company_name', 'LIKE', "%{$query}%");
            })
            ->where('status', 1)
            ->limit(20)
            ->get(['customer_id', 'name', 'mobile', 'email', 'company_name', 'customer_type']);

        return response()->json([
            'status' => true,
            'data'   => $customers
        ]);
    }

    /**
     * Export Customers to Excel (.xlsx)
     */
    public function export(Request $request)
    {
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

        $customers = $query->with(['creator:id,name', 'owner:id,name', 'assignedBy:id,name'])
            ->orderBy('customer_id', 'DESC')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Customers');

        $headers = [
            'S.No', 'Customer Type', 'Name', 'Company Name', 'Mobile',
            'Email', 'Alternate Mobile', 'Address', 'City', 'State',
            'Country', 'Pincode', 'Status', 'Created By', 'Created At'
        ];

        $sheet->fromArray($headers, null, 'A1');

        $rowNum = 2;
        foreach ($customers as $index => $c) {
            $sheet->fromArray([
                $index + 1,
                ucfirst($c->customer_type),
                $c->name,
                $c->company_name ?? '-',
                $c->mobile,
                $c->email ?? '-',
                $c->alternate_mobile ?? '-',
                $c->address ?? '-',
                $c->city ?? '-',
                $c->state ?? '-',
                $c->country ?? '-',
                $c->pincode ?? '-',
                $c->status == 1 ? 'Active' : 'Inactive',
                $c->creator ? $c->creator->name : '-',
                $c->created_at ? $c->created_at->format('d-m-Y H:i') : '-'
            ], null, "A{$rowNum}");
            $rowNum++;
        }

        $fileName = 'customers_export_' . date('Y_m_d_H_i_s') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function getCustomFieldsRules()
    {
        $customFields = CustomerCustomField::where('status', 1)->get();
        $rules = [];
        $attributes = [];

        foreach ($customFields as $cf) {
            $key = 'custom_fields.' . $cf->field_name;
            $fieldRules = [];

            if ($cf->is_required === 'Yes') {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            switch ($cf->field_type) {
                case 'Number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'Date':
                    $fieldRules[] = 'date';
                    break;
                case 'Dropdown':
                case 'Text':
                case 'Textarea':
                case 'Checkbox':
                default:
                    $fieldRules[] = 'string';
                    break;
            }

            $rules[$key] = $fieldRules;
            $attributes[$key] = $cf->field_label;
        }

        return [$rules, $attributes];
    }

    private function processCustomFieldsPayload(array $customFieldsData = [])
    {
        $allFields = CustomerCustomField::where('status', 1)->get();
        foreach ($allFields as $field) {
            if ($field->field_type === 'Checkbox') {
                if (isset($customFieldsData[$field->field_name]) && ($customFieldsData[$field->field_name] == 1 || $customFieldsData[$field->field_name] === '1' || $customFieldsData[$field->field_name] === 'Yes')) {
                    $customFieldsData[$field->field_name] = '1';
                } else {
                    $customFieldsData[$field->field_name] = '0';
                }
            }
        }
        return $customFieldsData;
    }
}
