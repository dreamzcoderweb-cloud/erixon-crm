<?php

namespace App\Http\Controllers;

use App\Models\CreditRequest;
use App\Models\Customer;
use App\Models\Lead;
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
        $data['leads']     = Lead::forUser($user)->with('customer')->orderBy('lead_id', 'DESC')->get();
        $data['customers'] = Customer::forUser($user)->where('status', 1)->orderBy('name')->get();

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
        $validated = $request->validate([
            'customer_id'   => ['required', 'exists:customers,customer_id'],
            'lead_id'       => ['nullable', 'exists:leads,lead_id'],
            'credit_amount' => ['required', 'numeric', 'min:0.01'],
            'is_estimate'   => ['nullable', 'boolean'],
            'username'      => ['nullable', 'string', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'email'         => ['nullable', 'email', 'max:255'],
        ]);

        $customer = Customer::find($validated['customer_id']);

        $creditRequest = CreditRequest::create([
            'customer_id'   => $validated['customer_id'],
            'lead_id'       => $validated['lead_id'] ?? null,
            'credit_amount' => $validated['credit_amount'],
            'is_estimate'   => !empty($validated['is_estimate']) ? true : false,
            'username'      => $validated['username'] ?? $customer->name ?? null,
            'phone'         => $validated['phone'] ?? $customer->mobile ?? null,
            'email'         => $validated['email'] ?? $customer->email ?? null,
            'status'        => 'Pending Admin Approval',
            'requested_by'  => Auth::id(),
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

        $validated = $request->validate([
            'customer_id'   => ['required', 'exists:customers,customer_id'],
            'lead_id'       => ['nullable', 'exists:leads,lead_id'],
            'credit_amount' => ['required', 'numeric', 'min:0.01'],
            'is_estimate'   => ['nullable', 'boolean'],
            'username'      => ['nullable', 'string', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'email'         => ['nullable', 'email', 'max:255'],
        ]);

        $customer = Customer::find($validated['customer_id']);

        $creditRequest->update([
            'customer_id'   => $validated['customer_id'],
            'lead_id'       => $validated['lead_id'] ?? null,
            'credit_amount' => $validated['credit_amount'],
            'is_estimate'   => !empty($validated['is_estimate']) ? true : false,
            'username'      => $validated['username'] ?? $customer->name ?? null,
            'phone'         => $validated['phone'] ?? $customer->mobile ?? null,
            'email'         => $validated['email'] ?? $customer->email ?? null,
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
}
