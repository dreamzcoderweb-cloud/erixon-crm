<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\PermissionRequest;
use App\Models\User;
use App\Services\SalaryCalculationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceApiController extends Controller
{
    protected SalaryCalculationService $salaryCalculator;

    public function __construct(SalaryCalculationService $salaryCalculator)
    {
        $this->salaryCalculator = $salaryCalculator;
    }

    /**
     * Get today's attendance status for the authenticated user.
     * GET /api/v1/attendance/today (or /api/v1/attendance/status)
     */
    public function todayStatus(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $today = Carbon::now()->toDateString();
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        // Check for any approved permission request for today
        $approvedPermission = PermissionRequest::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->where('status', 'Approved')
            ->first();

        $allowTimeRaw = $user->allow_check_in_time ?? $user->check_in_time;
        $shiftInfo = [
            'check_in_time'        => $user->check_in_time ? Carbon::parse($user->check_in_time)->format('h:i A') : null,
            'allow_check_in_time'  => $allowTimeRaw ? Carbon::parse($allowTimeRaw)->format('h:i A') : null,
            'check_out_time'       => $user->check_out_time ? Carbon::parse($user->check_out_time)->format('h:i A') : null,
            'late_tolerance_limit' => (int) ($user->late_attendance_count ?? 3),
        ];

        if (!$attendance) {
            return response()->json([
                'status'  => true,
                'message' => 'Not checked in yet today.',
                'data'    => [
                    'is_checked_in'          => false,
                    'status'                 => 'not_checked_in',
                    'today_date'             => $today,
                    'today_date_formatted'   => Carbon::now()->format('D, d M Y'),
                    'attendance_status'      => null,
                    'first_check_in'         => null,
                    'last_check_out'         => null,
                    'current_session_number' => 1,
                    'active_session'         => null,
                    'total_working_hours'    => null,
                    'sessions'               => [],
                    'shift_timings'          => $shiftInfo,
                    'approved_permission'    => $approvedPermission ? [
                        'id'         => $approvedPermission->id,
                        'start_time' => Carbon::parse($approvedPermission->start_time)->format('h:i A'),
                        'end_time'   => Carbon::parse($approvedPermission->end_time)->format('h:i A'),
                        'reason'     => $approvedPermission->reason,
                    ] : null,
                    'attendance'             => null,
                ]
            ]);
        }

        $sessions = $attendance->sessions_list;
        $isCheckedIn = $attendance->is_currently_checked_in;
        $lastSession = !empty($sessions) ? end($sessions) : null;

        $activeSession = null;
        if ($isCheckedIn && !empty($lastSession['check_in'])) {
            try {
                $sessionStart = Carbon::parse($today . ' ' . $lastSession['check_in']);
                $diffMinutes  = max(0, $sessionStart->diffInMinutes(Carbon::now()));
                $hours        = floor($diffMinutes / 60);
                $mins         = $diffMinutes % 60;
                $activeSession = [
                    'session'            => (int) ($lastSession['session'] ?? count($sessions)),
                    'check_in'           => $lastSession['check_in'],
                    'check_in_formatted' => Carbon::parse($lastSession['check_in'])->format('h:i A'),
                    'latitude'           => $lastSession['latitude'] ?? null,
                    'longitude'          => $lastSession['longitude'] ?? null,
                    'elapsed_minutes'    => $diffMinutes,
                    'elapsed_formatted'  => ($hours > 0 ? "{$hours} hrs " : '') . "{$mins} mins",
                ];
            } catch (\Exception $e) {
                $activeSession = [
                    'session'            => (int) ($lastSession['session'] ?? count($sessions)),
                    'check_in'           => $lastSession['check_in'],
                    'check_in_formatted' => Carbon::parse($lastSession['check_in'])->format('h:i A'),
                    'latitude'           => $lastSession['latitude'] ?? null,
                    'longitude'          => $lastSession['longitude'] ?? null,
                ];
            }
        }

        $firstCheckIn = !empty($sessions) && !empty($sessions[0]['check_in'])
            ? Carbon::parse($sessions[0]['check_in'])->format('h:i A')
            : (!empty($attendance->check_in) ? Carbon::parse($attendance->check_in)->format('h:i A') : null);

        $lastCheckOut = (!$isCheckedIn && $lastSession && !empty($lastSession['check_out']))
            ? Carbon::parse($lastSession['check_out'])->format('h:i A')
            : (!empty($attendance->check_out) && !$isCheckedIn ? Carbon::parse($attendance->check_out)->format('h:i A') : null);

        $statusKey = $isCheckedIn ? 'checked_in' : (!empty($sessions) ? 'checked_out' : 'not_checked_in');

        // Format sessions for clean API consumption
        $formattedSessions = array_map(function ($s) {
            return [
                'session'             => (int) ($s['session'] ?? 1),
                'check_in'            => $s['check_in'] ?? null,
                'check_in_formatted'  => !empty($s['check_in']) ? Carbon::parse($s['check_in'])->format('h:i A') : null,
                'check_out'           => $s['check_out'] ?? null,
                'check_out_formatted' => !empty($s['check_out']) ? Carbon::parse($s['check_out'])->format('h:i A') : null,
                'latitude'            => $s['latitude'] ?? null,
                'longitude'           => $s['longitude'] ?? null,
            ];
        }, $sessions);

        return response()->json([
            'status'  => true,
            'message' => $isCheckedIn ? 'Currently checked in.' : 'Currently checked out.',
            'data'    => [
                'is_checked_in'          => $isCheckedIn,
                'status'                 => $statusKey,
                'today_date'             => $today,
                'today_date_formatted'   => Carbon::parse($today)->format('D, d M Y'),
                'attendance_status'      => $attendance->status,
                'first_check_in'         => $firstCheckIn,
                'last_check_out'         => $lastCheckOut,
                'current_session_number' => $attendance->current_session_number,
                'active_session'         => $activeSession,
                'total_working_hours'    => $attendance->working_hours,
                'sessions'               => $formattedSessions,
                'shift_timings'          => $shiftInfo,
                'approved_permission'    => $approvedPermission ? [
                    'id'         => $approvedPermission->id,
                    'start_time' => Carbon::parse($approvedPermission->start_time)->format('h:i A'),
                    'end_time'   => Carbon::parse($approvedPermission->end_time)->format('h:i A'),
                    'reason'     => $approvedPermission->reason,
                ] : null,
                'attendance'             => $attendance,
            ]
        ]);
    }

    /**
     * Check in for the authenticated staff user.
     * POST /api/v1/attendance/check-in
     */
    public function checkIn(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'latitude'  => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $lat = $request->input('latitude');
        $lng = $request->input('longitude');
        if (!is_null($lat) && !is_numeric($lat)) { $lat = null; }
        if (!is_null($lng) && !is_numeric($lng)) { $lng = null; }

        $today = Carbon::now()->toDateString();
        $nowTime = Carbon::now()->format('H:i:s');
        $nowFormatted = Carbon::now()->format('h:i A');

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        // Check for approved permission request for user today
        $approvedPermission = PermissionRequest::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->where('status', 'Approved')
            ->first();

        if (!$attendance) {
            // Session 1 Check-In
            $status = $this->determineAttendanceStatus($user, $nowTime);

            $sessions = [
                [
                    'session'   => 1,
                    'check_in'  => $nowTime,
                    'check_out' => null,
                    'latitude'  => $lat,
                    'longitude' => $lng,
                ]
            ];

            $createData = [
                'user_id'       => $user->id,
                'date'          => $today,
                'check_in'      => $nowTime,
                'check_out'     => null,
                'working_hours' => null,
                'status'        => $status,
                'latitude'      => $lat,
                'longitude'     => $lng,
                'sessions'      => $sessions,
            ];

            if ($approvedPermission) {
                $createData['permission_start'] = $approvedPermission->start_time;
                $createData['permission_end']   = $approvedPermission->end_time;
                $createData['permission_id']    = $approvedPermission->id;
            }

            $attendance = Attendance::create($createData);

            return response()->json([
                'status'  => true,
                'message' => "Checked in successfully for Session 1 at {$nowFormatted}. Status: {$status}",
                'data'    => [
                    'is_checked_in'          => true,
                    'session_number'         => 1,
                    'check_in_time'          => $nowTime,
                    'check_in_formatted'     => $nowFormatted,
                    'attendance_status'      => $status,
                    'total_working_hours'    => null,
                    'attendance'             => $attendance->fresh(),
                ]
            ]);
        }

        // Subsequent session check-in
        $sessions = $attendance->sessions_list;
        $lastSession = !empty($sessions) ? end($sessions) : null;

        if ($lastSession && empty($lastSession['check_out'])) {
            $currSessNum = $lastSession['session'] ?? count($sessions);
            return response()->json([
                'status'  => false,
                'message' => "You are currently checked in for Session {$currSessNum}. Please check out before starting a new session."
            ], 422);
        }

        $nextSessionNum = $lastSession ? ((int) ($lastSession['session'] ?? count($sessions)) + 1) : 1;
        $newSession = [
            'session'   => $nextSessionNum,
            'check_in'  => $nowTime,
            'check_out' => null,
            'latitude'  => $lat,
            'longitude' => $lng,
        ];
        $sessions[] = $newSession;

        $updateData = [
            'sessions' => $sessions,
        ];

        if ($nextSessionNum === 2) {
            $updateData['second_check_in']           = $nowTime;
            $updateData['second_check_in_latitude']  = $lat;
            $updateData['second_check_in_longitude'] = $lng;
        }

        if (empty($attendance->latitude) && !is_null($lat)) {
            $updateData['latitude']  = $lat;
            $updateData['longitude'] = $lng;
        }

        if ($approvedPermission && empty($attendance->permission_id)) {
            $updateData['permission_start'] = $approvedPermission->start_time;
            $updateData['permission_end']   = $approvedPermission->end_time;
            $updateData['permission_id']    = $approvedPermission->id;
        }

        $attendance->update($updateData);

        return response()->json([
            'status'  => true,
            'message' => "Checked in successfully for Session {$nextSessionNum} at {$nowFormatted}.",
            'data'    => [
                'is_checked_in'          => true,
                'session_number'         => $nextSessionNum,
                'check_in_time'          => $nowTime,
                'check_in_formatted'     => $nowFormatted,
                'attendance_status'      => $attendance->status,
                'total_working_hours'    => $attendance->working_hours,
                'attendance'             => $attendance->fresh(),
            ]
        ]);
    }

    /**
     * Check out of the active session for the authenticated staff user.
     * POST /api/v1/attendance/check-out
     */
    public function checkOut(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'latitude'  => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $today = Carbon::now()->toDateString();
        $nowTime = Carbon::now()->format('H:i:s');
        $nowFormatted = Carbon::now()->format('h:i A');

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'status'  => false,
                'message' => 'You need to check in first before checking out.'
            ], 422);
        }

        $sessions = $attendance->sessions_list;
        $lastIndex = !empty($sessions) ? count($sessions) - 1 : -1;

        if ($lastIndex < 0 || !empty($sessions[$lastIndex]['check_out'])) {
            return response()->json([
                'status'  => false,
                'message' => 'No active check-in session found to check out.'
            ], 422);
        }

        $currentSessionNum = $sessions[$lastIndex]['session'] ?? ($lastIndex + 1);
        $sessions[$lastIndex]['check_out'] = $nowTime;

        $workingHours = $this->calculateWorkingHours($sessions);

        $updateData = [
            'sessions'      => $sessions,
            'working_hours' => $workingHours,
        ];

        if ($currentSessionNum === 1) {
            $updateData['check_out'] = $nowTime;
        } elseif ($currentSessionNum === 2) {
            $updateData['second_check_out'] = $nowTime;
        } else {
            $updateData['check_out'] = $nowTime;
        }

        $attendance->update($updateData);

        return response()->json([
            'status'  => true,
            'message' => "Checked out successfully for Session {$currentSessionNum} at {$nowFormatted}. Total hours: {$workingHours}",
            'data'    => [
                'is_checked_in'          => false,
                'session_number'         => $currentSessionNum,
                'check_out_time'         => $nowTime,
                'check_out_formatted'    => $nowFormatted,
                'total_working_hours'    => $workingHours,
                'attendance'             => $attendance->fresh(),
            ]
        ]);
    }

    
    /**
     * Staff attendance history with month/date filtering, pagination, and KPI summary.
     * GET /api/v1/attendance/history
     */
    public function history(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $month      = $request->input('month');
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');
        $status     = $request->input('status');
        $perPage    = max(1, min((int) ($request->input('per_page', 15)), 100));

        // Default to monthly current month if no filter passed
        if (empty($startDate) && empty($endDate) && empty($month)) {
            $month = Carbon::now()->format('Y-m');
        }

        $query = Attendance::with('permissionRequest')
            ->where('user_id', $user->id);

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif (!empty($startDate)) {
            $query->whereDate('date', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $query->whereDate('date', '<=', $endDate);
        } elseif (!empty($month)) {
            $parts = explode('-', $month);
            $y = $parts[0] ?? date('Y');
            $m = $parts[1] ?? date('m');
            $query->whereYear('date', $y)->whereMonth('date', $m);
        }

        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        // Fetch all records chronologically for summary calculations (late count, deduction, worked hours)
        $allRecords = (clone $query)->orderBy('date', 'asc')->get();

        $totalPresent        = 0;
        $totalLate           = 0;
        $totalHalfDay        = 0;
        $totalAbsent         = 0;
        $totalOnLeave        = 0;
        $totalMinutes        = 0;
        $totalLateDeductions = 0;
        $lateRunningCount    = 0;
        $allowedLateCount    = (int) ($user->late_attendance_count ?? 3);

        $rawAllowTime = $user->allow_check_in_time ?? $user->check_in_time ?? '09:10:00';
        $allowTime24  = Carbon::parse($rawAllowTime)->format('H:i:s');

        // Pre-calculate per-minute salary for deductions if user has base_salary
        $baseSalary = (float) ($user->base_salary ?? 0);
        $perMinuteSalary = 0;
        if ($baseSalary > 0) {
            $refDate = !empty($month) ? Carbon::parse($month . '-01') : Carbon::today();
            $workingDays = $this->salaryCalculator->workingDaysInMonth($refDate);
            $dailyMins = $this->salaryCalculator->dailyWorkingMinutes($user);
            $perDaySalary = $workingDays > 0 ? ($baseSalary / $workingDays) : 0;
            $perMinuteSalary = $dailyMins > 0 ? ($perDaySalary / $dailyMins) : 0;
        }

        $permissionCount = 0;

        foreach ($allRecords as $rec) {
            if ($rec->status === 'Present') { $totalPresent++; }
            elseif ($rec->status === 'Late') { $totalLate++; }
            elseif ($rec->status === 'Half Day') { $totalHalfDay++; }
            elseif ($rec->status === 'Absent') { $totalAbsent++; }
            elseif ($rec->status === 'On Leave') { $totalOnLeave++; }

            if (!empty($rec->permission_start) || !empty($rec->permission_id)) {
                $permissionCount++;
            }

            // Calculate late info and deduction
            $actualCheckIn24 = !empty($rec->check_in) ? Carbon::parse($rec->check_in)->format('H:i:s') : null;
            if ($actualCheckIn24 && $actualCheckIn24 > $allowTime24) {
                $lateRunningCount++;
                $lateDurationMins = max(0, round((strtotime($actualCheckIn24) - strtotime($allowTime24)) / 60));
                if ($lateRunningCount > $allowedLateCount && $perMinuteSalary > 0) {
                    $totalLateDeductions += round($lateDurationMins * $perMinuteSalary, 2);
                }
            }

            // Calculate worked minutes
            $sessList = $rec->sessions_list;
            if (!empty($sessList)) {
                foreach ($sessList as $s) {
                    if (!empty($s['check_in']) && !empty($s['check_out'])) {
                        try {
                            $in  = Carbon::parse($s['check_in']);
                            $out = Carbon::parse($s['check_out']);
                            if ($out->lessThan($in)) {
                                $out->addDay();
                            }
                            $totalMinutes += $in->diffInMinutes($out);
                        } catch (\Exception $e) {}
                    }
                }
            } elseif (!empty($rec->check_in) && !empty($rec->check_out)) {
                try {
                    $in  = Carbon::parse($rec->check_in);
                    $out = Carbon::parse($rec->check_out);
                    if ($out->lessThan($in)) {
                        $out->addDay();
                    }
                    $totalMinutes += $in->diffInMinutes($out);
                } catch (\Exception $e) {}
            }
        }

        $totalHours = floor($totalMinutes / 60);
        $remMinutes = $totalMinutes % 60;
        $totalHoursFormatted = $totalHours . ' hrs' . ($remMinutes > 0 ? " {$remMinutes} mins" : '');

        $summary = [
            'total_records'        => $allRecords->count(),
            'total_present'        => $totalPresent,
            'total_late'           => $totalLate,
            'total_half_day'       => $totalHalfDay,
            'total_absent'         => $totalAbsent,
            'total_on_leave'       => $totalOnLeave,
            'total_permissions'    => $permissionCount,
            'total_working_hours'  => $totalHoursFormatted,
            'total_working_minutes'=> $totalMinutes,
            'allowed_late_limit'   => $allowedLateCount,
            'actual_late_count'    => $lateRunningCount,
            'total_late_deduction' => round($totalLateDeductions, 2),
        ];

        // Paginated records sorted descending by date
        $paginated = $query->orderBy('date', 'desc')->paginate($perPage);

        // Transform collection items for mobile app presentation
        $paginated->getCollection()->transform(function ($rec) use ($allowTime24) {
            $sessList = $rec->sessions_list;
            $formattedSessions = array_map(function ($s) {
                return [
                    'session'             => (int) ($s['session'] ?? 1),
                    'check_in'            => $s['check_in'] ?? null,
                    'check_in_formatted'  => !empty($s['check_in']) ? Carbon::parse($s['check_in'])->format('h:i A') : null,
                    'check_out'           => $s['check_out'] ?? null,
                    'check_out_formatted' => !empty($s['check_out']) ? Carbon::parse($s['check_out'])->format('h:i A') : null,
                    'latitude'            => $s['latitude'] ?? null,
                    'longitude'           => $s['longitude'] ?? null,
                ];
            }, $sessList);

            $firstCheckIn = !empty($sessList) && !empty($sessList[0]['check_in'])
                ? Carbon::parse($sessList[0]['check_in'])->format('h:i A')
                : (!empty($rec->check_in) ? Carbon::parse($rec->check_in)->format('h:i A') : '-');

            $lastSession = !empty($sessList) ? end($sessList) : null;
            $lastCheckOut = $lastSession && !empty($lastSession['check_out'])
                ? Carbon::parse($lastSession['check_out'])->format('h:i A')
                : (!empty($rec->check_out) ? Carbon::parse($rec->check_out)->format('h:i A') : '-');

            $actualCheckIn24 = !empty($rec->check_in) ? Carbon::parse($rec->check_in)->format('H:i:s') : null;
            $isLate = $actualCheckIn24 && $actualCheckIn24 > $allowTime24;
            $lateMinutes = 0;
            if ($isLate) {
                $lateMinutes = max(0, round((strtotime($actualCheckIn24) - strtotime($allowTime24)) / 60));
            }

            // Permission breakdown
            $permPeriod = null;
            if (!empty($rec->permission_start) && !empty($rec->permission_end)) {
                $permPeriod = Carbon::parse($rec->permission_start)->format('h:i A') . ' → ' . Carbon::parse($rec->permission_end)->format('h:i A');
            }

            return [
                'attendance_id'       => $rec->attendance_id,
                'date'                => $rec->date,
                'date_formatted'      => Carbon::parse($rec->date)->format('D, d M Y'),
                'status'              => $rec->status,
                'first_check_in'      => $firstCheckIn,
                'last_check_out'      => $lastCheckOut,
                'working_hours'       => $rec->working_hours ?? '-',
                'is_late'             => $isLate,
                'late_minutes'        => $lateMinutes,
                'permission_period'   => $permPeriod,
                'sessions_count'      => count($sessList),
                'sessions'            => $formattedSessions,
            ];
        });

        return response()->json([
            'status'  => true,
            'summary' => $summary,
            'data'    => $paginated,
        ]);
    }

    /**
     * Determine attendance status (Present vs Late) based on user's check-in schedule.
     */
    private function determineAttendanceStatus(User $user, string $actualCheckIn): string
    {
        $allowTime = $user->allow_check_in_time ?? $user->check_in_time;
        if (!$allowTime) {
            return 'Present';
        }

        try {
            $assignedIn = Carbon::parse($allowTime)->format('H:i:s');
            $actualIn   = Carbon::parse($actualCheckIn)->format('H:i:s');

            if ($actualIn > $assignedIn) {
                return 'Late';
            }

            return 'Present';
        } catch (\Exception $e) {
            return 'Present';
        }
    }

    /**
     * Calculate cumulative working hours formatted as "X hrs Y mins".
     */
    private function calculateWorkingHours(array $sessions): ?string
    {
        $totalMinutes = 0;

        foreach ($sessions as $s) {
            if (!empty($s['check_in']) && !empty($s['check_out'])) {
                try {
                    $in  = Carbon::parse($s['check_in']);
                    $out = Carbon::parse($s['check_out']);
                    if ($out->lessThan($in)) {
                        $out->addDay();
                    }
                    $totalMinutes += $in->diffInMinutes($out);
                } catch (\Exception $e) {}
            }
        }

        if ($totalMinutes <= 0) {
            return null;
        }

        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        if ($minutes > 0) {
            return "{$hours} hrs {$minutes} mins";
        }

        return "{$hours} hrs";
    }
}
