<?php

namespace App\Http\Controllers;

use App\Models\LeadRequirement;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadRequirementController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData();
        }

        return view('lead_requirements.view');
    }

    public function listData()
    {
        $requirements = LeadRequirement::orderBy('lead_requirements_id', 'DESC')->get();

        return response()->json([
            'status' => true,
            'data'   => $requirements
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255', Rule::unique('lead_requirements', 'name')->withoutTrashed()],
            'status' => ['required', 'in:0,1'],
        ]);

        $requirement = LeadRequirement::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Lead requirement created successfully.',
            'data'    => $requirement
        ]);
    }

    public function edit($id)
    {
        $requirement = LeadRequirement::find($id);
        if (!$requirement) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead requirement not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $requirement
        ]);
    }

    public function update(Request $request, $id)
    {
        $requirement = LeadRequirement::find($id);
        if (!$requirement) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead requirement not found.'
            ], 404);
        }

        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255', Rule::unique('lead_requirements', 'name')->ignore($requirement->lead_requirements_id, 'lead_requirements_id')->withoutTrashed()],
            'status' => ['required', 'in:0,1'],
        ]);

        $requirement->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Lead requirement updated successfully.',
            'data'    => $requirement
        ]);
    }

    public function destroy($id)
    {
        $requirement = LeadRequirement::find($id);
        if (!$requirement) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead requirement not found.'
            ], 404);
        }

        $requirement->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Lead requirement deleted successfully.'
        ]);
    }

    public function changeStatus(Request $request, $id)
    {
        $requirement = LeadRequirement::find($id);
        if (!$requirement) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead requirement not found.'
            ], 404);
        }

        $requirement->status = $requirement->status == 1 ? 0 : 1;
        $requirement->save();

        return response()->json([
            'status'  => true,
            'message' => 'Lead requirement status updated successfully.',
            'new_status' => $requirement->status
        ]);
    }
}
