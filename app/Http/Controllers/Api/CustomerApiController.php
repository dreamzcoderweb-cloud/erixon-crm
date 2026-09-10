<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerCustomField;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerApiController extends Controller
{
    /**
     * Get user options formatted for dropdowns in mobile app (Owner By, Created By, Assign By).
     */
    private function getUserDropdownOptions()
    {
        return User::orderBy('name', 'asc')->get(['id', 'name', 'email'])->map(function ($u) {
            $emailSuffix = !empty($u->email) ? " ({$u->email})" : "";
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'label' => $u->name . $emailSuffix,
            ];
        })->values();
    }

    /**
     * Get formatted customer custom fields definition.
     */
    private function getFormattedCustomFields()
    {
        $customFields = CustomerCustomField::where('status', 1)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return $customFields->map(function ($cf) {
            $options = [];
            if (!empty($cf->field_options)) {
                $options = array_values(array_filter(array_map('trim', explode(',', $cf->field_options))));
            }

            $isRequired = in_array(strtolower((string) $cf->is_required), ['yes', '1', 'true'], true);

            return [
                'id' => $cf->id,
                'field_name' => $cf->field_name,
                'field_label' => $cf->field_label,
                'field_type' => $cf->field_type, // 'Text', 'Number', 'Dropdown', 'Textarea', 'Date', 'Checkbox'
                'field_options' => $options,
                'raw_options' => $cf->field_options ?? '',
                'is_required' => $isRequired,
                'placeholder' => ($cf->field_type === 'Dropdown' ? 'Select ' : 'Enter ') . $cf->field_label,
                'sort_order' => (int) ($cf->sort_order ?? 0),
            ];
        })->values();
    }



    /**
     * Dedicated endpoint returning all form metadata, options, and defaults for Add/Edit Customer in mobile app.
     * Accessible via GET api/v1/customers/form-data
     */
    public function getFormData(Request $request)
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        $userOptions = $this->getUserDropdownOptions();
        $customFields = $this->getFormattedCustomFields();

        return response()->json([
            'status' => true,
            'message' => 'Customer form data retrieved successfully.',
            'data' => [
                'owner_by_options' => $userOptions,
                'assign_by_options' => $userOptions,
                'customer_types' => [
                    ['value' => 'user', 'label' => 'User'],
                    ['value' => 'reseller', 'label' => 'Reseller'],
                ],
                'statuses' => [
                    ['value' => 1, 'label' => 'Active'],
                    ['value' => 0, 'label' => 'Inactive'],
                ],
                'custom_fields' => $customFields,
                'defaults' => [
                    'created_by' => $currentUser ? $currentUser->id : null,
                    'created_by_name' => $currentUser ? $currentUser->name : null,
                    'owner_by' => $currentUser ? $currentUser->id : null,
                    'owner_by_name' => $currentUser ? $currentUser->name : null,
                    'assign_by' => null,
                    'status' => $currentUser ? $currentUser->status : null,

                ],
            ],
        ]);
    }

    /**
     * Dedicated endpoint returning users/staff list for dropdown selections.
     * Accessible via GET api/v1/customers/users
     */
    public function getUsers(Request $request)
    {
        $users = $this->getUserDropdownOptions();

        return response()->json([
            'status' => true,
            'message' => 'Users list retrieved successfully.',
            'data' => $users,
        ]);
    }

    /**
     * Store a new customer from the mobile app, supporting standard fields, dropdowns, and dynamic additional fields.
     * Accessible via POST api/v1/customers
     */
    public function store(Request $request)
    {
        // 1. Normalize customer_type
        $rawType = strtolower(trim((string) $request->input('customer_type', 'user')));
        $customerType = in_array($rawType, ['reseller', 'company']) ? 'reseller' : 'user';

        // 2. Normalize status
        $rawStatus = $request->input('status', 1);
        if (is_string($rawStatus)) {
            $lowerStatus = strtolower(trim($rawStatus));
            $status = ($lowerStatus === 'inactive' || $lowerStatus === '0') ? 0 : 1;
        } else {
            $status = $rawStatus ? 1 : 0;
        }

        // 3. Extract and consolidate custom fields
        $customFieldsInput = $request->input('custom_fields', []);
        if (is_string($customFieldsInput)) {
            $decoded = json_decode($customFieldsInput, true);
            $customFieldsInput = is_array($decoded) ? $decoded : [];
        } elseif (!is_array($customFieldsInput)) {
            $customFieldsInput = [];
        }

        $allConfiguredFields = CustomerCustomField::where('status', 1)->get();
        foreach ($allConfiguredFields as $cf) {
            if (!isset($customFieldsInput[$cf->field_name]) && $request->has($cf->field_name)) {
                $customFieldsInput[$cf->field_name] = $request->input($cf->field_name);
            }
        }

        // Remarks inside custom_fields or payload
        if ($request->has('remarks')) {
            $customFieldsInput['remarks'] = trim((string) $request->input('remarks'));
        }

        // Process Checkboxes to '1' or '0'
        foreach ($allConfiguredFields as $cf) {
            if ($cf->field_type === 'Checkbox') {
                if (isset($customFieldsInput[$cf->field_name])) {
                    $val = $customFieldsInput[$cf->field_name];
                    $customFieldsInput[$cf->field_name] = ($val == 1 || $val === '1' || $val === true || strtolower((string) $val) === 'yes') ? '1' : '0';
                } else {
                    $customFieldsInput[$cf->field_name] = '0';
                }
            }
        }

        // 4. Build Validation Rules
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:20', Rule::unique('customers', 'mobile')],
            'email' => ['nullable', 'email', 'max:255'],
            'alternate_mobile' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:20'],
            'owner_by' => ['nullable', 'exists:users,id'],
            'assign_by' => ['nullable', 'exists:users,id'],
            'created_by' => ['nullable', 'exists:users,id'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ];

        $attributes = [
            'name' => 'Customer Name',
            'mobile' => 'Mobile Number',
            'owner_by' => 'Owner By',
            'assign_by' => 'Assign By',
            'created_by' => 'Created By',
        ];

        // Dynamic validation rules from CustomerCustomField
        foreach ($allConfiguredFields as $cf) {
            $key = 'custom_fields.' . $cf->field_name;
            $fieldRules = [];

            $isReq = in_array(strtolower((string) $cf->is_required), ['yes', '1', 'true'], true);
            if ($isReq) {
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

        // Merge normalized custom_fields into request payload for validation
        $payload = $request->all();
        $payload['customer_type'] = $customerType;
        $payload['status'] = $status;
        $payload['custom_fields'] = $customFieldsInput;

        $validator = Validator::make($payload, $rules, [], $attributes);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        $creatorId = $request->filled('created_by') ? (int) $request->input('created_by') : ($user ? $user->id : 1);
        $ownerId = $request->filled('owner_by') ? (int) $request->input('owner_by') :  null;
        $assignId = $request->filled('assign_by') ? (int) $request->input('assign_by') : null;

        try {
            $customer = Customer::create([
                'customer_type' => $customerType,
                'name' => trim((string) $request->input('name')),
                'company_name' => $request->filled('company_name') ? trim((string) $request->input('company_name')) : null,
                'mobile' => trim((string) $request->input('mobile')),
                'email' => $request->filled('email') ? trim((string) $request->input('email')) : null,
                'alternate_mobile' => $request->filled('alternate_mobile') ? trim((string) $request->input('alternate_mobile')) : null,
                'address' => $request->input('address'),
                'city' => $request->input('city'),
                'state' => $request->input('state'),
                'country' => $request->input('country', 'India'),
                'pincode' => $request->input('pincode'),
                'owner_by' => $ownerId,
                'assign_by' => $assignId,
                'created_by' => $creatorId,
                'status' => $status,
                'custom_fields' => !empty($customFieldsInput) ? $customFieldsInput : null,
            ]);
        } catch (QueryException $e) {
            // Handle duplicate entry gracefully (MySQL 1062)
            if ($e->errorInfo[1] == 1062 || str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'status' => false,
                    'message' => 'The mobile number has already been taken.',
                    'errors' => [
                        'mobile' => ['The mobile number has already been taken.']
                    ],
                ], 422);
            }
            throw $e;
        }

        $customer->loadMissing([
            'creator:id,name,email',
            'owner:id,name,email',
            'assignedBy:id,name,email',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Customer created successfully.',
            'data' => $customer,
        ], 201);
    }

    /**
     * List customers for the logged-in staff user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Customer::with([
            'creator:id,name,email',
            'owner:id,name,email',
            'assignedBy:id,name,email',
        ])->orderBy('customer_id', 'desc');

        if ($user) {
            $query->forUser($user);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->input('customer_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('owner_by')) {
            $query->where('owner_by', $request->input('owner_by'));
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->input('created_by'));
        }

        $perPage = (int) $request->input('per_page', 20);
        $customers = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Customers retrieved successfully.',
            'data' => $customers->items(),
            'pagination' => [
                'total' => $customers->total(),
                'per_page' => $customers->perPage(),
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
            ],
        ]);
    }

    /**
     * Get a single customer by ID.
     */
    public function show($id)
    {
        $customer = $this->findCustomer($id);

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Customer not found.',
            ], 404);
        }

        $customer->loadMissing([
            'creator:id,name,email',
            'owner:id,name,email',
            'assignedBy:id,name,email',
        ]);

        return response()->json([
            'status' => true,
            'data' => $customer,
        ]);
    }

    /**
     * Fetch customer data along with active custom fields and dropdown options for pre-filling the mobile edit form.
     */
    public function edit($id)
    {
        $customer = $this->findCustomer($id);

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Customer not found.',
            ], 404);
        }

        $customer->loadMissing([
            'creator:id,name,email',
            'owner:id,name,email',
            'assignedBy:id,name,email',
        ]);

        $customFields = CustomerCustomField::where('status', 1)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $customerCustomFields = is_array($customer->custom_fields) ? $customer->custom_fields : [];

        $formattedFields = $customFields->map(function ($cf) use ($customerCustomFields) {
            $options = [];
            if (!empty($cf->field_options)) {
                $options = array_values(array_filter(array_map('trim', explode(',', $cf->field_options))));
            }

            $isRequired = in_array(strtolower((string) $cf->is_required), ['yes', '1', 'true'], true);
            $currentValue = $customerCustomFields[$cf->field_name] ?? null;

            return [
                'id' => $cf->id,
                'field_name' => $cf->field_name,
                'field_label' => $cf->field_label,
                'field_type' => $cf->field_type,
                'field_options' => $options,
                'raw_options' => $cf->field_options ?? '',
                'is_required' => $isRequired,
                'placeholder' => ($cf->field_type === 'Dropdown' ? 'Select ' : 'Enter ') . $cf->field_label,
                'current_value' => $currentValue,
                'sort_order' => (int) ($cf->sort_order ?? 0),
            ];
        });

        $userOptions = $this->getUserDropdownOptions();

        return response()->json([
            'status' => true,
            'message' => 'Customer details for edit retrieved successfully.',
            'data' => $customer,
            'custom_fields_definition' => $formattedFields,
            'owner_by_options' => $userOptions,
            'created_by_options' => $userOptions,
            'assign_by_options' => $userOptions,
            'customer_type_options' => [
                ['value' => 'user', 'label' => 'User'],
                ['value' => 'reseller', 'label' => 'Reseller'],
            ],
            'status_options' => [
                ['value' => 1, 'label' => 'Active'],
                ['value' => 0, 'label' => 'Inactive'],
            ],
        ]);
    }

    /**
     * Update an existing customer from the mobile app, supporting standard and additional custom fields.
     */
    public function update(Request $request, $id)
    {
        $customer = $this->findCustomer($id);

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Customer not found.',
            ], 404);
        }

        // 1. Normalize customer_type
        $customerType = $customer->customer_type;
        if ($request->has('customer_type')) {
            $rawType = strtolower(trim((string) $request->input('customer_type')));
            if (in_array($rawType, ['user'])) {
                $customerType = 'user';
            } elseif (in_array($rawType, ['reseller', 'company'])) {
                $customerType = 'reseller';
            }
        }

        // 2. Normalize status
        $status = $customer->status;
        if ($request->has('status')) {
            $rawStatus = $request->input('status');
            if (is_string($rawStatus)) {
                $lowerStatus = strtolower(trim($rawStatus));
                $status = ($lowerStatus === 'inactive' || $lowerStatus === '0') ? 0 : 1;
            } else {
                $status = $rawStatus ? 1 : 0;
            }
        }

        // 3. Extract and consolidate custom fields
        $existingCustomFields = is_array($customer->custom_fields) ? $customer->custom_fields : [];
        $customFieldsInput = $request->input('custom_fields', null);

        if (is_string($customFieldsInput)) {
            $decoded = json_decode($customFieldsInput, true);
            $customFieldsInput = is_array($decoded) ? $decoded : [];
        } elseif (!is_array($customFieldsInput)) {
            $customFieldsInput = [];
        }

        $allConfiguredFields = CustomerCustomField::where('status', 1)->get();
        foreach ($allConfiguredFields as $cf) {
            if (!isset($customFieldsInput[$cf->field_name]) && $request->has($cf->field_name)) {
                $customFieldsInput[$cf->field_name] = $request->input($cf->field_name);
            }
        }

        $mergedCustomFields = array_merge($existingCustomFields, $customFieldsInput);

        if ($request->has('remarks')) {
            $mergedCustomFields['remarks'] = trim((string) $request->input('remarks'));
        }

        // Process Checkboxes to '1' or '0'
        foreach ($allConfiguredFields as $cf) {
            if ($cf->field_type === 'Checkbox') {
                if (isset($mergedCustomFields[$cf->field_name])) {
                    $val = $mergedCustomFields[$cf->field_name];
                    $mergedCustomFields[$cf->field_name] = ($val == 1 || $val === '1' || $val === true || strtolower((string) $val) === 'yes') ? '1' : '0';
                }
            }
        }

        // 4. Build Validation Rules
        $rules = [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'mobile' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('customers', 'mobile')->ignore($customer->customer_id, 'customer_id')
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'alternate_mobile' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:20'],
            'owner_by' => ['nullable', 'exists:users,id'],
            'assign_by' => ['nullable', 'exists:users,id'],
            'created_by' => ['nullable', 'exists:users,id'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ];

        $attributes = [
            'name' => 'Customer Name',
            'mobile' => 'Mobile Number',
            'owner_by' => 'Owner By',
            'assign_by' => 'Assign By',
            'created_by' => 'Created By',
        ];

        foreach ($allConfiguredFields as $cf) {
            $key = 'custom_fields.' . $cf->field_name;
            $fieldRules = [];

            $isReq = in_array(strtolower((string) $cf->is_required), ['yes', '1', 'true'], true);
            if ($isReq && !isset($mergedCustomFields[$cf->field_name])) {
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

        $payload = $request->all();
        $payload['customer_type'] = $customerType;
        $payload['status'] = $status;
        $payload['custom_fields'] = $mergedCustomFields;

        $validator = Validator::make($payload, $rules, [], $attributes);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $updateData = [
            'customer_type' => $customerType,
            'status' => $status,
            'custom_fields' => !empty($mergedCustomFields) ? $mergedCustomFields : null,
        ];

        if ($request->has('name')) {
            $updateData['name'] = trim((string) $request->input('name'));
        }
        if ($request->has('company_name')) {
            $updateData['company_name'] = $request->filled('company_name') ? trim((string) $request->input('company_name')) : null;
        }
        if ($request->has('mobile')) {
            $updateData['mobile'] = trim((string) $request->input('mobile'));
        }
        if ($request->has('email')) {
            $updateData['email'] = $request->filled('email') ? trim((string) $request->input('email')) : null;
        }
        if ($request->has('alternate_mobile')) {
            $updateData['alternate_mobile'] = $request->filled('alternate_mobile') ? trim((string) $request->input('alternate_mobile')) : null;
        }
        if ($request->has('address')) {
            $updateData['address'] = $request->input('address');
        }
        if ($request->has('city')) {
            $updateData['city'] = $request->input('city');
        }
        if ($request->has('state')) {
            $updateData['state'] = $request->input('state');
        }
        if ($request->has('country')) {
            $updateData['country'] = $request->input('country');
        }
        if ($request->has('pincode')) {
            $updateData['pincode'] = $request->input('pincode');
        }
        if ($request->has('owner_by')) {
            $updateData['owner_by'] = $request->input('owner_by');
        }
        if ($request->has('assign_by')) {
            $updateData['assign_by'] = $request->input('assign_by');
        }
        if ($request->has('created_by')) {
            $updateData['created_by'] = $request->input('created_by');
        }

        try {
            $customer->update($updateData);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062 || str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'status' => false,
                    'message' => 'The mobile number has already been taken.',
                    'errors' => [
                        'mobile' => ['The mobile number has already been taken.']
                    ],
                ], 422);
            }
            throw $e;
        }

        $customer->loadMissing([
            'creator:id,name,email',
            'owner:id,name,email',
            'assignedBy:id,name,email',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Customer updated successfully.',
            'data' => $customer->fresh(),
        ]);
    }

    /**
     * Delete (soft-delete) customer.
     */
    public function destroy($id)
    {
        $customer = $this->findCustomer($id);

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Customer not found.',
            ], 404);
        }

        $customer->delete();

        return response()->json([
            'status' => true,
            'message' => 'Customer deleted successfully.',
        ]);
    }

    /**
     * Change customer status (Active / Inactive).
     */
    public function changeStatus(Request $request, $id)
    {
        $customer = $this->findCustomer($id);

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Customer not found.',
            ], 404);
        }

        if ($request->has('status')) {
            $rawStatus = $request->input('status');
            $newStatus = (strtolower((string) $rawStatus) === 'active' || $rawStatus == 1 || $rawStatus === '1') ? 1 : 0;
        } else {
            $newStatus = $customer->status == 1 ? 0 : 1;
        }

        $customer->status = $newStatus;
        $customer->save();

        return response()->json([
            'status' => true,
            'message' => 'Customer status updated successfully.',
            'data' => [
                'customer_id' => $customer->customer_id,
                'status' => $customer->status,
                'status_text' => $customer->status == 1 ? 'Active' : 'Inactive',
            ],
        ]);
    }

    /**
     * Find customer with staff data access scope or global fallback.
     */
    private function findCustomer($id)
    {
        $user = Auth::user();
        if ($user) {
            $cust = Customer::forUser($user)->find($id);
            if ($cust) {
                return $cust;
            }
        }
        return Customer::find($id);
    }
}
