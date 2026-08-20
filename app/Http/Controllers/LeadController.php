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

        $data['customers']        = Customer::where('status', 1)->orderBy('name')->get();
        $data['leadSources']      = LeadSource::where('status', 1)->orderBy('name')->get();
        $data['leadStages']       = LeadStage::where('status', 1)->orderBy('sort_order', 'ASC')->get();
        $data['leadRequirements'] = LeadRequirement::where('status', 1)->orderBy('name')->get();
        $data['lostReasons']      = LostReason::where('status', 1)->orderBy('reason')->get();
        $data['staffs']           = User::staffOnly()->orderBy('name')->get();

        return view('leads.view', $data);
    }

    public function listData()
    {
        $leads = Lead::with([
            'customer:customer_id,name,mobile,email',
            'leadSource:lead_sources_id,name',
            'leadStage:lead_stage_id,name',
            'leadRequirement:lead_requirements_id,name',
            'lostReason:lost_reason_id,reason',
            'assignedUser:id,name',
            'creator:id,name'
        ])
        ->orderBy('lead_id', 'DESC')
        ->get();

        return response()->json([
            'status' => true,
            'data'   => $leads
        ]);
    }

    public function store(Request $request)
    {
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

        $validated['created_by'] = Auth::id();

        $lead = Lead::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Lead created successfully.',
            'data'    => $lead
        ]);
    }

    public function edit($id)
    {
        $lead = Lead::with(['customer', 'leadSource', 'leadStage', 'leadRequirement', 'lostReason', 'assignedUser'])->find($id);
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
        $lead = Lead::find($id);
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

        return response()->json([
            'status'  => true,
            'message' => 'Lead updated successfully.',
            'data'    => $lead
        ]);
    }

    public function destroy($id)
    {
        $lead = Lead::find($id);
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
        $lead = Lead::find($id);
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
