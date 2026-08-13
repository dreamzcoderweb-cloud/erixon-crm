<?php

namespace App\Http\Controllers;

use App\Models\LeadSource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadSourceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData();
        }

        return view('lead_sources.view');
    }

    public function listData()
    {
        $sources = LeadSource::orderBy('lead_sources_id', 'DESC')->get();

        return response()->json([
            'status' => true,
            'data'   => $sources
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255', Rule::unique('lead_sources', 'name')->withoutTrashed()],
            'status' => ['required', 'in:0,1'],
        ]);

        $source = LeadSource::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Lead source created successfully.',
            'data'    => $source
        ]);
    }

    public function edit($id)
    {
        $source = LeadSource::find($id);
        if (!$source) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead source not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $source
        ]);
    }

    public function update(Request $request, $id)
    {
        $source = LeadSource::find($id);
        if (!$source) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead source not found.'
            ], 404);
        }

        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255', Rule::unique('lead_sources', 'name')->ignore($source->lead_sources_id, 'lead_sources_id')->withoutTrashed()],
            'status' => ['required', 'in:0,1'],
        ]);

        $source->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Lead source updated successfully.',
            'data'    => $source
        ]);
    }

    public function destroy($id)
    {
        $source = LeadSource::find($id);
        if (!$source) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead source not found.'
            ], 404);
        }

        $source->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Lead source deleted successfully.'
        ]);
    }

    public function changeStatus(Request $request, $id)
    {
        $source = LeadSource::find($id);
        if (!$source) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead source not found.'
            ], 404);
        }

        $source->status = $source->status == 1 ? 0 : 1;
        $source->save();

        return response()->json([
            'status'  => true,
            'message' => 'Lead source status updated successfully.',
            'new_status' => $source->status
        ]);
    }
}
