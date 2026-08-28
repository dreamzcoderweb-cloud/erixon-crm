<?php

namespace App\Services;

use App\Models\Attendance;
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
}
