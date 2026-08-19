<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
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

        $data['staffs'] = User::orderBy('name')->get();
        return view('leaves.index', $data);
    }

    public function listData(Request $request)
    {
        $user = Auth::user();
        $query = LeaveRequest::with(['user:id,name,email', 'approver:id,name']);

        $canApprove = $user->can('leaves.approve') || $user->hasRole(['Super Admin', 'Admin']);
        $canDelete  = $user->can('leaves.delete') || $user->hasRole(['Super Admin', 'Admin']);

        // Non-admin staff can only see their own leave requests
        if (!$canApprove) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
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
        if (Auth::user()->can('leaves.approve') && !empty($validated['user_id'])) {
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
        if ($leave->from_date->toDateString() <= $today && $leave->to_date->toDateString() >= $today) {
            $staff = User::find($leave->user_id);
            if ($staff) {
                $staff->is_on_leave = true;
                $staff->save();
            }
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
    public function salaryReportData(Request $request)
    {
        $monthStr = $request->input('month', date('Y-m'));
        if (empty($monthStr)) {
            $monthStr = date('Y-m');
        }

        $carbonMonth = Carbon::parse($monthStr . '-01');

        $startOfMonth = $carbonMonth->copy()->startOfMonth()->startOfDay();
        $endOfMonth   = $carbonMonth->copy()->endOfMonth()->endOfDay();
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

        // Fetch staff members excluding Super Admin role
        $staffs = User::whereDoesntHave('roles', function ($q) {
            $q->whereIn('name', ['Super Admin', 'super admin', 'super-admin', 'Super-Admin']);
        })->orderBy('name')->get();

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
                    $daysInMonth = $overlapStart->diffInDays($overlapEnd) + 1;
                    $totalApprovedLeaveDays += min($daysInMonth, floatval($leave->number_of_days));
                }
            }

            $totalApprovedLeaveDays = round($totalApprovedLeaveDays, 2);
            $availableLeaves = floatval($staff->available_leave_count ?? 0);
            $excessLeaveDays = max(0, round($totalApprovedLeaveDays - $availableLeaves, 2));

            $baseSalary = floatval($staff->base_salary ?? 0);
            $perDaySalary = $workingDaysInMonth > 0 ? round($baseSalary / $workingDaysInMonth, 2) : 0;
            $salaryDeduction = round($excessLeaveDays * $perDaySalary, 2);
            $netSalary = max(0, round($baseSalary - $salaryDeduction, 2));

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
                'per_day_salary'         => $perDaySalary,
                'salary_deduction'       => $salaryDeduction,
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
}
