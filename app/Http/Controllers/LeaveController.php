<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\SalaryAdjustment;
use App\Models\User;
use App\Notifications\AdminLeaveRequestReceived;
use App\Notifications\LeaveQuotaCompleted;
use App\Notifications\LeaveRequestApproved;
use App\Notifications\LeaveRequestRejected;
use App\Notifications\LeaveRequestSubmitted;
use App\Services\SalaryCalculationService;
use App\Services\LeaveApprovalService;
use App\Traits\SendsPushNotifications;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LeaveController extends Controller
{
    use SendsPushNotifications;
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData($request);
        }

        $user = Auth::user();
        if ($user->isSuperAdmin() || $user->isMd() || $user->isHr()) {
            $data['staffs'] = User::staffOnly()->orderBy('name')->get();
        } elseif ($user->accessibleTeamIds()) {
            $data['staffs'] = User::staffOnly()->whereIn('team_id', $user->accessibleTeamIds())->orderBy('name')->get();
        } else {
            $data['staffs'] = User::where('id', $user->id)->get();
        }

        return view('leaves.index', $data);
    }

    public function listData(Request $request)
    {
        $user = Auth::user();
        $approvalService = app(LeaveApprovalService::class);
        $query = LeaveRequest::with([
            'user:id,name,email,team_id,designation',
            'approver:id,name',
            'tlApprover:id,name',
            'hrApprover:id,name',
            'mdApprover:id,name',
        ]);

        $isSuperAdmin = $user->isSuperAdmin();
        $isMd         = $user->isMd();
        $isHr         = $user->isHr();
        $canDelete    = $user->can('leaves.delete') || $isSuperAdmin;

        // Scoping: Super Admin, MD, and HR see all staff leaves.
        // Team Leader sees own team members' leaves + own leaves.
        // Normal staff sees only own leaves.
        if ($isSuperAdmin || $isMd || $isHr) {
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->input('user_id'));
            }
        } elseif (!empty($user->accessibleTeamIds())) {
            $teamIds = $user->accessibleTeamIds();
            $query->where(function ($q) use ($user, $teamIds) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('user', function ($uq) use ($teamIds) {
                      $uq->whereIn('team_id', $teamIds);
                  });
            });
        } else {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $leaves = $query->orderBy('id', 'DESC')->get();

        $data = $leaves->map(function ($l) use ($user, $approvalService) {
            $item = $l->toArray();
            $item['can_approve'] = $approvalService->canUserApprove($user, $l);
            $item['stage_label'] = strtoupper($l->approval_stage ?? 'tl');
            return $item;
        });

        return response()->json([
            'status'      => true,
            'can_approve' => $isSuperAdmin || $isMd || $isHr || !empty($user->accessibleTeamIds()),
            'can_delete'  => $canDelete,
            'data'        => $data
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

        $targetUser = User::find($targetUserId);
        $approvalService = app(LeaveApprovalService::class);
        $stageInfo = $approvalService->determineInitialStage($targetUser);

        $leave = LeaveRequest::create([
            'user_id'        => $targetUserId,
            'from_date'      => $validated['from_date'],
            'to_date'        => $validated['to_date'],
            'number_of_days' => $validated['number_of_days'],
            'leave_type'     => $validated['leave_type'],
            'reason'         => $validated['reason'] ?? null,
            'approval_stage' => $stageInfo['approval_stage'],
            'status'         => $stageInfo['status'],
        ]);

        // Dispatch notifications according to workflow stage
        $approvalService->notifyOnSubmit($leave, $targetUser);

        // Leave quota check
        try {
            $allowedLeaveDays = (float) ($targetUser?->available_leave_count ?? 0);
            if ($targetUser && $allowedLeaveDays > 0) {
                $month = Carbon::parse($validated['from_date']);
                $usedLeaveDays = (float) LeaveRequest::where('user_id', $targetUser->id)
                    ->whereIn('status', ['Approved', 'Pending', 'Pending TL Approval', 'Pending HR Approval', 'Pending MD Approval'])
                    ->whereDate('from_date', '<=', $month->copy()->endOfMonth())
                    ->whereDate('to_date', '>=', $month->copy()->startOfMonth())
                    ->sum('number_of_days');

                if ($usedLeaveDays >= $allowedLeaveDays) {
                    $targetUser->notify(new LeaveQuotaCompleted(
                        $month->format('F'),
                        round($usedLeaveDays, 2),
                        $allowedLeaveDays
                    ));
                    $this->sendPushNotification(
                        $targetUser,
                        'Leave Quota Alert',
                        "You have used " . round($usedLeaveDays, 2) . " of {$allowedLeaveDays} allowed leave days for " . $month->format('F') . ".",
                        [
                            'type'  => 'leave_quota',
                            'month' => $month->format('F'),
                        ]
                    );
                }
            }
        } catch (\Throwable $e) {
            Log::error('LeaveController: Error checking quota: ' . $e->getMessage());
        }

        return response()->json([
            'status'  => true,
            'message' => "Leave request submitted successfully (Status: {$stageInfo['status']}).",
            'data'    => $leave
        ]);
    }

    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $leave = LeaveRequest::find($id);
        if (!$leave) {
            return response()->json(['status' => false, 'message' => 'Leave request not found.'], 404);
        }

        $approvalService = app(LeaveApprovalService::class);
        if (!$approvalService->canUserApprove($user, $leave)) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized action. You are not authorized to approve at the current stage (' . strtoupper($leave->approval_stage ?? 'tl') . ').'
            ], 403);
        }

        $result = $approvalService->approveStage($user, $leave, $request->input('admin_remarks'));

        return response()->json([
            'status'  => true,
            'message' => $result['message'],
            'data'    => $leave->fresh(['user', 'approver', 'tlApprover', 'hrApprover', 'mdApprover'])
        ]);
    }

    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $leave = LeaveRequest::find($id);
        if (!$leave) {
            return response()->json(['status' => false, 'message' => 'Leave request not found.'], 404);
        }

        $approvalService = app(LeaveApprovalService::class);
        if (!$approvalService->canUserApprove($user, $leave)) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized action. You are not authorized to reject at the current stage (' . strtoupper($leave->approval_stage ?? 'tl') . ').'
            ], 403);
        }

        $result = $approvalService->rejectStage($user, $leave, $request->input('admin_remarks'));

        return response()->json([
            'status'  => true,
            'message' => $result['message'],
            'data'    => $leave->fresh(['user', 'approver', 'tlApprover', 'hrApprover', 'mdApprover'])
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
        $salaryCalculator = app(SalaryCalculationService::class);

        $monthStr = $request->input('month', date('Y-m'));
        if (empty($monthStr)) {
            $monthStr = date('Y-m');
        }

        $carbonMonth = Carbon::parse($monthStr . '-01');
        $totalDays    = $carbonMonth->daysInMonth;

        // Calculate working days in month (excluding Sundays)
        $sundays = 0;
        for ($d = 1; $d <= $totalDays; $d++) {
            $dt = Carbon::createFromDate($carbonMonth->year, $carbonMonth->month, $d);
            if ($dt->isSunday()) {
                $sundays++;
            }
        }
        $workingDaysInMonth = max(1, $totalDays - $sundays);

        // Only Super Admin or authorized users see all staff
        if ($isSuperAdmin) {
            $staffs = User::staffOnly()->orderBy('name')->get();
        } else {
            $staffs = User::where('id', $user->id)->get();
        }

        // Fetch custom monthly salary adjustments for the target month
        $adjustments = SalaryAdjustment::where('month', $monthStr)
            ->get()
            ->keyBy('user_id');

        $reportData = [];

        foreach ($staffs as $staff) {
            $adj = $adjustments->get($staff->id);
            $row = $this->computeStaffSalaryRecord($staff, $carbonMonth, $monthStr, $workingDaysInMonth, $salaryCalculator, $adj);
            $row['sundays_count'] = $sundays;
            $row['total_calendar_days'] = $totalDays;
            $reportData[] = $row;
        }

        $canEdit = $isSuperAdmin || $user->can('salary.edit') || $user->can('salary.view') || $user->can('salary.create');

        return response()->json([
            'status'             => true,
            'can_edit'           => $canEdit,
            'month'              => $monthStr,
            'month_name'         => $carbonMonth->format('F Y'),
            'total_days'         => $totalDays,
            'sundays'            => $sundays,
            'working_days'       => $workingDaysInMonth,
            'data'               => $reportData
        ]);
    }

    /**
     * Compute staff salary record including leave, OT, late, incentives and custom adjustments
     */
    protected function computeStaffSalaryRecord($staff, Carbon $carbonMonth, string $monthStr, int $workingDaysInMonth, $salaryCalculator, $adjustment = null)
    {
        return $salaryCalculator->computeStaffSalaryRecord($staff, $carbonMonth, $adjustment, $workingDaysInMonth);
    }

    /**
     * Update custom salary adjustment (OT income or Leave deduction) inline
     */
    public function updateSalaryAdjustment(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin() && !$user->can('salary.edit') && !$user->can('salary.view') && !$user->can('salary.create')) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'month'   => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'field'   => ['required', 'in:ot_income,leave_deduction'],
            'value'   => ['required', 'numeric', 'min:0'],
        ]);

        $monthStr = $validated['month'];
        $carbonMonth = Carbon::parse($monthStr . '-01');
        $field = $validated['field'];
        $val = round(floatval($validated['value']), 2);

        $adjustment = SalaryAdjustment::firstOrNew([
            'user_id' => $validated['user_id'],
            'month'   => $monthStr,
        ]);

        $adjustment->{$field} = $val;
        $adjustment->created_by = Auth::id();
        $adjustment->save();

        // Recompute row details
        $salaryCalculator = app(SalaryCalculationService::class);
        $totalDays = $carbonMonth->daysInMonth;
        $sundays = 0;
        for ($d = 1; $d <= $totalDays; $d++) {
            $dt = Carbon::createFromDate($carbonMonth->year, $carbonMonth->month, $d);
            if ($dt->isSunday()) $sundays++;
        }
        $workingDaysInMonth = max(1, $totalDays - $sundays);

        $staff = User::find($validated['user_id']);
        $updatedRow = $this->computeStaffSalaryRecord($staff, $carbonMonth, $monthStr, $workingDaysInMonth, $salaryCalculator, $adjustment);

        $fieldName = $field === 'ot_income' ? 'OT Income' : 'Leave Deduction';

        return response()->json([
            'status'  => true,
            'message' => "{$fieldName} updated successfully.",
            'data'    => $updatedRow,
        ]);
    }

    /**
     * Reset custom salary adjustment back to system calculated value
     */
    public function resetSalaryAdjustment(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin() && !$user->can('salary.edit') && !$user->can('salary.view') && !$user->can('salary.create')) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'month'   => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'field'   => ['required', 'in:ot_income,leave_deduction,all'],
        ]);

        $monthStr = $validated['month'];
        $carbonMonth = Carbon::parse($monthStr . '-01');
        $field = $validated['field'];

        $adjustment = SalaryAdjustment::where('user_id', $validated['user_id'])
            ->where('month', $monthStr)
            ->first();

        if ($adjustment) {
            if ($field === 'all') {
                $adjustment->delete();
                $adjustment = null;
            } else {
                $adjustment->{$field} = null;
                if (is_null($adjustment->ot_income) && is_null($adjustment->leave_deduction)) {
                    $adjustment->delete();
                    $adjustment = null;
                } else {
                    $adjustment->save();
                }
            }
        }

        // Recompute row details
        $salaryCalculator = app(SalaryCalculationService::class);
        $totalDays = $carbonMonth->daysInMonth;
        $sundays = 0;
        for ($d = 1; $d <= $totalDays; $d++) {
            $dt = Carbon::createFromDate($carbonMonth->year, $carbonMonth->month, $d);
            if ($dt->isSunday()) $sundays++;
        }
        $workingDaysInMonth = max(1, $totalDays - $sundays);

        $staff = User::find($validated['user_id']);
        $updatedRow = $this->computeStaffSalaryRecord($staff, $carbonMonth, $monthStr, $workingDaysInMonth, $salaryCalculator, $adjustment);

        return response()->json([
            'status'  => true,
            'message' => 'Reset to auto-calculated value successfully.',
            'data'    => $updatedRow,
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
