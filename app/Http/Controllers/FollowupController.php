<?php

namespace App\Http\Controllers;

use App\Models\Followup;
use App\Models\FollowupReassignment;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FollowupController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData($request);
        }

        $user    = Auth::user();
        $isAdmin = $user && $user->isAdmin();

        $data['leads']       = Lead::forUser($user)->with('customer')->orderBy('lead_id', 'DESC')->get();
        $data['customers']   = \App\Models\Customer::forUser($user)->where('status', 1)->orderBy('name')->get();
        $data['leadSources'] = \App\Models\LeadSource::where('status', 1)->orderBy('name')->get();
        $data['allStaffs']   = User::orderBy('name')->get();

        if ($isAdmin) {
            $data['staffs']          = User::staffOnly()->orderBy('name')->get();
            $data['availableStaffs'] = User::staffOnly()->availableForAssignment()->orderBy('name')->get();
        } else {
            $data['staffs']          = User::where('id', $user->id)->get();
            $data['availableStaffs'] = User::where('id', $user->id)->get();
        }

        return view('followups.view', $data);
    }

    public function listData(Request $request)
    {
        $user   = Auth::user();
        $today  = \Carbon\Carbon::today()->toDateString();

        $filterType = $request->input('filter_type', 'all');
        $staffId    = $request->input('staff_id', $request->input('created_by'));
        $customDate = $request->input('date');
        $month      = $request->input('month');
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');

        $query = Followup::forUser($user)->with([
            'lead:lead_id,lead_title,customer_id',
            'lead.customer:customer_id,name,mobile',
            'forwardToUser:id,name,is_on_leave',
            'creator:id,name'
        ]);

        if (!empty($staffId)) {
            $query->where(function ($q) use ($staffId) {
                $q->where('forward_to', $staffId)
                  ->orWhere('created_by', $staffId)
                  ->orWhereHas('lead', function ($lq) use ($staffId) {
                      $lq->where('assigned_to', $staffId)
                        ->orWhere('created_by', $staffId);
                  });
            });
        }

        if ($request->filled('lead_id')) {
            $query->where('lead_id', $request->input('lead_id'));
        }

        if ($request->filled('customer_id')) {
            $query->whereHas('lead', function ($lq) use ($request) {
                $lq->where('customer_id', $request->input('customer_id'));
            });
        }

        if ($request->filled('lead_source_id')) {
            $query->whereHas('lead', function ($lq) use ($request) {
                $lq->where('lead_source_id', $request->input('lead_source_id'));
            });
        }

        if ($request->filled('status') && $request->input('status') !== '') {
            $query->where('followup_status', $request->input('status'));
        }

        // Apply date / period filtering
        if ($filterType === 'today') {
            $query->whereDate('next_followup_date', '=', $today);
        } elseif ($filterType === 'tomorrow' || $filterType === 'upcoming') {
            $query->whereDate('next_followup_date', '>', $today);
        } elseif ($filterType === 'overdue') {
            $query->whereDate('next_followup_date', '<', $today)
                  ->where('followup_status', 'Pending');
        } elseif ($filterType === 'daily' && !empty($customDate)) {
            $query->whereDate('next_followup_date', '=', $customDate);
        } elseif ($filterType === 'weekly') {
            $refDate = !empty($startDate) ? \Carbon\Carbon::parse($startDate) : \Carbon\Carbon::today();
            $query->whereBetween('next_followup_date', [
                $refDate->copy()->startOfWeek(),
                $refDate->copy()->endOfWeek(),
            ]);
        } elseif ($filterType === 'monthly' && !empty($month)) {
            [$year, $selectedMonth] = array_pad(explode('-', $month), 2, null);
            $query->whereYear('next_followup_date', $year ?: date('Y'))
                ->whereMonth('next_followup_date', $selectedMonth ?: date('m'));
        } elseif ($filterType === 'custom') {
            if (!empty($startDate)) {
                $query->whereDate('next_followup_date', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $query->whereDate('next_followup_date', '<=', $endDate);
            }
        }

        $followups = $query->orderBy('next_followup_date', 'ASC')->get();

        // Calculate counts for tab badges and KPI cards
        $baseCountQuery = Followup::query()->forUser($user);
        if (!empty($staffId)) {
            $baseCountQuery->where(function ($q) use ($staffId) {
                $q->where('forward_to', $staffId)
                  ->orWhere('created_by', $staffId)
                  ->orWhereHas('lead', function ($lq) use ($staffId) {
                      $lq->where('assigned_to', $staffId)
                        ->orWhere('created_by', $staffId);
                  });
            });
        }
        if ($request->filled('lead_id')) {
            $baseCountQuery->where('lead_id', $request->input('lead_id'));
        }
        if ($request->filled('customer_id')) {
            $baseCountQuery->whereHas('lead', function ($lq) use ($request) {
                $lq->where('customer_id', $request->input('customer_id'));
            });
        }
        if ($request->filled('lead_source_id')) {
            $baseCountQuery->whereHas('lead', function ($lq) use ($request) {
                $lq->where('lead_source_id', $request->input('lead_source_id'));
            });
        }
        if ($request->filled('status') && $request->input('status') !== '') {
            $baseCountQuery->where('followup_status', $request->input('status'));
        }

        $counts = [
            'all'      => (clone $baseCountQuery)->count(),
            'today'    => (clone $baseCountQuery)->whereDate('next_followup_date', '=', $today)->count(),
            'upcoming' => (clone $baseCountQuery)->whereDate('next_followup_date', '>', $today)->count(),
            'overdue'  => (clone $baseCountQuery)->whereDate('next_followup_date', '<', $today)->where('followup_status', 'Pending')->count(),
        ];

        $totalFollowups    = (clone $baseCountQuery)->count();
        $staffCreatedCount = (clone $baseCountQuery)->whereNotNull('created_by')->count();

        return response()->json([
            'status'              => true,
            'counts'              => $counts,
            'total_followups'     => $totalFollowups,
            'staff_created_count' => $staffCreatedCount,
            'data'                => $followups
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'lead_id'            => ['required', 'exists:leads,lead_id'],
            'followup_type'      => ['required', 'string', 'max:100'],
            'duration'           => ['nullable', 'string', 'max:100'],
            'remarks'            => ['nullable', 'string'],
            'next_followup_date' => ['nullable', 'date'],
            'followup_status'    => ['required', 'in:Pending,Completed,Cancelled'],
            'forward_to'         => ['nullable', 'exists:users,id'],
        ];

        if ($request->input('followup_type') === 'Call') {
            $rules['duration'] = ['required', 'string', 'max:100'];
        }

        $validated = $request->validate($rules, [
            'duration.required' => 'Duration is required when Follow-up Type is Call.'
        ]);

        if (!empty($validated['forward_to'])) {
            $forwardUser = User::find($validated['forward_to']);
            if ($forwardUser && $forwardUser->is_on_leave) {
                return response()->json([
                    'status' => false,
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        'forward_to' => ['Selected staff member is currently on leave.']
                    ]
                ], 422);
            }
        }

        if ($validated['followup_type'] !== 'Call') {
            $validated['duration'] = null;
        }

        $validated['created_by'] = Auth::id();

        $followup = Followup::create($validated);

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
        $followup = Followup::forUser(Auth::user())->with(['lead.customer', 'forwardToUser', 'creator'])->find($id);
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
        $followup = Followup::forUser(Auth::user())->find($id);
        if (!$followup) {
            return response()->json([
                'status'  => false,
                'message' => 'Follow-up not found.'
            ], 404);
        }

        $rules = [
            'lead_id'            => ['required', 'exists:leads,lead_id'],
            'followup_type'      => ['required', 'string', 'max:100'],
            'duration'           => ['nullable', 'string', 'max:100'],
            'remarks'            => ['nullable', 'string'],
            'next_followup_date' => ['nullable', 'date'],
            'followup_status'    => ['required', 'in:Pending,Completed,Cancelled'],
            'forward_to'         => ['nullable', 'exists:users,id'],
        ];

        if ($request->input('followup_type') === 'Call') {
            $rules['duration'] = ['required', 'string', 'max:100'];
        }

        $validated = $request->validate($rules, [
            'duration.required' => 'Duration is required when Follow-up Type is Call.'
        ]);

        if (!empty($validated['forward_to'])) {
            $forwardUser = User::find($validated['forward_to']);
            if ($forwardUser && $forwardUser->is_on_leave) {
                return response()->json([
                    'status' => false,
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        'forward_to' => ['Selected staff member is currently on leave.']
                    ]
                ], 422);
            }
        }

        if ($validated['followup_type'] !== 'Call') {
            $validated['duration'] = null;
        }

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
        $followup = Followup::forUser(Auth::user())->find($id);
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
        $followup = Followup::forUser(Auth::user())->find($id);
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
            $followup->followup_status = $followup->followup_status === 'Completed' ? 'Pending' : 'Completed';
        }
        $followup->save();

        return response()->json([
            'status'     => true,
            'message'    => 'Follow-up status updated successfully.',
            'new_status' => $followup->followup_status
        ]);
    }

    /**
     * Requirement 1: Get today's pending follow-ups for logged-in staff member
     */
    public function getTodayReminders(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'count' => 0, 'data' => []]);
        }

        $userId = $user->id;
        $today  = \Carbon\Carbon::today()->toDateString();

        // The Today Reminder popup must be strictly user-specific for the logged-in user
        $followups = Followup::with([
            'lead:lead_id,lead_title,customer_id,assigned_to',
            'lead.customer:customer_id,name,mobile,email',
            'forwardToUser:id,name',
            'creator:id,name'
        ])
        ->where('followup_status', 'Pending')
        ->whereDate('next_followup_date', '=', $today)
        ->where(function ($query) use ($userId) {
            $query->where('forward_to', $userId)
                  ->orWhere(function ($q2) use ($userId) {
                      $q2->whereNull('forward_to')
                         ->where('created_by', $userId);
                  })
                  ->orWhereHas('lead', function ($q3) use ($userId) {
                      $q3->where('assigned_to', $userId);
                  });
        })
        ->orderBy('next_followup_date', 'ASC')
        ->get();

        return response()->json([
            'status' => true,
            'count'  => $followups->count(),
            'data'   => $followups
        ]);
    }

    /**
     * Requirement 2: Reassign a follow-up assigned to staff on leave
     */
    public function reassign(Request $request, $id)
    {
        if (!Auth::user()->can('followups.reassign') && !Auth::user()->hasRole('Super Admin')) {
            return response()->json([
                'status'  => false,
                'message' => 'You do not have permission to reassign follow-ups.'
            ], 403);
        }

        $followup = Followup::find($id);
        if (!$followup) {
            return response()->json([
                'status'  => false,
                'message' => 'Follow-up not found.'
            ], 404);
        }

        $request->validate([
            'new_staff_id' => ['required', 'exists:users,id'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ]);

        $newStaff = User::find($request->new_staff_id);
        if (!$newStaff || $newStaff->is_on_leave) {
            return response()->json([
                'status'  => false,
                'message' => 'Cannot reassign follow-up to a staff member who is currently on leave.'
            ], 422);
        }

        $previousStaffId = $followup->forward_to ?? $followup->created_by ?? Auth::id();

        if ((int)$previousStaffId === (int)$newStaff->id) {
            return response()->json([
                'status'  => false,
                'message' => 'The follow-up is already assigned to this staff member.'
            ], 422);
        }

        // Store reassignment history
        FollowupReassignment::create([
            'followup_id'       => $followup->followups_id,
            'previous_staff_id' => $previousStaffId,
            'new_staff_id'      => $newStaff->id,
            'reassigned_by'     => Auth::id(),
            'notes'             => $request->notes ?? 'Reassigned due to staff leave',
        ]);

        // Update assignment on followup
        $followup->forward_to = $newStaff->id;
        $followup->save();

        return response()->json([
            'status'  => true,
            'message' => 'Follow-up reassigned successfully to ' . $newStaff->name . '.'
        ]);
    }

    /**
     * Requirement 2: Audit Trail History of Reassignments
     */
    public function reassignmentHistory(Request $request)
    {
        $history = FollowupReassignment::with([
            'followup.lead.customer',
            'previousStaff:id,name',
            'newStaff:id,name',
            'reassignedBy:id,name'
        ])
        ->orderBy('id', 'DESC')
        ->get();

        return response()->json([
            'status' => true,
            'data'   => $history
        ]);
    }

    /**
     * Requirement 2: Get pending today's followups for staff on leave
     */
    public function getLeaveStaffFollowups(Request $request, $staffId)
    {
        $today = date('Y-m-d');
        $staff = User::find($staffId);

        if (!$staff) {
            return response()->json(['status' => false, 'message' => 'Staff not found.'], 404);
        }

        $followups = Followup::with([
            'lead:lead_id,lead_title,customer_id',
            'lead.customer:customer_id,name,mobile',
            'forwardToUser:id,name',
            'creator:id,name'
        ])
        ->where('followup_status', 'Pending')
        ->whereDate('next_followup_date', $today)
        ->where(function ($q) use ($staffId) {
            $q->where('forward_to', $staffId)
              ->orWhere(function ($q2) use ($staffId) {
                  $q2->whereNull('forward_to')
                     ->where('created_by', $staffId);
              });
        })
        ->get();

        return response()->json([
            'status' => true,
            'staff'  => $staff,
            'data'   => $followups
        ]);
    }
}
