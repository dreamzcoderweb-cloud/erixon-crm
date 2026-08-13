<?php

namespace App\Http\Controllers;

use App\Models\Followup;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowupController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData();
        }

        $data['leads']  = Lead::with('customer')->orderBy('lead_id', 'DESC')->get();
        $data['staffs'] = User::orderBy('name')->get();

        return view('followups.view', $data);
    }

    public function listData()
    {
        $followups = Followup::with([
            'lead:lead_id,lead_title,customer_id',
            'lead.customer:customer_id,name,mobile',
            'forwardToUser:id,name',
            'creator:id,name'
        ])
        ->orderBy('followups_id', 'DESC')
        ->get();

        return response()->json([
            'status' => true,
            'data'   => $followups
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id'            => ['required', 'exists:leads,lead_id'],
            'followup_type'      => ['required', 'string', 'max:100'],
            'remarks'            => ['nullable', 'string'],
            'next_followup_date' => ['nullable', 'date'],
            'followup_status'    => ['required', 'in:Pending,Completed,Cancelled'],
            'forward_to'         => ['nullable', 'exists:users,id'],
        ]);

        $validated['created_by'] = Auth::id();

        $followup = Followup::create($validated);

        // Update the next_followup_date on the parent lead if provided
        if (!empty($validated['next_followup_date'])) {
            Lead::where('lead_id', $validated['lead_id'])->update([
                'next_followup_date' => $validated['next_followup_date']
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Follow-up created successfully.',
            'data'    => $followup
        ]);
    }

    public function edit($id)
    {
        $followup = Followup::with(['lead.customer', 'forwardToUser', 'creator'])->find($id);
        if (!$followup) {
            return response()->json([
                'status'  => false,
                'message' => 'Follow-up not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $followup
        ]);
    }

    public function update(Request $request, $id)
    {
        $followup = Followup::find($id);
        if (!$followup) {
            return response()->json([
                'status'  => false,
                'message' => 'Follow-up not found.'
            ], 404);
        }

        $validated = $request->validate([
            'lead_id'            => ['required', 'exists:leads,lead_id'],
            'followup_type'      => ['required', 'string', 'max:100'],
            'remarks'            => ['nullable', 'string'],
            'next_followup_date' => ['nullable', 'date'],
            'followup_status'    => ['required', 'in:Pending,Completed,Cancelled'],
            'forward_to'         => ['nullable', 'exists:users,id'],
        ]);

        $followup->update($validated);

        if (!empty($validated['next_followup_date'])) {
            Lead::where('lead_id', $validated['lead_id'])->update([
                'next_followup_date' => $validated['next_followup_date']
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Follow-up updated successfully.',
            'data'    => $followup
        ]);
    }

    public function destroy($id)
    {
        $followup = Followup::find($id);
        if (!$followup) {
            return response()->json([
                'status'  => false,
                'message' => 'Follow-up not found.'
            ], 404);
        }

        $followup->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Follow-up deleted successfully.'
        ]);
    }

    public function changeStatus(Request $request, $id)
    {
        $followup = Followup::find($id);
        if (!$followup) {
            return response()->json([
                'status'  => false,
                'message' => 'Follow-up not found.'
            ], 404);
        }

        $status = $request->input('followup_status');
        if (in_array($status, ['Pending', 'Completed', 'Cancelled'])) {
            $followup->followup_status = $status;
        } else {
            // Toggle between Pending and Completed
            $followup->followup_status = $followup->followup_status === 'Completed' ? 'Pending' : 'Completed';
        }
        $followup->save();

        return response()->json([
            'status'     => true,
            'message'    => 'Follow-up status updated successfully.',
            'new_status' => $followup->followup_status
        ]);
    }
}
