<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStage;
use App\Models\LeadRequirement;
use App\Models\LostReason;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData();
        }

        $user = Auth::user();
        $isAdmin = $user->isAdmin();

        $data['customers']        = Customer::forUser($user)->where('status', 1)->orderBy('name')->get();
        $data['leadSources']      = LeadSource::where('status', 1)->orderBy('name')->get();
        $data['leadStages']       = LeadStage::where('status', 1)->orderBy('sort_order', 'ASC')->get();
        $data['leadRequirements'] = LeadRequirement::where('status', 1)->orderBy('name')->get();
        $data['lostReasons']      = LostReason::where('status', 1)->orderBy('reason')->get();

        if ($isAdmin) {
            $data['staffs'] = User::staffOnly()->orderBy('name')->get();
        } else {
            $data['staffs'] = User::where('id', $user->id)->get();
        }

        return view('leads.view', $data);
    }

    public function listData()
    {
        $user = Auth::user();

        $leads = Lead::forUser($user)->with([
            'customer:customer_id,name,mobile,email',
            'leadSource:lead_sources_id,name',
            'leadStage:lead_stage_id,name',
            'leadRequirement:lead_requirements_id,name',
            'lostReason:lost_reason_id,reason',
            'assignedUser:id,name',
            'creator:id,name'
        ])->orderBy('lead_id', 'DESC')->get();

        return response()->json([
            'status' => true,
            'data'   => $leads
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->isAdmin();

        $validated = $request->validate([
            'customer_id'         => ['required', 'exists:customers,customer_id'],
            'lead_title'          => ['required', 'string', 'max:255'],
            'lead_source_id'      => ['nullable', 'exists:lead_sources,lead_sources_id'],
            'lead_stage_id'       => ['nullable', 'exists:lead_stages,lead_stage_id'],
            'lead_requirement_id' => ['nullable', 'exists:lead_requirements,lead_requirements_id'],
            'assigned_to'         => ['nullable', 'exists:users,id'],
            'priority'            => ['required', 'in:low,medium,high,urgent'],
            'expected_amount'     => ['nullable', 'numeric', 'min:0'],
            'description'         => ['nullable', 'string'],
            'next_followup_date'  => ['nullable', 'date'],
            'status'              => ['required', 'in:0,1'],
            'lost_reason_id'      => ['nullable', 'exists:lost_reasons,lost_reason_id'],
        ]);

        if (!$isAdmin && empty($validated['assigned_to'])) {
            $validated['assigned_to'] = $user->id;
        }

        $validated['created_by'] = $user->id;

        $lead = Lead::create($validated);

        // Requirement 9: sales closed - credit request trigger
        $this->checkAndCreateSalesClosedCreditRequest($lead);

        return response()->json([
            'status'  => true,
            'message' => 'Lead created successfully.',
            'data'    => $lead
        ]);
    }

    public function edit($id)
    {
        $lead = Lead::forUser(Auth::user())->with(['customer', 'leadSource', 'leadStage', 'leadRequirement', 'lostReason', 'assignedUser'])->find($id);
        if (!$lead) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $lead
        ]);
    }

    public function update(Request $request, $id)
    {
        $lead = Lead::forUser(Auth::user())->find($id);
        if (!$lead) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead not found.'
            ], 404);
        }

        $validated = $request->validate([
            'customer_id'         => ['required', 'exists:customers,customer_id'],
            'lead_title'          => ['required', 'string', 'max:255'],
            'lead_source_id'      => ['nullable', 'exists:lead_sources,lead_sources_id'],
            'lead_stage_id'       => ['nullable', 'exists:lead_stages,lead_stage_id'],
            'lead_requirement_id' => ['nullable', 'exists:lead_requirements,lead_requirements_id'],
            'assigned_to'         => ['nullable', 'exists:users,id'],
            'priority'            => ['required', 'in:low,medium,high,urgent'],
            'expected_amount'     => ['nullable', 'numeric', 'min:0'],
            'description'         => ['nullable', 'string'],
            'next_followup_date'  => ['nullable', 'date'],
            'status'              => ['required', 'in:0,1'],
            'lost_reason_id'      => ['nullable', 'exists:lost_reasons,lost_reason_id'],
        ]);

        $lead->update($validated);

        // Requirement 9: sales closed - credit request trigger
        $this->checkAndCreateSalesClosedCreditRequest($lead);

        return response()->json([
            'status'  => true,
            'message' => 'Lead updated successfully.',
            'data'    => $lead
        ]);
    }

    /**
     * Requirement 9: When lead stage is Sales Closed, create a credit request
     */
    private function checkAndCreateSalesClosedCreditRequest(Lead $lead)
    {
        if (!$lead->lead_stage_id) {
            return;
        }

        $stage = LeadStage::find($lead->lead_stage_id);
        if ($stage && (str_contains(strtolower($stage->name), 'closed') || str_contains(strtolower($stage->name), 'won') || str_contains(strtolower($stage->name), 'sale'))) {
            // Check if credit request already exists for this lead
            $exists = \App\Models\CreditRequest::where('lead_id', $lead->lead_id)->first();
            if (!$exists) {
                $customer = Customer::find($lead->customer_id);
                \App\Models\CreditRequest::create([
                    'lead_id'       => $lead->lead_id,
                    'customer_id'   => $lead->customer_id,
                    'username'      => $customer->name ?? null,
                    'phone'         => $customer->mobile ?? null,
                    'email'         => $customer->email ?? null,
                    'credit_amount' => $lead->expected_amount ?? 0.00,
                    'is_estimate'   => false,
                    'status'        => 'Pending Admin Approval',
                    'requested_by'  => Auth::id(),
                ]);
            }
        }
    }

    public function destroy($id)
    {
        $lead = Lead::forUser(Auth::user())->find($id);
        if (!$lead) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead not found.'
            ], 404);
        }

        $lead->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Lead deleted successfully.'
        ]);
    }

    public function changeStatus(Request $request, $id)
    {
        $lead = Lead::forUser(Auth::user())->find($id);
        if (!$lead) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead not found.'
            ], 404);
        }

        $lead->status = $lead->status == 1 ? 0 : 1;
        $lead->save();

        return response()->json([
            'status'  => true,
            'message' => 'Lead status updated successfully.',
            'new_status' => $lead->status
        ]);
    }
}
