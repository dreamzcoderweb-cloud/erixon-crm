<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use App\Models\CallRecording;
use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CallLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData();
        }

        return view('call_logs.view', $this->formData());
    }

    public function listData()
    {
        $logs = CallLog::with($this->relations())
            ->orderBy('call_id', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $logs,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateCallLog($request);

        $log = CallLog::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Call log saved successfully.',
            'data'    => $log,
        ]);
    }

    public function edit($id)
    {
        $log = CallLog::with($this->relations())->find($id);

        if (!$log) {
            return response()->json([
                'status'  => false,
                'message' => 'Call log not found.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $log,
        ]);
    }

    public function update(Request $request, $id)
    {
        $log = CallLog::find($id);

        if (!$log) {
            return response()->json([
                'status'  => false,
                'message' => 'Call log not found.',
            ], 404);
        }

        $log->update($this->validateCallLog($request));

        return response()->json([
            'status'  => true,
            'message' => 'Call log updated successfully.',
            'data'    => $log,
        ]);
    }

    public function destroy($id)
    {
        $log = CallLog::find($id);

        if (!$log) {
            return response()->json([
                'status'  => false,
                'message' => 'Call log not found.',
            ], 404);
        }

        $log->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Call log deleted successfully.',
        ]);
    }

    public function report(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->reportData($request);
        }

        return view('call_logs.report', $this->formData());
    }

    public function reportData(Request $request)
    {
        $logs = $this->filteredReportQuery($request)
            ->with($this->relations())
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'status'  => true,
            'summary' => $this->summary($logs),
            'data'    => $logs,
        ]);
    }

    private function formData(): array
    {
        return [
            'leads'      => Lead::with('customer')->orderBy('lead_id', 'DESC')->get(),
            'staffs'     => User::staffOnly()->orderBy('name')->get(),
            'recordings' => CallRecording::with('lead.customer')->orderBy('call_id', 'DESC')->get(),
        ];
    }

    private function relations(): array
    {
        return [
            'lead:lead_id,lead_title,customer_id',
            'lead.customer:customer_id,name',
            'user:id,name,email',
            'recording:call_id,lead_id,duration,recording_file',
        ];
    }

    private function validateCallLog(Request $request): array
    {
        return $request->validate([
            'lead_id'      => ['nullable', 'exists:leads,lead_id'],
            'user_id'      => ['nullable', 'exists:users,id'],
            'phone'        => ['required', 'string', 'max:30'],
            'call_type'    => ['required', 'string', 'max:50'],
            'duration'     => ['nullable', 'string', 'max:100'],
            'call_status'  => ['required', 'string', 'max:100'],
            'recording_id' => ['nullable', 'exists:call_recordings,call_id'],
            'created_at'   => ['nullable', 'date'],
        ]);
    }

    private function filteredReportQuery(Request $request)
    {
        $filterType = $request->input('filter_type', 'daily');
        $date       = $request->input('date', date('Y-m-d'));
        $month      = $request->input('month', date('Y-m'));
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');

        $query = CallLog::query();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('lead_id')) {
            $query->where('lead_id', $request->input('lead_id'));
        }

        if ($request->filled('call_type')) {
            $query->where('call_type', $request->input('call_type'));
        }

        if ($request->filled('call_status')) {
            $query->where('call_status', $request->input('call_status'));
        }

        if ($filterType === 'daily') {
            $query->whereDate('created_at', $date);
        } elseif ($filterType === 'weekly') {
            $refDate = !empty($startDate) ? Carbon::parse($startDate) : Carbon::today();
            $query->whereBetween('created_at', [
                $refDate->copy()->startOfWeek(),
                $refDate->copy()->endOfWeek(),
            ]);
        } elseif ($filterType === 'monthly') {
            [$year, $selectedMonth] = array_pad(explode('-', $month), 2, null);
            $query->whereYear('created_at', $year ?: date('Y'))
                ->whereMonth('created_at', $selectedMonth ?: date('m'));
        } elseif ($filterType === 'custom') {
            if (!empty($startDate)) {
                $query->whereDate('created_at', '>=', $startDate);
            }

            if (!empty($endDate)) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }

        return $query;
    }

    private function summary($logs): array
    {
        return [
            'total_calls'     => $logs->count(),
            'inbound_calls'   => $logs->where('call_type', 'Inbound')->count(),
            'outbound_calls'  => $logs->where('call_type', 'Outbound')->count(),
            'missed_calls'    => $logs->filter(fn ($log) => in_array($log->call_status, ['Missed', 'No Answer']))->count(),
            'completed_calls' => $logs->where('call_status', 'Completed')->count(),
            'recorded_calls'  => $logs->whereNotNull('recording_id')->count(),
        ];
    }
}
