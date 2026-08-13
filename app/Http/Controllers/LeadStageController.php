<?php

namespace App\Http\Controllers;

use App\Models\LeadStage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadStageController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData();
        }

        return view('lead_stages.view');
    }

    public function listData()
    {
        $stages = LeadStage::orderBy('sort_order', 'ASC')
            ->orderBy('lead_stage_id', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $stages
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255', Rule::unique('lead_stages', 'name')->withoutTrashed()],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status'     => ['required', 'in:0,1'],
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $stage = LeadStage::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Lead stage created successfully.',
            'data'    => $stage
        ]);
    }

    public function edit($id)
    {
        $stage = LeadStage::find($id);
        if (!$stage) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead stage not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $stage
        ]);
    }

    public function update(Request $request, $id)
    {
        $stage = LeadStage::find($id);
        if (!$stage) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead stage not found.'
            ], 404);
        }

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255', Rule::unique('lead_stages', 'name')->ignore($stage->lead_stage_id, 'lead_stage_id')->withoutTrashed()],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status'     => ['required', 'in:0,1'],
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $stage->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Lead stage updated successfully.',
            'data'    => $stage
        ]);
    }

    public function destroy($id)
    {
        $stage = LeadStage::find($id);
        if (!$stage) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead stage not found.'
            ], 404);
        }

        $stage->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Lead stage deleted successfully.'
        ]);
    }

    public function changeStatus(Request $request, $id)
    {
        $stage = LeadStage::find($id);
        if (!$stage) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead stage not found.'
            ], 404);
        }

        $stage->status = $stage->status == 1 ? 0 : 1;
        $stage->save();

        return response()->json([
            'status'  => true,
            'message' => 'Lead stage status updated successfully.',
            'new_status' => $stage->status
        ]);
    }
}
