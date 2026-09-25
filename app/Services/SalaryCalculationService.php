<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Incentive;
use App\Models\LeaveRequest;
use App\Models\SalaryAdjustment;
use App\Models\User;
use Carbon\Carbon;

class SalaryCalculationService
{
    public function workingDaysInMonth(Carbon $month): int
    {
        $workingDays = 0;
        $current = $month->copy()->startOfMonth();

        while ($current->month === $month->month) {
            if (!$current->isSunday()) {
                $workingDays++;
            }
            $current->addDay();
        }

        return max(1, $workingDays);
    }

    public function dailyWorkingMinutes(?User $staff): int
    {
        if ($staff && $staff->check_in_time && $staff->check_out_time) {
            try {
                $minutes = Carbon::parse($staff->check_in_time)
                    ->diffInMinutes(Carbon::parse($staff->check_out_time));

                if ($minutes > 0) {
                    return $minutes;
                }
            } catch (\Exception $e) {}
        }

        return 480;
    }

    public function otMinutes(?Attendance $attendance, ?User $staff): int
    {
        if (!$attendance || !$attendance->check_out || !$staff || !$staff->check_out_time) {
            return 0;
        }

        try {
            $date = $attendance->date ? Carbon::parse($attendance->date) : Carbon::today();
            $checkout = $date->copy()->setTimeFromTimeString($attendance->check_out);
            $workFinished = $date->copy()->setTimeFromTimeString($staff->check_out_time);

            if ($checkout->lessThan($workFinished)) {
                $checkout->addDay();
            }

            if ($checkout->lessThanOrEqualTo($workFinished)) {
                return 0;
            }

            return $workFinished->diffInMinutes($checkout);
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function otIncome(?Attendance $attendance, ?User $staff, int $workingDays): float
    {
        $dailyRate = $workingDays > 0 ? ((float) ($staff->base_salary ?? 0) / $workingDays) : 0;
        $perMinuteRate = $this->dailyWorkingMinutes($staff) > 0
            ? $dailyRate / $this->dailyWorkingMinutes($staff)
            : 0;

        return round($this->otMinutes($attendance, $staff) * $perMinuteRate, 2);
    }

    /**
     * Compute staff salary record including leave, OT, late, incentives, and custom adjustments
     */
    public function computeStaffSalaryRecord(
        User $staff,
        Carbon $carbonMonth,
        ?SalaryAdjustment $adjustment = null,
        ?int $workingDaysInMonth = null
    ): array {
        $monthStr = $carbonMonth->format('Y-m');
        $startOfMonth = $carbonMonth->copy()->startOfMonth()->startOfDay();
        $endOfMonth   = $carbonMonth->copy()->endOfMonth()->endOfDay();

        $totalDays = $carbonMonth->daysInMonth;
        $sundays = 0;
        for ($d = 1; $d <= $totalDays; $d++) {
            $dt = Carbon::createFromDate($carbonMonth->year, $carbonMonth->month, $d);
            if ($dt->isSunday()) {
                $sundays++;
            }
        }

        if ($workingDaysInMonth === null) {
            $workingDaysInMonth = max(1, $totalDays - $sundays);
        }

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
        $paidLeaveDays = min($totalApprovedLeaveDays, $availableLeaves);
        $excessLeaveDays = max(0, round($totalApprovedLeaveDays - $availableLeaves, 2));

        $baseSalary = floatval($staff->base_salary ?? 0);
        $perDaySalary = $workingDaysInMonth > 0 ? ($baseSalary / $workingDaysInMonth) : 0;
        $autoLeaveDeduction = round($excessLeaveDays * $perDaySalary, 2);

        $isLeaveEdited = false;
        if ($adjustment && !is_null($adjustment->leave_deduction)) {
            $leaveDeduction = round(floatval($adjustment->leave_deduction), 2);
            $isLeaveEdited = true;
        } else {
            $leaveDeduction = $autoLeaveDeduction;
        }

        // Calculate Late Attendance Deduction for staff in target month
        $allowedLateCount = (int) ($staff->late_attendance_count ?? 3);
        $rawAllowTime = ($staff && $staff->allow_check_in_time) ? $staff->allow_check_in_time : (($staff && $staff->check_in_time) ? $staff->check_in_time : '09:10:00');
        $allowTime24 = Carbon::parse($rawAllowTime)->format('H:i:s');

        $dailyMins = $this->dailyWorkingMinutes($staff);
        $perMinuteSalary = $dailyMins > 0 ? ($perDaySalary / $dailyMins) : 0;

        $attRecords = Attendance::where('user_id', $staff->id)
            ->whereYear('date', $carbonMonth->year)
            ->whereMonth('date', $carbonMonth->month)
            ->orderBy('date', 'ASC')
            ->get();

        $lateCount = 0;
        $lateDeduction = 0.00;
        $totalOtMinutes = 0;
        $autoOtIncome = 0.00;
        $presentDays = 0;

        foreach ($attRecords as $rec) {
            if (!empty($rec->check_in)) {
                $presentDays++;
            }

            // Late deduction
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

            // OT Calculation
            $totalOtMinutes += $this->otMinutes($rec, $staff);
            $autoOtIncome += $this->otIncome($rec, $staff, $workingDaysInMonth);
        }

        $lateDeduction = round($lateDeduction, 2);
        $autoOtIncome = round($autoOtIncome, 2);

        $isOtEdited = false;
        if ($adjustment && !is_null($adjustment->ot_income)) {
            $otIncome = round(floatval($adjustment->ot_income), 2);
            $isOtEdited = true;
        } else {
            $otIncome = $autoOtIncome;
        }

        $perDaySalaryRate = round($perDaySalary, 2);
        $totalSalaryDeduction = round($leaveDeduction + $lateDeduction, 2);

        // Incentive amount for target month
        $incentiveAmount = Incentive::where('staff_id', $staff->id)
            ->where('month', $monthStr)
            ->sum('amount');
        $incentiveAmount = round(floatval($incentiveAmount ?? 0), 2);

        $netSalary = max(0, round($baseSalary + $otIncome - $totalSalaryDeduction + $incentiveAmount, 2));

        $otHours = sprintf('%02d:%02d', intdiv($totalOtMinutes, 60), ($totalOtMinutes % 60));

        // Payout date is 5th of next month
        $payoutDate = $carbonMonth->copy()->addMonth()->setDay(5);

        return [
            'user_id'                => $staff->id,
            'staff_name'             => $staff->name,
            'email'                  => $staff->email,
            'month'                  => $carbonMonth->format('M Y'),
            'month_key'              => $monthStr,
            'designation'            => $staff->designation ?? 'Staff',
            'base_salary'            => $baseSalary,
            'available_leave_count'  => $availableLeaves,
            'total_leave_days'       => $totalApprovedLeaveDays,
            'paid_leave_days'        => round($paidLeaveDays, 2),
            'unpaid_leave_days'      => $excessLeaveDays,
            'approved_leave_days'    => $totalApprovedLeaveDays,
            'excess_leave_days'      => $excessLeaveDays,
            'working_days_in_month'  => $workingDaysInMonth,
            'per_day_salary'         => $perDaySalaryRate,
            'leave_deduction'        => $leaveDeduction,
            'auto_leave_deduction'   => $autoLeaveDeduction,
            'is_leave_edited'        => $isLeaveEdited,
            'ot_income'              => $otIncome,
            'auto_ot_income'         => $autoOtIncome,
            'is_ot_edited'           => $isOtEdited,
            'ot_minutes'             => $totalOtMinutes,
            'ot_hours'               => $otHours,
            'late_count'             => $lateCount,
            'late_deduction'         => $lateDeduction,
            'salary_deduction'       => $totalSalaryDeduction,
            'incentive_amount'       => $incentiveAmount,
            'net_salary'             => $netSalary,
            'total_calendar_days'    => $totalDays,
            'sundays_count'          => $sundays,
            'present_days'           => $presentDays,
            'expected_payout_date'   => $payoutDate->format('Y-m-d'),
            'expected_payout_label'  => $payoutDate->format('d M Y'),
        ];
    }
}
