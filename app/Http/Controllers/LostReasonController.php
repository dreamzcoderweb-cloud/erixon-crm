<?php

namespace App\Http\Controllers;

use App\Models\LostReason;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LostReasonController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData();
        }

        return view('lost_reasons.view');
    }

    public function listData()
    {
        $reasons = LostReason::orderBy('lost_reason_id', 'DESC')->get();

        return response()->json([
            'status' => true,
            'data'   => $reasons
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255', Rule::unique('lost_reasons', 'reason')->withoutTrashed()],
            'status' => ['required', 'in:0,1'],
        ]);

        $reason = LostReason::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Lost reason created successfully.',
            'data'    => $reason
        ]);
    }

    public function edit($id)
    {
        $reason = LostReason::find($id);
        if (!$reason) {
            return response()->json([
                'status'  => false,
                'message' => 'Lost reason not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $reason
        ]);
    }

    public function update(Request $request, $id)
    {
        $reason = LostReason::find($id);
        if (!$reason) {
            return response()->json([
                'status'  => false,
                'message' => 'Lost reason not found.'
            ], 404);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255', Rule::unique('lost_reasons', 'reason')->ignore($reason->lost_reason_id, 'lost_reason_id')->withoutTrashed()],
            'status' => ['required', 'in:0,1'],
        ]);

        $reason->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Lost reason updated successfully.',
            'data'    => $reason
        ]);
    }

    public function destroy($id)
    {
        $reason = LostReason::find($id);
        if (!$reason) {
            return response()->json([
                'status'  => false,
                'message' => 'Lost reason not found.'
            ], 404);
        }

        $reason->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Lost reason deleted successfully.'
        ]);
    }

    public function changeStatus(Request $request, $id)
    {
        $reason = LostReason::find($id);
        if (!$reason) {
            return response()->json([
                'status'  => false,
                'message' => 'Lost reason not found.'
            ], 404);
        }

        $reason->status = $reason->status == 1 ? 0 : 1;
        $reason->save();

        return response()->json([
            'status'  => true,
            'message' => 'Lost reason status updated successfully.',
            'new_status' => $reason->status
        ]);
    }
}
