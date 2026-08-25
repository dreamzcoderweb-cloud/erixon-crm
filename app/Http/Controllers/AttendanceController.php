<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData($request);
        }

        $data['staffs'] = User::staffOnly()->orderBy('name')->get();
        $data['myTodayAttendance'] = Attendance::where('user_id', auth()->id())
            ->whereDate('date', date('Y-m-d'))
            ->first();

        return view('attendance.view', $data);
    }

    public function listData(Request $request = null)
    {
        $request = $request ?? request();
        $user = auth()->user();
        $canManageAll = $user->can('leaves.approve') || $user->hasRole(['Super Admin', 'Admin', 'super admin', 'super-admin']);

        $query = Attendance::with('user:id,name,email,check_in_time,check_out_time');

        // Non-admin staff can only see their own attendance records
        if (!$canManageAll) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
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

        if ($request->filled('check_in_time')) {
            $inTime = $request->input('check_in_time');
            $query->where(function ($q) use ($inTime) {
                $q->where('check_in', 'LIKE', "%{$inTime}%")
                  ->orWhereRaw("TIME_FORMAT(check_in, '%H:%i') = ?", [$inTime]);
            });
        }

        if ($request->filled('check_out_time')) {
            $outTime = $request->input('check_out_time');
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
        if (!$canManageAll) {
            $baseCountQuery->where('user_id', $user->id);
        }
        if ($request->filled('user_id')) {
            $baseCountQuery->where('user_id', $request->input('user_id'));
        }
        if ($request->filled('status') && $request->input('status') !== '') {
            $baseCountQuery->where('status', $request->input('status'));
        }
        if ($request->filled('checkin_checkout')) {
            $cc = $request->input('checkin_checkout');
            if ($cc === 'checked_in') {
                $baseCountQuery->whereNotNull('check_in');
            } elseif ($cc === 'checked_out') {
                $baseCountQuery->whereNotNull('check_out');
            } elseif ($cc === 'not_checked_out') {
                $baseCountQuery->whereNotNull('check_in')->whereNull('check_out');
            }
        }
        if ($request->filled('check_in_time')) {
            $inTime = $request->input('check_in_time');
            $baseCountQuery->where(function ($q) use ($inTime) {
                $q->where('check_in', 'LIKE', "%{$inTime}%")
                  ->orWhereRaw("TIME_FORMAT(check_in, '%H:%i') = ?", [$inTime]);
            });
        }
        if ($request->filled('check_out_time')) {
            $outTime = $request->input('check_out_time');
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
            'can_manage_all'   => $canManageAll,
            'total_attendance' => $totalAttendance,
            'present_count'    => $presentCount,
            'staff_count'      => $staffCount,
            'data'             => $attendance
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->can('leaves.approve') && !$user->hasRole(['Super Admin', 'Admin', 'super admin', 'super-admin'])) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action. Admin privileges required to add attendance for staff.'], 403);
        }

        $validated = $request->validate([
            'user_id'   => ['required', 'exists:users,id'],
            'date'      => ['required', 'date'],
            'check_in'  => ['required'],
            'check_out' => ['nullable'],
            'status'    => ['nullable', 'in:Auto,Present,Late,Half Day,Absent,On Leave'],
        ]);

        $targetUser = User::find($validated['user_id']);
        $status = $this->determineAttendanceStatus($targetUser, $validated['check_in'], $validated['status'] ?? 'Auto');
        $workingHours = $this->calculateWorkingHours($validated['check_in'], $validated['check_out'] ?? null);

        $attendance = Attendance::create([
            'user_id'       => $validated['user_id'],
            'date'          => $validated['date'],
            'check_in'      => $validated['check_in'],
            'check_out'     => $validated['check_out'] ?? null,
            'working_hours' => $workingHours,
            'status'        => $status,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Attendance recorded successfully.',
            'data'    => $attendance
        ]);
    }

    public function edit($id)
    {
        $attendance = Attendance::with('user:id,name,email,check_in_time,check_out_time')->find($id);
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
            'user_id'   => ['required', 'exists:users,id'],
            'date'      => ['required', 'date'],
            'check_in'  => ['required'],
            'check_out' => ['nullable'],
            'status'    => ['nullable', 'in:Auto,Present,Late,Half Day,Absent,On Leave'],
        ]);

        $user = User::find($validated['user_id']);
        $status = $this->determineAttendanceStatus($user, $validated['check_in'], $validated['status'] ?? 'Auto');
        $workingHours = $this->calculateWorkingHours($validated['check_in'], $validated['check_out'] ?? null);

        $attendance->update([
            'user_id'       => $validated['user_id'],
            'date'          => $validated['date'],
            'check_in'      => $validated['check_in'],
            'check_out'     => $validated['check_out'] ?? null,
            'working_hours' => $workingHours,
            'status'        => $status,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Attendance updated successfully.',
            'data'    => $attendance
        ]);
    }

    /**
     * Mark self attendance (Check In / Check Out) for logged-in user
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

        if ($type === 'check_in') {
            if ($attendance) {
                return response()->json([
                    'status'  => false,
                    'message' => 'You have already checked in today at ' . $attendance->check_in
                ], 422);
            }

            $status = $this->determineAttendanceStatus($user, $nowTime, 'Auto');

            $attendance = Attendance::create([
                'user_id'       => $user->id,
                'date'          => $today,
                'check_in'      => $nowTime,
                'check_out'     => null,
                'working_hours' => null,
                'status'        => $status,
            ]);

            return response()->json([
                'status'  => true,
                'message' => "Checked in successfully at {$nowTime}. Status: {$status}",
                'data'    => $attendance
            ]);
        } elseif ($type === 'check_out') {
            if (!$attendance) {
                return response()->json([
                    'status'  => false,
                    'message' => 'You need to check in before checking out.'
                ], 422);
            }

            $workingHours = $this->calculateWorkingHours($attendance->check_in, $nowTime);

            $attendance->update([
                'check_out'     => $nowTime,
                'working_hours' => $workingHours,
            ]);

            return response()->json([
                'status'  => true,
                'message' => "Checked out successfully at {$nowTime}.",
                'data'    => $attendance
            ]);
        }

        return response()->json(['status' => false, 'message' => 'Invalid action.'], 400);
    }

    /**
     * Compare actual check-in time against staff reference check_in_time
     */
    private function determineAttendanceStatus($user, $actualCheckIn, $requestedStatus = 'Auto')
    {
        if ($requestedStatus && !in_array($requestedStatus, ['Auto', ''])) {
            return $requestedStatus;
        }

        if (!$user || !$user->check_in_time) {
            return 'Present';
        }

        try {
            $assignedIn = Carbon::parse($user->check_in_time)->format('H:i:s');
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

        $data['staffs'] = User::staffOnly()->orderBy('name')->get();

        return view('attendance.report', $data);
    }

    /**
     * Requirement: Attendance Report Data with Daily, Weekly, Monthly & Custom filters
     */
    public function reportData(Request $request)
    {
        $filterType = $request->input('filter_type', 'daily');
        $userId     = $request->input('user_id');
        $date       = $request->input('date', date('Y-m-d'));
        $month      = $request->input('month', date('Y-m'));
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');

        $query = Attendance::with('user:id,name,email');

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

        $records = $query->orderBy('date', 'DESC')->get();

        // Calculate KPI Summaries
        $totalPresent  = $records->where('status', 'Present')->count();
        $totalLate     = $records->where('status', 'Late')->count();
        $totalHalfDay  = $records->where('status', 'Half Day')->count();
        $totalAbsent   = $records->where('status', 'Absent')->count();
        $totalOnLeave  = $records->where('status', 'On Leave')->count();

        // Calculate Total Working Hours
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
        }

        $totalHours   = floor($totalMinutes / 60);
        $remMinutes   = $totalMinutes % 60;
        $totalHrsText = $totalHours . ' hrs' . ($remMinutes > 0 ? " {$remMinutes} mins" : '');

        $summary = [
            'total_records'       => $records->count(),
            'total_present'       => $totalPresent,
            'total_late'          => $totalLate,
            'total_half_day'      => $totalHalfDay,
            'total_absent'        => $totalAbsent,
            'total_on_leave'      => $totalOnLeave,
            'total_working_hours' => $totalHrsText,
        ];

        return response()->json([
            'status'  => true,
            'summary' => $summary,
            'data'    => $records
        ]);
    }

    private function calculateWorkingHours($checkIn, $checkOut)
    {
        if (empty($checkIn) || empty($checkOut)) {
            return null;
        }

        try {
            $in = Carbon::parse($checkIn);
            $out = Carbon::parse($checkOut);

            if ($out->lessThan($in)) {
                $out->addDay();
            }

            $diffMinutes = $in->diffInMinutes($out);
            $hours = floor($diffMinutes / 60);
            $minutes = $diffMinutes % 60;

            if ($minutes > 0) {
                return "{$hours} hrs {$minutes} mins";
            }

            return "{$hours} hrs";
        } catch (\Exception $e) {
            return null;
        }
    }
}
