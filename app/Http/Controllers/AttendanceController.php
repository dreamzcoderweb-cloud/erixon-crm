<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Services\SalaryCalculationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData($request);
        }

        $user = auth()->user();
        $data['staffs'] = $user->isSuperAdmin()
            ? User::staffOnly()->orderBy('name')->get()
            : User::where('id', $user->id)->get();

        $data['myTodayAttendance'] = Attendance::where('user_id', $user->id)
            ->whereDate('date', date('Y-m-d'))
            ->first();

        return view('attendance.view', $data);
    }

    public function listData(Request $request = null)
    {
        $request = $request ?? request();
        $user = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();

        $query = Attendance::with('user:id,name,email,check_in_time,check_out_time,allow_check_in_time');

        // Non-Super Admin staff can only see their own attendance records
        if (!$isSuperAdmin) {
            $query->where('user_id', $user->id);
        } else {
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->input('user_id'));
            }
        }

        if ($request->filled('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('checkin_checkout')) {
            $cc = $request->input('checkin_checkout');
            if ($cc === 'checked_in') {
                $query->whereNotNull('check_in');
            } elseif ($cc === 'checked_out') {
                $query->whereNotNull('check_out');
            } elseif ($cc === 'not_checked_out') {
                $query->whereNotNull('check_in')->whereNull('check_out');
            }
        }

        if ($request->filled('check_in_time') && trim((string) $request->input('check_in_time')) !== '') {
            $inTime = trim((string) $request->input('check_in_time'));
            $query->where(function ($q) use ($inTime) {
                $q->where('check_in', 'LIKE', "%{$inTime}%")
                  ->orWhereRaw("TIME_FORMAT(check_in, '%H:%i') = ?", [$inTime]);
            });
        }

        if ($request->filled('check_out_time') && trim((string) $request->input('check_out_time')) !== '') {
            $outTime = trim((string) $request->input('check_out_time'));
            $query->where(function ($q) use ($outTime) {
                $q->where('check_out', 'LIKE', "%{$outTime}%")
                  ->orWhereRaw("TIME_FORMAT(check_out, '%H:%i') = ?", [$outTime]);
            });
        }

        $filterType = $request->input('filter_type');
        $date       = $request->input('date');
        $month      = $request->input('month');
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');

        if ($filterType === 'daily' && !empty($date)) {
            $query->whereDate('date', $date);
        } elseif ($filterType === 'weekly') {
            $refDate = !empty($startDate) ? Carbon::parse($startDate) : Carbon::today();
            $query->whereBetween('date', [
                $refDate->copy()->startOfWeek(),
                $refDate->copy()->endOfWeek(),
            ]);
        } elseif ($filterType === 'monthly' && !empty($month)) {
            [$year, $selectedMonth] = array_pad(explode('-', $month), 2, null);
            $query->whereYear('date', $year ?: date('Y'))
                ->whereMonth('date', $selectedMonth ?: date('m'));
        } elseif ($filterType === 'custom') {
            if (!empty($startDate)) {
                $query->whereDate('date', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $query->whereDate('date', '<=', $endDate);
            }
        }

        $attendance = (clone $query)->orderBy('attendance_id', 'DESC')->get();

        $baseCountQuery = Attendance::query();
        if (!$isSuperAdmin) {
            $baseCountQuery->where('user_id', $user->id);
        } else {
            if ($request->filled('user_id') && !empty($request->input('user_id'))) {
                $baseCountQuery->where('user_id', $request->input('user_id'));
            }
        }
        if ($request->filled('status') && $request->input('status') !== '' && $request->input('status') !== null) {
            $baseCountQuery->where('status', $request->input('status'));
        }
        if ($request->filled('checkin_checkout') && !empty($request->input('checkin_checkout'))) {
            $cc = $request->input('checkin_checkout');
            if ($cc === 'checked_in') {
                $baseCountQuery->whereNotNull('check_in');
            } elseif ($cc === 'checked_out') {
                $baseCountQuery->whereNotNull('check_out');
            } elseif ($cc === 'not_checked_out') {
                $baseCountQuery->whereNotNull('check_in')->whereNull('check_out');
            }
        }
        if ($request->filled('check_in_time') && trim((string) $request->input('check_in_time')) !== '') {
            $inTime = trim((string) $request->input('check_in_time'));
            $baseCountQuery->where(function ($q) use ($inTime) {
                $q->where('check_in', 'LIKE', "%{$inTime}%")
                  ->orWhereRaw("TIME_FORMAT(check_in, '%H:%i') = ?", [$inTime]);
            });
        }
        if ($request->filled('check_out_time') && trim((string) $request->input('check_out_time')) !== '') {
            $outTime = trim((string) $request->input('check_out_time'));
            $baseCountQuery->where(function ($q) use ($outTime) {
                $q->where('check_out', 'LIKE', "%{$outTime}%")
                  ->orWhereRaw("TIME_FORMAT(check_out, '%H:%i') = ?", [$outTime]);
            });
        }

        if ($filterType === 'daily' && !empty($date)) {
            $baseCountQuery->whereDate('date', $date);
        } elseif ($filterType === 'weekly') {
            $refDate = !empty($startDate) ? Carbon::parse($startDate) : Carbon::today();
            $baseCountQuery->whereBetween('date', [
                $refDate->copy()->startOfWeek(),
                $refDate->copy()->endOfWeek(),
            ]);
        } elseif ($filterType === 'monthly' && !empty($month)) {
            [$year, $selectedMonth] = array_pad(explode('-', $month), 2, null);
            $baseCountQuery->whereYear('date', $year ?: date('Y'))
                ->whereMonth('date', $selectedMonth ?: date('m'));
        } elseif ($filterType === 'custom') {
            if (!empty($startDate)) {
                $baseCountQuery->whereDate('date', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $baseCountQuery->whereDate('date', '<=', $endDate);
            }
        }

        $totalAttendance = (clone $baseCountQuery)->count();
        $presentCount    = (clone $baseCountQuery)->whereIn('status', ['Present', 'Auto'])->count();
        $staffCount      = (clone $baseCountQuery)->distinct('user_id')->count('user_id');

        return response()->json([
            'status'           => true,
            'can_manage_all'   => $isSuperAdmin,
            'total_attendance' => $totalAttendance,
            'present_count'    => $presentCount,
            'staff_count'      => $staffCount,
            'data'             => $attendance
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action. Super Admin privileges required to add attendance for staff.'], 403);
        }

        $validated = $request->validate([
            'user_id'          => ['required', 'exists:users,id'],
            'date'             => ['required', 'date'],
            'check_in'         => ['required'],
            'check_out'        => ['nullable'],
            'permission_start' => ['nullable'],
            'permission_end'   => ['nullable'],
            'second_check_in'  => ['nullable'],
            'second_check_out' => ['nullable'],
            'status'           => ['nullable', 'in:Auto,Present,Late,Half Day,Absent,On Leave'],
        ]);

        $targetUser = User::find($validated['user_id']);
        $status = $this->determineAttendanceStatus($targetUser, $validated['check_in'], $validated['status'] ?? 'Auto');
        $workingHours = $this->calculateWorkingHours(
            $validated['check_in'],
            $validated['check_out'] ?? null,
            $validated['second_check_in'] ?? null,
            $validated['second_check_out'] ?? null
        );

        $attendance = Attendance::create([
            'user_id'          => $validated['user_id'],
            'date'             => $validated['date'],
            'check_in'         => $validated['check_in'],
            'check_out'        => $validated['check_out'] ?? null,
            'permission_start' => $validated['permission_start'] ?? null,
            'permission_end'   => $validated['permission_end'] ?? null,
            'second_check_in'  => $validated['second_check_in'] ?? null,
            'second_check_out' => $validated['second_check_out'] ?? null,
            'working_hours'    => $workingHours,
            'status'           => $status,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Attendance recorded successfully.',
            'data'    => $attendance
        ]);
    }

    public function edit($id)
    {
        $attendance = Attendance::with('user:id,name,email,check_in_time,check_out_time,allow_check_in_time')->find($id);
        if (!$attendance) {
            return response()->json([
                'status'  => false,
                'message' => 'Attendance record not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $attendance
        ]);
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::find($id);
        if (!$attendance) {
            return response()->json([
                'status'  => false,
                'message' => 'Attendance record not found.'
            ], 404);
        }

        $validated = $request->validate([
            'user_id'          => ['required', 'exists:users,id'],
            'date'             => ['required', 'date'],
            'check_in'         => ['required'],
            'check_out'        => ['nullable'],
            'permission_start' => ['nullable'],
            'permission_end'   => ['nullable'],
            'second_check_in'  => ['nullable'],
            'second_check_out' => ['nullable'],
            'status'           => ['nullable', 'in:Auto,Present,Late,Half Day,Absent,On Leave'],
        ]);

        $user = User::find($validated['user_id']);
        $status = $this->determineAttendanceStatus($user, $validated['check_in'], $validated['status'] ?? 'Auto');
        $workingHours = $this->calculateWorkingHours(
            $validated['check_in'],
            $validated['check_out'] ?? null,
            $validated['second_check_in'] ?? null,
            $validated['second_check_out'] ?? null
        );

        $attendance->update([
            'user_id'          => $validated['user_id'],
            'date'             => $validated['date'],
            'check_in'         => $validated['check_in'],
            'check_out'        => $validated['check_out'] ?? null,
            'permission_start' => $validated['permission_start'] ?? null,
            'permission_end'   => $validated['permission_end'] ?? null,
            'second_check_in'  => $validated['second_check_in'] ?? null,
            'second_check_out' => $validated['second_check_out'] ?? null,
            'working_hours'    => $workingHours,
            'status'           => $status,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Attendance updated successfully.',
            'data'    => $attendance
        ]);
    }

    /**
     * Mark self attendance (Check In / Check Out) for logged-in user with multi-session support
     */
    public function markSelfAttendance(Request $request)
    {
        $user = auth()->user();
        $type = $request->input('type'); // 'check_in' or 'check_out'
        $today = Carbon::now()->toDateString();
        $nowTime = Carbon::now()->format('H:i:s');

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        // Check for approved permission request for user today
        $approvedPermission = \App\Models\PermissionRequest::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->where('status', 'Approved')
            ->first();

        if ($type === 'check_in') {
            if (!$attendance) {
                // Session 1 Check-In
                $status = $this->determineAttendanceStatus($user, $nowTime, 'Auto');

                $createData = [
                    'user_id'       => $user->id,
                    'date'          => $today,
                    'check_in'      => $nowTime,
                    'check_out'     => null,
                    'working_hours' => null,
                    'status'        => $status,
                ];

                if ($approvedPermission) {
                    $createData['permission_start'] = $approvedPermission->start_time;
                    $createData['permission_end']   = $approvedPermission->end_time;
                    $createData['permission_id']    = $approvedPermission->id;
                }

                $attendance = Attendance::create($createData);

                return response()->json([
                    'status'  => true,
                    'message' => "Checked in for Session 1 at {$nowTime}. Status: {$status}",
                    'data'    => $attendance
                ]);
            } else {
                // Attendance record already exists for today
                if (empty($attendance->check_out)) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'You are currently checked in for Session 1.'
                    ], 422);
                }

                if (!empty($attendance->check_in) && !empty($attendance->check_out) && empty($attendance->second_check_in)) {
                    // Session 2 Check-In
                    $updateData = [
                        'second_check_in' => $nowTime,
                    ];

                    if ($approvedPermission && empty($attendance->permission_id)) {
                        $updateData['permission_start'] = $approvedPermission->start_time;
                        $updateData['permission_end']   = $approvedPermission->end_time;
                        $updateData['permission_id']    = $approvedPermission->id;
                    }

                    $attendance->update($updateData);

                    return response()->json([
                        'status'  => true,
                        'message' => "Checked in for Session 2 at {$nowTime}.",
                        'data'    => $attendance
                    ]);
                }

                if (!empty($attendance->second_check_in) && empty($attendance->second_check_out)) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'You are currently checked in for Session 2.'
                    ], 422);
                }

                if (!empty($attendance->second_check_out)) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'You have already completed all work sessions for today.'
                    ], 422);
                }
            }
        } elseif ($type === 'check_out') {
            if (!$attendance) {
                return response()->json([
                    'status'  => false,
                    'message' => 'You need to check in before checking out.'
                ], 422);
            }

            if (!empty($attendance->second_check_in) && empty($attendance->second_check_out)) {
                // Session 2 Check-Out
                $workingHours = $this->calculateWorkingHours($attendance->check_in, $attendance->check_out, $attendance->second_check_in, $nowTime);
                $attendance->update([
                    'second_check_out' => $nowTime,
                    'working_hours'    => $workingHours,
                ]);

                return response()->json([
                    'status'  => true,
                    'message' => "Checked out for Session 2 at {$nowTime}.",
                    'data'    => $attendance
                ]);
            } elseif (!empty($attendance->check_in) && empty($attendance->check_out)) {
                // Session 1 Check-Out
                $workingHours = $this->calculateWorkingHours($attendance->check_in, $nowTime);
                $attendance->update([
                    'check_out'     => $nowTime,
                    'working_hours' => $workingHours,
                ]);

                return response()->json([
                    'status'  => true,
                    'message' => "Checked out for Session 1 at {$nowTime}.",
                    'data'    => $attendance
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'No active check-in session found to check out.'
                ], 422);
            }
        }

        return response()->json(['status' => false, 'message' => 'Invalid action.'], 400);
    }

    /**
     * Compare actual check-in time against staff reference allow_check_in_time or check_in_time
     */
    private function determineAttendanceStatus($user, $actualCheckIn, $requestedStatus = 'Auto')
    {
        if ($requestedStatus && !in_array($requestedStatus, ['Auto', ''])) {
            return $requestedStatus;
        }

        $allowTime = $user ? ($user->allow_check_in_time ?? $user->check_in_time) : null;
        if (!$allowTime) {
            return 'Present';
        }

        try {
            $assignedIn = Carbon::parse($allowTime)->format('H:i:s');
            $actualIn = Carbon::parse($actualCheckIn)->format('H:i:s');

            if ($actualIn > $assignedIn) {
                return 'Late';
            }

            return 'Present';
        } catch (\Exception $e) {
            return 'Present';
        }
    }

    public function destroy($id)
    {
        $attendance = Attendance::find($id);
        if (!$attendance) {
            return response()->json([
                'status'  => false,
                'message' => 'Attendance record not found.'
            ], 404);
        }

        $attendance->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Attendance deleted successfully.'
        ]);
    }

    /**
     * Requirement: Attendance Report View
     */
    public function report(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->reportData($request);
        }

        $user = auth()->user();
        $data['staffs'] = $user->isSuperAdmin()
            ? User::staffOnly()->orderBy('name')->get()
            : User::where('id', $user->id)->get();

        return view('attendance.report', $data);
    }

    /**
     * Requirement: Attendance Report Data with Daily, Weekly, Monthly & Custom filters
     * Includes Late Attendance Count and Salary Deduction calculation logic.
     */
    public function reportData(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();
        $salaryCalculator = app(SalaryCalculationService::class);

        $filterType = $request->input('filter_type');
        $userId     = $request->input('user_id');
        $date       = $request->input('date', date('Y-m-d'));
        $month      = $request->input('month', date('Y-m'));
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');

        // Requirement: For a logged-in non-Super Admin staff member, restrict user_id to logged-in user and default to current month
        if (!$isSuperAdmin) {
            $userId = $user->id;
            if (empty($filterType)) {
                $filterType = 'monthly';
                $month = date('Y-m'); // e.g. August 2026
            }
        } else {
            if (empty($filterType)) {
                $filterType = 'daily';
            }
        }

        $query = Attendance::with('user:id,name,email,base_salary,check_in_time,check_out_time,allow_check_in_time,late_attendance_count');

        if (!empty($userId)) {
            $query->where('user_id', $userId);
        }

        if ($filterType === 'daily') {
            $query->whereDate('date', $date);
        } elseif ($filterType === 'weekly') {
            $refDate   = !empty($startDate) ? Carbon::parse($startDate) : Carbon::today();
            $weekStart = $refDate->copy()->startOfWeek();
            $weekEnd   = $refDate->copy()->endOfWeek();
            $query->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()]);
        } elseif ($filterType === 'monthly') {
            $parts = explode('-', $month);
            $y = $parts[0] ?? date('Y');
            $m = $parts[1] ?? date('m');
            $query->whereYear('date', $y)->whereMonth('date', $m);
        } elseif ($filterType === 'custom') {
            if (!empty($startDate) && !empty($endDate)) {
                $query->whereBetween('date', [$startDate, $endDate]);
            } elseif (!empty($startDate)) {
                $query->whereDate('date', '>=', $startDate);
            } elseif (!empty($endDate)) {
                $query->whereDate('date', '<=', $endDate);
            }
        }

        // Fetch all matching records sorted chronologically by user and date ASC for late count calculation
        $allRecords = $query->orderBy('user_id', 'ASC')->orderBy('date', 'ASC')->get();

        // Calculate late attendance & salary deduction per user
        $userLateCounts = [];
        $totalLateDeductions = 0;

        foreach ($allRecords as $rec) {
            $staff = $rec->user;
            $uId = $rec->user_id;

            if (!isset($userLateCounts[$uId])) {
                $userLateCounts[$uId] = 0;
            }

            $rawAllowTime = ($staff && $staff->allow_check_in_time) ? $staff->allow_check_in_time : (($staff && $staff->check_in_time) ? $staff->check_in_time : '09:10:00');
            $allowTimeFormatted = Carbon::parse($rawAllowTime)->format('h:i A');
            $allowTime24 = Carbon::parse($rawAllowTime)->format('H:i:s');
            $allowedLateCount = (int) ($staff->late_attendance_count ?? 3);

            $actualCheckIn24 = !empty($rec->check_in) ? Carbon::parse($rec->check_in)->format('H:i:s') : null;
            $actualCheckInFormatted = !empty($rec->check_in) ? Carbon::parse($rec->check_in)->format('h:i A') : '-';

            $isLate = false;
            $lateDurationMins = 0;
            $deductionAmount = 0.00;
            $isExceeded = false;
            $lateCountStatus = 'On Time';

            if ($actualCheckIn24 && $actualCheckIn24 > $allowTime24) {
                $isLate = true;
                $userLateCounts[$uId]++;
                $currentLateCount = $userLateCounts[$uId];

                // Calculate late duration in minutes
                $inTimeSeconds = strtotime($actualCheckIn24);
                $allowTimeSeconds = strtotime($allowTime24);
                $lateDurationMins = max(0, round(($inTimeSeconds - $allowTimeSeconds) / 60));

                if ($currentLateCount > $allowedLateCount) {
                    $isExceeded = true;
                    $lateCountStatus = "Late #{$currentLateCount} ({$currentLateCount}/{$allowedLateCount})";

                    // Calculate deduction rate based on base salary
                    $baseSalary = (float) ($staff->base_salary ?? 0);
                    $recDate = Carbon::parse($rec->date);
                    $totalDaysInMonth = $recDate->daysInMonth;

                    // Calculate working days in month (excluding Sundays)
                    $sundays = 0;
                    for ($d = 1; $d <= $totalDaysInMonth; $d++) {
                        if (Carbon::createFromDate($recDate->year, $recDate->month, $d)->isSunday()) {
                            $sundays++;
                        }
                    }
                    $workingDays = max(1, $totalDaysInMonth - $sundays);
                    $perDaySalary = $workingDays > 0 ? ($baseSalary / $workingDays) : 0;

                    // Calculate daily working minutes from assigned check_in and check_out time, default 480 mins (8 hrs)
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

                    $perMinuteSalary = $perDaySalary / $dailyMins;
                    $deductionAmount = round($lateDurationMins * $perMinuteSalary, 2);
                } else {
                    $lateCountStatus = "Late #{$currentLateCount} ({$currentLateCount}/{$allowedLateCount})";
                }
            }

            // Check for linked or approved permission request for user on date
            $permReq = $rec->permissionRequest;
            if (!$permReq && empty($rec->permission_start)) {
                $permReq = \App\Models\PermissionRequest::where('user_id', $rec->user_id)
                    ->whereDate('date', $rec->date)
                    ->where('status', 'Approved')
                    ->first();
            }

            if ($permReq && empty($rec->permission_start)) {
                $rec->permission_start = $permReq->start_time;
                $rec->permission_end   = $permReq->end_time;
                $rec->permission_id    = $permReq->id;
            }

            // Session 1 breakdown
            $s1CheckIn  = !empty($rec->check_in) ? Carbon::parse($rec->check_in)->format('h:i A') : '-';
            $s1CheckOut = !empty($rec->check_out) ? Carbon::parse($rec->check_out)->format('h:i A') : '-';
            $rec->session_1 = ($rec->check_in) ? "{$s1CheckIn} → {$s1CheckOut}" : '-';

            // Permission breakdown & duration
            $pStart = !empty($rec->permission_start) ? Carbon::parse($rec->permission_start)->format('h:i A') : null;
            $pEnd   = !empty($rec->permission_end) ? Carbon::parse($rec->permission_end)->format('h:i A') : null;
            $rec->permission_period = ($pStart && $pEnd) ? "{$pStart} → {$pEnd}" : '-';

            $pDurationMins = 0;
            if ($pStart && $pEnd) {
                try {
                    $pIn = Carbon::parse($rec->permission_start);
                    $pOut = Carbon::parse($rec->permission_end);
                    if ($pOut->greaterThan($pIn)) {
                        $pDurationMins = $pIn->diffInMinutes($pOut);
                    }
                } catch (\Exception $e) {}
            }
            $pHours = floor($pDurationMins / 60);
            $pMins  = $pDurationMins % 60;
            $rec->permission_duration = $pDurationMins > 0 ? ($pHours > 0 ? "{$pHours} hrs " : "") . ($pMins > 0 ? "{$pMins} mins" : "") : '-';

            // Session 2 breakdown
            $s2CheckIn  = !empty($rec->second_check_in) ? Carbon::parse($rec->second_check_in)->format('h:i A') : '-';
            $s2CheckOut = !empty($rec->second_check_out) ? Carbon::parse($rec->second_check_out)->format('h:i A') : '-';
            $rec->session_2 = ($rec->second_check_in) ? "{$s2CheckIn} → {$s2CheckOut}" : '-';

            // Total working hours
            $computedWorkedHours = $this->calculateWorkingHours($rec->check_in, $rec->check_out, $rec->second_check_in, $rec->second_check_out);
            if ($computedWorkedHours) {
                $rec->working_hours = $computedWorkedHours;
            }

            $recDate = Carbon::parse($rec->date);
            $rec->actual_work_finished_time = ($staff && $staff->check_out_time)
                ? Carbon::parse($staff->check_out_time)->format('h:i A')
                : '-';
            $rec->ot_minutes = $salaryCalculator->otMinutes($rec, $staff);
            $rec->ot_income = $salaryCalculator->otIncome(
                $rec,
                $staff,
                $salaryCalculator->workingDaysInMonth($recDate)
            );

            $rec->allowed_check_in_time = $allowTimeFormatted;
            $rec->actual_check_in_formatted = $actualCheckInFormatted;
            $rec->late_duration_minutes = $lateDurationMins;
            $rec->late_count_status = $lateCountStatus;
            $rec->is_allowed_count_exceeded = $isExceeded;
            $rec->salary_deduction = $deductionAmount;

            $totalLateDeductions += $deductionAmount;
        }

        // Sort records by date DESC for display
        $records = $allRecords->sortByDesc('date')->values();

        // Calculate KPI Summaries
        $totalPresent  = $records->where('status', 'Present')->count();
        $totalLate     = $records->where('status', 'Late')->count();
        $totalHalfDay  = $records->where('status', 'Half Day')->count();
        $totalAbsent   = $records->where('status', 'Absent')->count();
        $totalOnLeave  = $records->where('status', 'On Leave')->count();

        // Calculate Total Working Hours across all work sessions
        $totalMinutes = 0;
        foreach ($records as $rec) {
            if (!empty($rec->check_in) && !empty($rec->check_out)) {
                try {
                    $in  = Carbon::parse($rec->check_in);
                    $out = Carbon::parse($rec->check_out);
                    if ($out->lessThan($in)) {
                        $out->addDay();
                    }
                    $totalMinutes += $in->diffInMinutes($out);
                } catch (\Exception $e) {}
            }
            if (!empty($rec->second_check_in) && !empty($rec->second_check_out)) {
                try {
                    $in2  = Carbon::parse($rec->second_check_in);
                    $out2 = Carbon::parse($rec->second_check_out);
                    if ($out2->lessThan($in2)) {
                        $out2->addDay();
                    }
                    $totalMinutes += $in2->diffInMinutes($out2);
                } catch (\Exception $e) {}
            }
        }

        $totalHours   = floor($totalMinutes / 60);
        $remMinutes   = $totalMinutes % 60;
        $totalHrsText = $totalHours . ' hrs' . ($remMinutes > 0 ? " {$remMinutes} mins" : '');

        $summary = [
            'total_records'        => $records->count(),
            'total_present'        => $totalPresent,
            'total_late'           => $totalLate,
            'total_half_day'       => $totalHalfDay,
            'total_absent'         => $totalAbsent,
            'total_on_leave'       => $totalOnLeave,
            'total_working_hours'  => $totalHrsText,
            'total_late_deduction' => round($totalLateDeductions, 2),
        ];

        return response()->json([
            'status'  => true,
            'summary' => $summary,
            'data'    => $records
        ]);
    }

    private function calculateWorkingHours($checkIn, $checkOut, $secondCheckIn = null, $secondCheckOut = null)
    {
        $totalMinutes = 0;

        if (!empty($checkIn) && !empty($checkOut)) {
            try {
                $in = Carbon::parse($checkIn);
                $out = Carbon::parse($checkOut);
                if ($out->lessThan($in)) {
                    $out->addDay();
                }
                $totalMinutes += $in->diffInMinutes($out);
            } catch (\Exception $e) {}
        }

        if (!empty($secondCheckIn) && !empty($secondCheckOut)) {
            try {
                $in2 = Carbon::parse($secondCheckIn);
                $out2 = Carbon::parse($secondCheckOut);
                if ($out2->lessThan($in2)) {
                    $out2->addDay();
                }
                $totalMinutes += $in2->diffInMinutes($out2);
            } catch (\Exception $e) {}
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
