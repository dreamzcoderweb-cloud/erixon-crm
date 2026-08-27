<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\LeaveQuotaCompleted;
use App\Notifications\LeaveRequestSubmitted;
use App\Notifications\LeaveRequestApproved;
use App\Notifications\LeaveRequestRejected;
use App\Notifications\AdminLeaveRequestReceived;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData($request);
        }

        $user = Auth::user();
        if ($user->isSuperAdmin()) {
            $data['staffs'] = User::staffOnly()->orderBy('name')->get();
        } else {
            $data['staffs'] = User::where('id', $user->id)->get();
        }

        return view('leaves.index', $data);
    }

    public function listData(Request $request)
    {
        $user = Auth::user();
        $query = LeaveRequest::with(['user:id,name,email', 'approver:id,name']);

        $isSuperAdmin = $user->isSuperAdmin();
        $canApprove   = $user->can('leaves.approve') || $isSuperAdmin;
        $canDelete    = $user->can('leaves.delete') || $isSuperAdmin;

        // Only Super Admin can view all staff list details.
        // Non-Super Admin staff (e.g. Tharik) can only see their own leave requests.
        if (!$isSuperAdmin) {
            $query->where('user_id', $user->id);
        } else {
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->input('user_id'));
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $leaves = $query->orderBy('id', 'DESC')->get();

        return response()->json([
            'status'      => true,
            'can_approve' => $canApprove,
            'can_delete'  => $canDelete,
            'data'        => $leaves
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'        => ['nullable', 'exists:users,id'],
            'from_date'      => ['required', 'date'],
            'to_date'        => ['required', 'date', 'after_or_equal:from_date'],
            'number_of_days' => ['required', 'numeric', 'min:0.5'],
            'leave_type'     => ['required', 'string', 'max:50'],
            'reason'         => ['nullable', 'string', 'max:1000'],
        ]);

        $targetUserId = Auth::id();
        if (Auth::user()->isSuperAdmin() && !empty($validated['user_id'])) {
            $targetUserId = $validated['user_id'];
        }

        $leave = LeaveRequest::create([
            'user_id'        => $targetUserId,
            'from_date'      => $validated['from_date'],
            'to_date'        => $validated['to_date'],
            'number_of_days' => $validated['number_of_days'],
            'leave_type'     => $validated['leave_type'],
            'reason'         => $validated['reason'] ?? null,
            'status'         => 'Pending',
        ]);

        $targetUser = User::find($targetUserId);
        if ($targetUser) {
            $targetUser->notify(new LeaveRequestSubmitted($leave));
        }

        $allowedLeaveDays = (float) ($targetUser?->available_leave_count ?? 0);
        if ($targetUser && $allowedLeaveDays > 0) {
            $month = Carbon::parse($validated['from_date']);
            $usedLeaveDays = (float) LeaveRequest::where('user_id', $targetUser->id)
                ->whereIn('status', ['Approved', 'Pending'])
                ->whereDate('from_date', '<=', $month->copy()->endOfMonth())
                ->whereDate('to_date', '>=', $month->copy()->startOfMonth())
                ->sum('number_of_days');

            if ($usedLeaveDays >= $allowedLeaveDays) {
                $targetUser->notify(new LeaveQuotaCompleted(
                    $month->format('F'),
                    round($usedLeaveDays, 2),
                    $allowedLeaveDays
                ));
            }
        }

        return response()->json([
            'status'  => true,
            'message' => 'Leave request submitted successfully (Status: Pending).',
            'data'    => $leave
        ]);
    }

    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->can('leaves.approve') && !$user->hasRole(['Super Admin', 'Admin'])) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action. Permission leaves.approve required.'], 403);
        }

        $leave = LeaveRequest::find($id);
        if (!$leave) {
            return response()->json(['status' => false, 'message' => 'Leave request not found.'], 404);
        }

        $leave->status = 'Approved';
        $leave->approved_by = Auth::id();
        $leave->admin_remarks = $request->input('admin_remarks');
        $leave->save();

        // Update user is_on_leave status if leave is active today
        $today = Carbon::today()->toDateString();
        $staff = User::find($leave->user_id);
        if ($leave->from_date->toDateString() <= $today && $leave->to_date->toDateString() >= $today) {
            if ($staff) {
                $staff->is_on_leave = true;
                $staff->save();
            }
        }

        // Notify staff member that leave request was approved by admin
        if ($staff) {
            $staff->notify(new LeaveRequestApproved($leave));
        }

        return response()->json([
            'status'  => true,
            'message' => 'Leave request approved successfully.',
            'data'    => $leave
        ]);
    }

    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->can('leaves.approve') && !$user->hasRole(['Super Admin', 'Admin'])) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action. Permission leaves.approve required.'], 403);
        }

        $leave = LeaveRequest::find($id);
        if (!$leave) {
            return response()->json(['status' => false, 'message' => 'Leave request not found.'], 404);
        }

        $leave->status = 'Rejected';
        $leave->approved_by = Auth::id();
        $leave->admin_remarks = $request->input('admin_remarks');
        $leave->save();

        // Notify staff member that leave request was rejected by admin
        $staff = User::find($leave->user_id);
        if ($staff) {
            $staff->notify(new LeaveRequestRejected($leave));
        }

        return response()->json([
            'status'  => true,
            'message' => 'Leave request rejected.',
            'data'    => $leave
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->can('leaves.delete') && !$user->hasRole(['Super Admin', 'Admin'])) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action. Permission leaves.delete required.'], 403);
        }

        $leave = LeaveRequest::find($id);
        if (!$leave) {
            return response()->json(['status' => false, 'message' => 'Leave request not found.'], 404);
        }

        $leave->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Leave request deleted successfully.'
        ]);
    }

    /**
     * Month-wise Salary and Excess Leave Deduction Summary Report
     */
    /**
     * Requirement 3: Month-wise Salary and Excess Leave Deduction Summary Report - only user for non-admin staff
     */
    public function salaryReportData(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();

        $monthStr = $request->input('month', date('Y-m'));
        if (empty($monthStr)) {
            $monthStr = date('Y-m');
        }

        $carbonMonth = Carbon::parse($monthStr . '-01');

        $startOfMonth = $carbonMonth->copy()->startOfMonth()->startOfDay();
        $endOfMonth   = $carbonMonth->copy()->endOfMonth()->endOfDay();
        $totalDays    = $carbonMonth->daysInMonth;

        // Requirement 8: Calculate working days in month (excluding Sundays)
        $sundays = 0;
        for ($d = 1; $d <= $totalDays; $d++) {
            $dt = Carbon::createFromDate($carbonMonth->year, $carbonMonth->month, $d);
            if ($dt->isSunday()) {
                $sundays++;
            }
        }
        $workingDaysInMonth = max(1, $totalDays - $sundays);

        // Requirement 3: monthly salary staff details - only Super Admin sees all staff
        if ($isSuperAdmin) {
            $staffs = User::staffOnly()->orderBy('name')->get();
        } else {
            $staffs = User::where('id', $user->id)->get();
        }

        $reportData = [];

        foreach ($staffs as $staff) {
            // Retrieve only APPROVED leave requests overlapping with this month
            $approvedLeaves = LeaveRequest::where('user_id', $staff->id)
                ->where('status', 'Approved')
                ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->whereBetween('from_date', [$startOfMonth->toDateTimeString(), $endOfMonth->toDateTimeString()])
                      ->orWhereBetween('to_date', [$startOfMonth->toDateTimeString(), $endOfMonth->toDateTimeString()])
                      ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                          $q2->where('from_date', '<=', $startOfMonth->toDateTimeString())
                             ->where('to_date', '>=', $endOfMonth->toDateTimeString());
                      });
                })->get();

            $totalApprovedLeaveDays = 0;
            foreach ($approvedLeaves as $leave) {
                $lFrom = Carbon::parse($leave->from_date)->startOfDay();
                $lTo   = Carbon::parse($leave->to_date)->startOfDay();

                $overlapStart = $lFrom->greaterThan($startOfMonth) ? $lFrom->copy() : $startOfMonth->copy();
                $overlapEnd   = $lTo->lessThan($endOfMonth) ? $lTo->copy() : $endOfMonth->copy();

                if ($overlapStart->lessThanOrEqualTo($overlapEnd)) {
                    $curr = $overlapStart->copy();
                    $leaveDaysInOverlap = 0;
                    while ($curr->lessThanOrEqualTo($overlapEnd)) {
                        // Exclude Sundays (weekly holidays) from deductible leave calculation
                        if (!$curr->isSunday()) {
                            if ($lFrom->equalTo($lTo) && floatval($leave->number_of_days) <= 0.5) {
                                $leaveDaysInOverlap += floatval($leave->number_of_days);
                            } else {
                                $leaveDaysInOverlap += 1.0;
                            }
                        }
                        $curr->addDay();
                    }
                    $totalApprovedLeaveDays += min($leaveDaysInOverlap, floatval($leave->number_of_days));
                }
            }

            $totalApprovedLeaveDays = round($totalApprovedLeaveDays, 2);
            $availableLeaves = floatval($staff->available_leave_count ?? 0);
            $excessLeaveDays = max(0, round($totalApprovedLeaveDays - $availableLeaves, 2));

            $baseSalary = floatval($staff->base_salary ?? 0);
            $perDaySalary = $workingDaysInMonth > 0 ? ($baseSalary / $workingDaysInMonth) : 0;
            $leaveDeduction = round($excessLeaveDays * $perDaySalary, 2);

            // Calculate Late Attendance Deduction for staff in target month
            $allowedLateCount = (int) ($staff->late_attendance_count ?? 3);
            $rawAllowTime = ($staff && $staff->allow_check_in_time) ? $staff->allow_check_in_time : (($staff && $staff->check_in_time) ? $staff->check_in_time : '09:10:00');
            $allowTime24 = Carbon::parse($rawAllowTime)->format('H:i:s');

            $dailyMins = 480;
            if ($staff && $staff->check_in_time && $staff->check_out_time) {
                try {
                    $cIn = Carbon::parse($staff->check_in_time);
                    $cOut = Carbon::parse($staff->check_out_time);
                    $diff = $cIn->diffInMinutes($cOut);
                    if ($diff > 0) {
                        $dailyMins = $diff;
                    }
                } catch (\Exception $e) {}
            }

            $perMinuteSalary = $dailyMins > 0 ? ($perDaySalary / $dailyMins) : 0;

            $attRecords = \App\Models\Attendance::where('user_id', $staff->id)
                ->whereYear('date', $carbonMonth->year)
                ->whereMonth('date', $carbonMonth->month)
                ->orderBy('date', 'ASC')
                ->get();

            $lateCount = 0;
            $lateDeduction = 0.00;

            foreach ($attRecords as $rec) {
                $actualCheckIn24 = !empty($rec->check_in) ? Carbon::parse($rec->check_in)->format('H:i:s') : null;
                if ($actualCheckIn24 && $actualCheckIn24 > $allowTime24) {
                    $lateCount++;
                    if ($lateCount > $allowedLateCount) {
                        $inTimeSeconds = strtotime($actualCheckIn24);
                        $allowTimeSeconds = strtotime($allowTime24);
                        $lateDurationMins = max(0, round(($inTimeSeconds - $allowTimeSeconds) / 60));
                        $lateDeduction += round($lateDurationMins * $perMinuteSalary, 2);
                    }
                }
            }

            $lateDeduction = round($lateDeduction, 2);
            $perDaySalaryRate = round($perDaySalary, 2);
            $totalSalaryDeduction = round($leaveDeduction + $lateDeduction, 2);

            // Incentive amount for target month
            $incentiveAmount = \App\Models\Incentive::where('staff_id', $staff->id)
                ->where('month', $monthStr)
                ->sum('amount');
            $incentiveAmount = round(floatval($incentiveAmount ?? 0), 2);

            $netSalary = max(0, round($baseSalary - $totalSalaryDeduction + $incentiveAmount, 2));

            $reportData[] = [
                'user_id'                => $staff->id,
                'staff_name'             => $staff->name,
                'email'                  => $staff->email,
                'designation'            => $staff->designation ?? 'Staff',
                'base_salary'            => $baseSalary,
                'available_leave_count'  => $availableLeaves,
                'approved_leave_days'    => $totalApprovedLeaveDays,
                'excess_leave_days'      => $excessLeaveDays,
                'working_days_in_month'  => $workingDaysInMonth,
                'sundays_count'          => $sundays,
                'total_calendar_days'    => $totalDays,
                'per_day_salary'         => $perDaySalaryRate,
                'leave_deduction'        => $leaveDeduction,
                'late_deduction'         => $lateDeduction,
                'salary_deduction'       => $totalSalaryDeduction,
                'incentive_amount'       => $incentiveAmount,
                'net_salary'             => $netSalary,
            ];
        }

        return response()->json([
            'status'             => true,
            'month'              => $monthStr,
            'month_name'         => $carbonMonth->format('F Y'),
            'total_days'         => $totalDays,
            'sundays'            => $sundays,
            'working_days'       => $workingDaysInMonth,
            'data'               => $reportData
        ]);
    }

    /**
     * Requirement 8: Permission Approval & Timing (Short Permission / Timing)
     */
    public function listPermissions(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();

        $query = \App\Models\PermissionRequest::with(['user:id,name,email', 'approver:id,name']);

        if (!$isSuperAdmin) {
            $query->where('user_id', $user->id);
        }

        $permissions = $query->orderBy('id', 'DESC')->get();

        return response()->json([
            'status'      => true,
            'can_approve' => $isSuperAdmin,
            'data'        => $permissions
        ]);
    }

    public function storePermission(Request $request)
    {
        $validated = $request->validate([
            'user_id'         => ['nullable', 'exists:users,id'],
            'date'            => ['required', 'date'],
            'start_time'      => ['required'],
            'end_time'        => ['required'],
            'permission_type' => ['required', 'string', 'max:100'],
            'reason'          => ['nullable', 'string', 'max:1000'],
        ]);

        $targetUserId = Auth::id();
        if (Auth::user()->isSuperAdmin() && !empty($validated['user_id'])) {
            $targetUserId = $validated['user_id'];
        }

        $permission = \App\Models\PermissionRequest::create([
            'user_id'         => $targetUserId,
            'date'            => $validated['date'],
            'start_time'      => $validated['start_time'],
            'end_time'        => $validated['end_time'],
            'permission_type' => $validated['permission_type'],
            'reason'          => $validated['reason'] ?? null,
            'status'          => 'Pending',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Permission request submitted successfully (Pending Approval).',
            'data'    => $permission
        ]);
    }

    public function approvePermission(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->can('permissions.approve') && !$user->hasRole(['Super Admin', 'Admin', 'super admin', 'super-admin'])) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $permission = \App\Models\PermissionRequest::find($id);
        if (!$permission) {
            return response()->json(['status' => false, 'message' => 'Permission request not found.'], 404);
        }

        $permission->status        = 'Approved';
        $permission->approved_by   = Auth::id();
        $permission->admin_remarks = $request->input('admin_remarks');
        $permission->save();

        // Auto-link approved permission details to the user's Attendance record for that date if present
        $attendance = \App\Models\Attendance::where('user_id', $permission->user_id)
            ->whereDate('date', $permission->date)
            ->first();

        if ($attendance) {
            $attendance->update([
                'permission_start' => $permission->start_time,
                'permission_end'   => $permission->end_time,
                'permission_id'    => $permission->id,
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Permission request approved successfully.',
            'data'    => $permission
        ]);
    }

    public function rejectPermission(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->can('permissions.approve') && !$user->hasRole(['Super Admin', 'Admin', 'super admin', 'super-admin'])) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $permission = \App\Models\PermissionRequest::find($id);
        if (!$permission) {
            return response()->json(['status' => false, 'message' => 'Permission request not found.'], 404);
        }

        $permission->status        = 'Rejected';
        $permission->approved_by   = Auth::id();
        $permission->admin_remarks = $request->input('admin_remarks');
        $permission->save();

        return response()->json([
            'status'  => true,
            'message' => 'Permission request rejected.',
            'data'    => $permission
        ]);
    }
}
