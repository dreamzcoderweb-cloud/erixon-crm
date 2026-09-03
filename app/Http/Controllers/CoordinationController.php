<?php

namespace App\Http\Controllers;

use App\Models\Coordination;
use App\Models\CoordinationJoiningStaff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoordinationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData();
        }

        $staffList = User::orderBy('name', 'asc')->get();

        return view('coordinations.view', compact('staffList'));
    }

    public function listData()
    {
        $user = Auth::user();
        $coordinations = Coordination::forUser($user)
            ->with([
                'staff:id,name,email',
                'creator:id,name',
                'joiningStaff:id,name,email'
            ])
            ->orderBy('coordination_id', 'DESC')
            ->get();

        $data = $coordinations->map(function ($c) use ($user) {
            $joiningStaffList = $c->joiningStaff;

            if ($joiningStaffList->isEmpty()) {
                // Legacy fallback: default joining staff to staff_id and created_by
                $defaultIds = array_values(array_unique(array_filter([$c->staff_id, $c->created_by])));
                $defaultUsers = User::whereIn('id', $defaultIds)->get();
                $joiningStaffData = $defaultUsers->map(function ($u) {
                    return [
                        'id'        => $u->id,
                        'name'      => $u->name,
                        'email'     => $u->email,
                        'status'    => 'Pending',
                        'joined_at' => null,
                    ];
                });
            } else {
                $joiningStaffData = $joiningStaffList->map(function ($u) {
                    return [
                        'id'        => $u->id,
                        'name'      => $u->name,
                        'email'     => $u->email,
                        'status'    => $u->pivot->status ?? 'Pending',
                        'joined_at' => $u->pivot->joined_at ? strval($u->pivot->joined_at) : null,
                    ];
                });
            }

            $totalJoining = $joiningStaffData->count();
            $joinedStaff = $joiningStaffData->where('status', 'Joined')->values();
            $pendingStaff = $joiningStaffData->where('status', 'Pending')->values();

            $userJoinedStatus = 'Pending';
            if ($user) {
                $userEntry = $joiningStaffData->firstWhere('id', $user->id);
                if ($userEntry) {
                    $userJoinedStatus = $userEntry['status'];
                }
            }

            return [
                'coordination_id'    => $c->coordination_id,
                'staff_id'           => $c->staff_id,
                'staff'              => $c->staff,
                'link'               => $c->link,
                'created_by'         => $c->created_by,
                'creator'            => $c->creator,
                'created_at'         => $c->created_at,
                'joining_staff'      => $joiningStaffData,
                'total_joining'      => $totalJoining,
                'joined_count'       => $joinedStaff->count(),
                'pending_count'      => $pendingStaff->count(),
                'joined_staff'       => $joinedStaff,
                'pending_staff'      => $pendingStaff,
                'user_joined_status' => $userJoinedStatus,
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => $data
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id'          => ['required', 'exists:users,id'],
            'link'              => ['required', 'string', 'max:2048'],
            'joining_staff_ids'   => ['nullable', 'array'],
            'joining_staff_ids.*' => ['exists:users,id'],
        ]);

        $validated['created_by'] = Auth::id();

        $coordination = Coordination::create([
            'staff_id'   => $validated['staff_id'],
            'link'       => $validated['link'],
            'created_by' => $validated['created_by'],
        ]);

        // Ensure Created Staff (staff_id and created_by) is automatically included in Joining Staff
        $joiningIds = $validated['joining_staff_ids'] ?? [];
        $joiningIds[] = intval($validated['staff_id']);
        if (!empty($validated['created_by'])) {
            $joiningIds[] = intval($validated['created_by']);
        }
        $joiningIds = array_values(array_unique(array_filter(array_map('intval', $joiningIds))));

        $syncData = [];
        foreach ($joiningIds as $uid) {
            $syncData[$uid] = ['status' => 'Pending'];
        }
        $coordination->joiningStaff()->sync($syncData);

        $coordination->load(['staff:id,name', 'creator:id,name', 'joiningStaff:id,name']);

        return response()->json([
            'status'  => true,
            'message' => 'Coordination record created successfully.',
            'data'    => $coordination
        ]);
    }

    public function edit($id)
    {
        $coordination = Coordination::forUser(Auth::user())
            ->with(['staff:id,name', 'creator:id,name', 'joiningStaff:id,name'])
            ->find($id);

        if (!$coordination) {
            return response()->json([
                'status'  => false,
                'message' => 'Coordination record not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $coordination
        ]);
    }

    public function update(Request $request, $id)
    {
        $coordination = Coordination::forUser(Auth::user())->find($id);
        if (!$coordination) {
            return response()->json([
                'status'  => false,
                'message' => 'Coordination record not found.'
            ], 404);
        }

        $validated = $request->validate([
            'staff_id'          => ['required', 'exists:users,id'],
            'link'              => ['required', 'string', 'max:2048'],
            'joining_staff_ids'   => ['nullable', 'array'],
            'joining_staff_ids.*' => ['exists:users,id'],
        ]);

        $coordination->update([
            'staff_id' => $validated['staff_id'],
            'link'     => $validated['link'],
        ]);

        // Ensure Created Staff is included
        $joiningIds = $validated['joining_staff_ids'] ?? [];
        $joiningIds[] = intval($validated['staff_id']);
        if (!empty($coordination->created_by)) {
            $joiningIds[] = intval($coordination->created_by);
        }
        $joiningIds = array_values(array_unique(array_filter(array_map('intval', $joiningIds))));

        // Preserve existing 'Joined' statuses for staff members
        $existingPivot = CoordinationJoiningStaff::where('coordination_id', $coordination->coordination_id)
            ->get()
            ->keyBy('user_id');

        $syncData = [];
        foreach ($joiningIds as $uid) {
            if (isset($existingPivot[$uid])) {
                $syncData[$uid] = [
                    'status'    => $existingPivot[$uid]->status,
                    'joined_at' => $existingPivot[$uid]->joined_at,
                ];
            } else {
                $syncData[$uid] = [
                    'status'    => 'Pending',
                    'joined_at' => null,
                ];
            }
        }
        $coordination->joiningStaff()->sync($syncData);
        $coordination->load(['staff:id,name', 'creator:id,name', 'joiningStaff:id,name']);

        return response()->json([
            'status'  => true,
            'message' => 'Coordination record updated successfully.',
            'data'    => $coordination
        ]);
    }

    public function toggleJoinStatus(Request $request, $id)
    {
        $user = Auth::user();
        $coordination = Coordination::forUser($user)->find($id);

        if (!$coordination) {
            return response()->json([
                'status'  => false,
                'message' => 'Coordination record not found or access denied.'
            ], 404);
        }

        $pivot = CoordinationJoiningStaff::where('coordination_id', $coordination->coordination_id)
            ->where('user_id', $user->id)
            ->first();

        $forceJoin = $request->boolean('force_join', false);

        if (!$pivot) {
            $pivot = CoordinationJoiningStaff::create([
                'coordination_id' => $coordination->coordination_id,
                'user_id'         => $user->id,
                'status'          => 'Joined',
                'joined_at'       => now(),
            ]);
            $newStatus = 'Joined';
        } else {
            if ($forceJoin) {
                $newStatus = 'Joined';
            } else {
                $newStatus = ($pivot->status === 'Joined') ? 'Pending' : 'Joined';
            }

            $pivot->update([
                'status'    => $newStatus,
                'joined_at' => ($newStatus === 'Joined') ? ($pivot->joined_at ?? now()) : null,
            ]);
        }

        $msg = ($newStatus === 'Joined') ? 'You have joined this coordination.' : 'Status updated to pending.';

        return response()->json([
            'status'     => true,
            'message'    => $msg,
            'new_status' => $newStatus,
        ]);
    }

    public function destroy($id)
    {
        $coordination = Coordination::forUser(Auth::user())->find($id);
        if (!$coordination) {
            return response()->json([
                'status'  => false,
                'message' => 'Coordination record not found.'
            ], 404);
        }

        $coordination->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Coordination record deleted successfully.'
        ]);
    }
}
