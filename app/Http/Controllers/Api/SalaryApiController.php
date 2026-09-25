<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalaryAdjustment;
use App\Models\User;
use App\Services\SalaryCalculationService;
use App\Traits\HasApiPermissionCheck;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalaryApiController extends Controller
{
    use HasApiPermissionCheck;

    protected SalaryCalculationService $salaryCalculator;

    public function __construct(SalaryCalculationService $salaryCalculator)
    {
        $this->salaryCalculator = $salaryCalculator;
    }

    /**
     * Get next / current month salary breakdown for staff
     * GET /api/v1/salary/next-details (or /api/v1/salary/current)
     */
    public function nextSalaryDetails(Request $request): JsonResponse
    {
        $currentUser = $request->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        // Allow Admin / Authorized users to view other staff's salary
        $targetStaff = $currentUser;
        if ($request->filled('staff_id') && (int) $request->staff_id !== (int) $currentUser->id) {
            if (!$this->hasPermission($currentUser, 'salary.view')) {
                return $this->permissionDeniedResponse('salary details of other staff');
            }
            $targetStaff = User::find($request->staff_id);
            if (!$targetStaff) {
                return response()->json(['status' => false, 'message' => 'Staff member not found.'], 404);
            }
        }

        // Default to current month or validated query month
        $monthStr = $request->input('month', Carbon::now()->format('Y-m'));
        if (!preg_match('/^\d{4}-\d{2}$/', $monthStr)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid month format. Please use YYYY-MM (e.g. ' . Carbon::now()->format('Y-m') . ').'
            ], 422);
        }

        $carbonMonth = Carbon::parse($monthStr . '-01');

        // Fetch adjustment if exists
        $adjustment = SalaryAdjustment::where('user_id', $targetStaff->id)
            ->where('month', $monthStr)
            ->first();

        $calc = $this->salaryCalculator->computeStaffSalaryRecord($targetStaff, $carbonMonth, $adjustment);

        $isCurrentOrFuture = $carbonMonth->format('Y-m') >= Carbon::now()->format('Y-m');
        $paymentStatus = $isCurrentOrFuture ? 'Upcoming' : 'Processed';

        $grossEarnings = round($calc['base_salary'] + $calc['ot_income'] + $calc['incentive_amount'], 2);

        $data = [
            'staff_id'              => (int) $targetStaff->id,
            'staff_name'            => $targetStaff->name,
            'email'                 => $targetStaff->email,
            'designation'           => $targetStaff->designation ?? 'Staff',
            'staff_type'            => $targetStaff->staff_type ?? 'Permanent',
            'salary_month'          => $calc['month_key'],
            'salary_month_label'    => $carbonMonth->format('F Y'),
            'expected_payout_date'  => $calc['expected_payout_date'],
            'expected_payout_label' => $calc['expected_payout_label'],
            'payment_status'        => $paymentStatus,
            'days_breakdown'        => [
                'total_calendar_days'   => (int) $calc['total_calendar_days'],
                'working_days'          => (int) $calc['working_days_in_month'],
                'sundays'               => (int) $calc['sundays_count'],
                'present_days'          => (int) $calc['present_days'],
                'available_leaves'      => (float) $calc['available_leave_count'],
                'approved_leaves'       => (float) $calc['approved_leave_days'],
                'paid_leaves'           => (float) $calc['paid_leave_days'],
                'unpaid_excess_leaves'  => (float) $calc['excess_leave_days'],
            ],
            'earnings'              => [
                'base_salary'       => (float) $calc['base_salary'],
                'per_day_salary'    => (float) $calc['per_day_salary'],
                'ot_minutes'        => (int) $calc['ot_minutes'],
                'ot_hours'          => $calc['ot_hours'],
                'ot_income'         => (float) $calc['ot_income'],
                'is_ot_edited'      => (bool) $calc['is_ot_edited'],
                'incentive_amount'  => (float) $calc['incentive_amount'],
                'gross_earnings'    => $grossEarnings,
            ],
            'deductions'            => [
                'leave_deduction'       => (float) $calc['leave_deduction'],
                'is_leave_edited'       => (bool) $calc['is_leave_edited'],
                'late_attendance_count' => (int) $calc['late_count'],
                'late_deduction'        => (float) $calc['late_deduction'],
                'total_deductions'      => (float) $calc['salary_deduction'],
            ],
            'net_salary'            => (float) $calc['net_salary'],
            'currency'              => 'INR',
        ];

        return response()->json([
            'status'  => true,
            'message' => 'Next salary details fetched successfully.',
            'data'    => $data,
        ]);
    }

    /**
     * Get staff salary history list (Eppo, Evlo amount vandhuchu)
     * GET /api/v1/salary/list (or /api/v1/salary/history)
     */
    public function salaryList(Request $request): JsonResponse
    {
        $currentUser = $request->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        // Allow Admin / Authorized users to view other staff's salary list
        $targetStaff = $currentUser;
        if ($request->filled('staff_id') && (int) $request->staff_id !== (int) $currentUser->id) {
            if (!$this->hasPermission($currentUser, 'salary.view')) {
                return $this->permissionDeniedResponse('salary history of other staff');
            }
            $targetStaff = User::find($request->staff_id);
            if (!$targetStaff) {
                return response()->json(['status' => false, 'message' => 'Staff member not found.'], 404);
            }
        }

        $now = Carbon::now();
        $limit = max(1, min(24, (int) $request->input('limit', 6)));
        $yearFilter = $request->input('year');
        $includeCurrent = filter_var($request->input('include_current', false), FILTER_VALIDATE_BOOLEAN);

        // Calculate start month: either previous month or current month if requested
        $cursorMonth = $includeCurrent ? $now->copy()->startOfMonth() : $now->copy()->subMonth()->startOfMonth();

        // Earliest join date constraint if available
        $joinMonth = null;
        if (!empty($targetStaff->date_of_joining)) {
            try {
                $joinMonth = Carbon::parse($targetStaff->date_of_joining)->startOfMonth();
            } catch (\Exception $e) {}
        }

        $monthsToCompute = [];

        if ($yearFilter && is_numeric($yearFilter)) {
            $yearInt = (int) $yearFilter;
            $maxMonthNum = ($yearInt === (int) $now->year && !$includeCurrent)
                ? max(1, (int) $now->month - 1)
                : 12;

            for ($m = $maxMonthNum; $m >= 1; $m--) {
                $carbonM = Carbon::createFromDate($yearInt, $m, 1)->startOfMonth();
                if ($carbonM->greaterThan($now) && !$includeCurrent) {
                    continue;
                }
                if ($joinMonth && $carbonM->lessThan($joinMonth)) {
                    continue;
                }
                $monthsToCompute[] = $carbonM;
                if (count($monthsToCompute) >= $limit) {
                    break;
                }
            }
        } else {
            // Consecutive past months up to limit
            for ($i = 0; $i < $limit; $i++) {
                $carbonM = $cursorMonth->copy()->subMonths($i);
                if ($joinMonth && $carbonM->lessThan($joinMonth)) {
                    break;
                }
                $monthsToCompute[] = $carbonM;
            }
        }

        $monthKeys = array_map(fn($m) => $m->format('Y-m'), $monthsToCompute);

        // Preload custom salary adjustments for this staff across all target months
        $adjustments = SalaryAdjustment::where('user_id', $targetStaff->id)
            ->whereIn('month', $monthKeys)
            ->get()
            ->keyBy('month');

        $salaryList = [];

        foreach ($monthsToCompute as $m) {
            $mKey = $m->format('Y-m');
            $adj = $adjustments->get($mKey);

            $calc = $this->salaryCalculator->computeStaffSalaryRecord($targetStaff, $m, $adj);

            // Salary credit / payout date is standard 5th of following month
            $payoutDate = $m->copy()->addMonth()->setDay(5);

            $salaryList[] = [
                'month_key'               => $mKey,
                'month_label'             => $m->format('F Y'),
                'credited_date'           => $payoutDate->format('Y-m-d'),
                'credited_date_formatted' => $payoutDate->format('d M Y'),
                'amount_received'         => (float) $calc['net_salary'],
                'base_salary'             => (float) $calc['base_salary'],
                'ot_income'               => (float) $calc['ot_income'],
                'incentive_amount'        => (float) $calc['incentive_amount'],
                'leave_deduction'         => (float) $calc['leave_deduction'],
                'late_deduction'          => (float) $calc['late_deduction'],
                'total_deductions'        => (float) $calc['salary_deduction'],
                'currency'                => 'INR',
            ];
        }

        return response()->json([
            'status'  => true,
            'message' => 'Salary list fetched successfully.',
            'count'   => count($salaryList),
            'staff'   => [
                'id'          => (int) $targetStaff->id,
                'name'        => $targetStaff->name,
                'email'       => $targetStaff->email,
                'designation' => $targetStaff->designation ?? 'Staff',
            ],
            'data'    => $salaryList,
        ]);
    }
}
