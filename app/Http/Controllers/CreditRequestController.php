<?php

namespace App\Http\Controllers;

use App\Models\CreditRequest;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\User;
use App\Notifications\CreditRequestApprovedByAdmin;
use App\Notifications\CreditRequestApprovedByProductManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditRequestController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData($request);
        }

        $user = Auth::user();
        $data['leads']       = Lead::forUser($user)->with('customer')->orderBy('lead_id', 'DESC')->get();
        $data['leadSources'] = LeadSource::where('status', 1)->orderBy('lead_sources_id')->get();
        $data['customers']   = Customer::forUser($user)->where('status', 1)->orderBy('name')->get();

        $customFields = \App\Models\CreditRequestCustomField::where('status', 1)->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();

        $standardFields = [
            'customer_info' => 'Customer / User',
            'contact_info'  => 'Phone / Email',
            'lead_source'   => 'Lead Source',
            'credit_amount' => 'Credit Amount',
            'is_estimate'   => 'Type',
            'status'        => 'Status',
            'requested_by'  => 'Requested By',
            'created_at'    => 'Date',
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

        $setting = \App\Models\LeadSetting::getSettings();
        $savedColumns = $setting->credit_request_list_columns;

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

        $data['customFields']   = $customFields;
        $data['visibleColumns'] = $visibleColumns;

        return view('credit_requests.view', $data);
    }

    public function listData(Request $request)
    {
        $user   = Auth::user();
        $status = $request->input('status');

        $query = CreditRequest::forUser($user)->with([
            'lead:lead_id,lead_title,customer_id',
            'lead.customer:customer_id,name,mobile,email',
            'customer:customer_id,name,mobile,email,credit_balance',
            'leadSource:lead_sources_id,name',
            'adminApprover:id,name',
            'supportApprover:id,name',
            'requester:id,name'
        ]);

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $creditRequests = $query->orderBy('credit_request_id', 'DESC')->get();

        return response()->json([
            'status' => true,
            'data'   => $creditRequests
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'customer_id'   => ['required', 'exists:customers,customer_id'],
            'lead_source_id' => ['nullable', 'exists:lead_sources,lead_sources_id'],
            'credit_amount' => ['required', 'numeric', 'min:0.01'],
            'is_estimate'   => ['nullable', 'boolean'],
            'username'      => ['nullable', 'string', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'email'         => ['nullable', 'email', 'max:255'],
        ];

        [$cfRules, $cfAttributes] = $this->getCustomFieldsRules();
        $rules = array_merge($rules, $cfRules);

        $validated = $request->validate($rules, [], $cfAttributes);

        $customer = Customer::find($validated['customer_id']);
        $customFieldsData = $this->processCustomFieldsPayload($request->input('custom_fields', []));

        $creditRequest = CreditRequest::create([
            'customer_id'   => $validated['customer_id'],
            'lead_source_id' => $validated['lead_source_id'] ?? null,
            'credit_amount' => $validated['credit_amount'],
            'is_estimate'   => !empty($validated['is_estimate']) ? true : false,
            'username'      => $validated['username'] ?? $customer->name ?? null,
            'phone'         => $validated['phone'] ?? $customer->mobile ?? null,
            'email'         => $validated['email'] ?? $customer->email ?? null,
            'status'        => 'Pending Admin Approval',
            'requested_by'  => Auth::id(),
            'custom_fields' => $customFieldsData,
        ]);

        $msgType = !empty($validated['is_estimate']) ? 'Estimate Credit Request' : 'Credit Request';

        return response()->json([
            'status'  => true,
            'message' => "{$msgType} submitted successfully (Pending Admin Approval).",
            'data'    => $creditRequest
        ]);
    }

    public function edit($id)
    {
        $creditRequest = CreditRequest::forUser(Auth::user())->with(['customer', 'lead', 'requester'])->find($id);
        if (!$creditRequest) {
            return response()->json([
                'status'  => false,
                'message' => 'Credit request not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $creditRequest
        ]);
    }

    public function update(Request $request, $id)
    {
        $creditRequest = CreditRequest::forUser(Auth::user())->find($id);
        if (!$creditRequest) {
            return response()->json([
                'status'  => false,
                'message' => 'Credit request not found.'
            ], 404);
        }

        $rules = [
            'customer_id'   => ['required', 'exists:customers,customer_id'],
            'lead_source_id' => ['nullable', 'exists:lead_sources,lead_sources_id'],
            'credit_amount' => ['required', 'numeric', 'min:0.01'],
            'is_estimate'   => ['nullable', 'boolean'],
            'username'      => ['nullable', 'string', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'email'         => ['nullable', 'email', 'max:255'],
        ];

        [$cfRules, $cfAttributes] = $this->getCustomFieldsRules();
        $rules = array_merge($rules, $cfRules);

        $validated = $request->validate($rules, [], $cfAttributes);

        $customer = Customer::find($validated['customer_id']);
        $customFieldsData = $this->processCustomFieldsPayload($request->input('custom_fields', []));

        $creditRequest->update([
            'customer_id'   => $validated['customer_id'],
            'lead_source_id' => $validated['lead_source_id'] ?? null,
            'credit_amount' => $validated['credit_amount'],
            'is_estimate'   => !empty($validated['is_estimate']) ? true : false,
            'username'      => $validated['username'] ?? $customer->name ?? null,
            'phone'         => $validated['phone'] ?? $customer->mobile ?? null,
            'email'         => $validated['email'] ?? $customer->email ?? null,
            'custom_fields' => $customFieldsData,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Credit request updated successfully.',
            'data'    => $creditRequest
        ]);
    }

    /**
     * Requirement 9 & 11: Admin Approval -> Status: Approved by Admin & Forwarded to Support Team
     */
    public function approveAdmin(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasRole(['Super Admin', 'Admin', 'super admin', 'super-admin']) && !$user->can('credit-requests.approve_admin')) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action. Admin permission required.'], 403);
        }

        $creditRequest = CreditRequest::find($id);
        if (!$creditRequest) {
            return response()->json(['status' => false, 'message' => 'Credit request not found.'], 404);
        }

        $creditRequest->status            = 'Forwarded to Support';
        $creditRequest->admin_approved_by = Auth::id();
        $creditRequest->admin_approved_at = now();
        $creditRequest->admin_remarks     = $request->input('admin_remarks');
        $creditRequest->save();

        // First Notification: Send ONLY to approving Super Admin and Product Manager users
        $recipients = collect();

        if ($user) {
            $recipients->push($user);
        }

        $productManagers = User::whereHas('roles', function ($query) {
            $query->whereRaw('LOWER(name) IN (?, ?, ?)', ['product manager', 'product-manager', 'product_manager']);
        })->get();

        foreach ($productManagers as $pm) {
            $recipients->push($pm);
        }

        $recipients = $recipients->unique('id');

        foreach ($recipients as $recipient) {
            $recipient->notify(new CreditRequestApprovedByAdmin($creditRequest));
        }

        return response()->json([
            'status'  => true,
            'message' => 'Credit request approved by Admin and forwarded to Support Team.',
            'data'    => $creditRequest
        ]);
    }

    /**
     * Requirement 9: Support Team Approval -> Credit Added to Customer Balance
     */
    public function approveSupport(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasRole(['Super Admin', 'Admin', 'super admin', 'super-admin']) && !$user->can('credit-requests.approve_support')) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action. Support Team permission required.'], 403);
        }

        $creditRequest = CreditRequest::find($id);
        if (!$creditRequest) {
            return response()->json(['status' => false, 'message' => 'Credit request not found.'], 404);
        }

        if ($creditRequest->status === 'Credit Added') {
            return response()->json(['status' => false, 'message' => 'Credit has already been added for this request.'], 422);
        }

        $creditRequest->status              = 'Credit Added';
        $creditRequest->support_approved_by = Auth::id();
        $creditRequest->support_approved_at = now();
        $creditRequest->support_remarks     = $request->input('support_remarks');
        $creditRequest->save();

        // Add credit amount to Customer balance
        $customer = Customer::find($creditRequest->customer_id);
        if ($customer) {
            $customer->credit_balance = floatval($customer->credit_balance ?? 0) + floatval($creditRequest->credit_amount);
            $customer->save();
        }

        // Second Notification: Send ONLY to approving Product Manager and the handling Super Admin
        $recipients = collect();

        if ($user) {
            $recipients->push($user);
        }

        $adminId = $creditRequest->admin_approved_by ?: $creditRequest->requested_by ?: 1;
        $superAdminUser = User::find($adminId);
        if ($superAdminUser) {
            $recipients->push($superAdminUser);
        }

        $recipients = $recipients->unique('id');

        foreach ($recipients as $recipient) {
            $recipient->notify(new CreditRequestApprovedByProductManager($creditRequest));
        }

        return response()->json([
            'status'  => true,
            'message' => 'Support team approved credit request. Added ₹' . number_format($creditRequest->credit_amount, 2) . ' to customer credit balance.',
            'data'    => $creditRequest
        ]);
    }

    public function reject(Request $request, $id)
    {
        $creditRequest = CreditRequest::find($id);
        if (!$creditRequest) {
            return response()->json(['status' => false, 'message' => 'Credit request not found.'], 404);
        }

        $creditRequest->status        = 'Rejected';
        $creditRequest->admin_remarks = $request->input('remarks', 'Request rejected.');
        $creditRequest->save();

        return response()->json([
            'status'  => true,
            'message' => 'Credit request rejected.',
            'data'    => $creditRequest
        ]);
    }

    public function destroy($id)
    {
        $creditRequest = CreditRequest::forUser(Auth::user())->find($id);
        if (!$creditRequest) {
            return response()->json(['status' => false, 'message' => 'Credit request not found.'], 404);
        }

        $creditRequest->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Credit request deleted successfully.'
        ]);
    }

    private function getCustomFieldsRules()
    {
        $customFields = \App\Models\CreditRequestCustomField::where('status', 1)->get();
        $rules = [];
        $attributes = [];

        foreach ($customFields as $field) {
            $ruleKey = 'custom_fields.' . $field->field_name;
            $fieldRules = [];

            if ($field->is_required === 'Yes') {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            switch ($field->field_type) {
                case 'Number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'Date':
                    $fieldRules[] = 'date';
                    break;
                case 'Text':
                case 'Textarea':
                case 'Dropdown':
                default:
                    $fieldRules[] = 'string';
                    break;
            }

            $rules[$ruleKey] = $fieldRules;
            $attributes[$ruleKey] = $field->field_label;
        }

        return [$rules, $attributes];
    }

    private function processCustomFieldsPayload(array $customFieldsData = [])
    {
        $allFields = \App\Models\CreditRequestCustomField::where('status', 1)->get();
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
