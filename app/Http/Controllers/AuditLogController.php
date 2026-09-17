<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    /**
     * Display the audit log page with KPI statistics and filter lists.
     */
    public function index(Request $request)
    {
        $today = Carbon::today();

        $totalLogs = AuditLog::count();
        $todayLogs = AuditLog::whereDate('created_at', $today)->count();

        // Top active user
        $topUserRecord = AuditLog::selectRaw('user_id, COUNT(*) as total')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->first();

        $topUser = null;
        if ($topUserRecord && $topUserRecord->user_id) {
            $user = User::find($topUserRecord->user_id);
            if ($user) {
                $topUser = [
                    'name'  => $user->name,
                    'count' => $topUserRecord->total,
                ];
            }
        }

        // Top modified module
        $topModuleRecord = AuditLog::selectRaw('module, COUNT(*) as total')
            ->whereNotNull('module')
            ->groupBy('module')
            ->orderByDesc('total')
            ->first();

        $topModule = $topModuleRecord ? [
            'name'  => $topModuleRecord->module,
            'count' => $topModuleRecord->total,
        ] : null;

        // Filter dropdown options
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        $modules = AuditLog::select('module')
            ->distinct()
            ->whereNotNull('module')
            ->orderBy('module')
            ->pluck('module');

        return view('audit_logs.index', compact(
            'totalLogs',
            'todayLogs',
            'topUser',
            'topModule',
            'users',
            'modules'
        ));
    }

    /**
     * Get JSON data for DataTables.
     */
    public function listData(Request $request): JsonResponse
    {
        $query = AuditLog::with(['user:id,name,email,profile_image'])
            ->orderBy('id', 'DESC');

        // Period filter
        if ($request->filled('filter_type')) {
            $query->filterPeriod(
                $request->filter_type,
                $request->date,
                $request->month,
                $request->start_date,
                $request->end_date
            );
        }

        // User filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Module filter
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Event filter
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('module', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $logs = $query->limit(500)->get();

        return response()->json([
            'status' => true,
            'data'   => $logs,
        ]);
    }

    /**
     * View detailed audit log item including side-by-side field diffs.
     */
    public function show($id): JsonResponse
    {
        $log = AuditLog::with(['user:id,name,email,profile_image'])->find($id);

        if (!$log) {
            return response()->json([
                'status'  => false,
                'message' => 'Audit log not found.',
            ], 404);
        }

        $oldValues = $log->old_values ?? [];
        $newValues = $log->new_values ?? [];

        // Build list of all changed keys
        $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
        $diff = [];

        foreach ($allKeys as $key) {
            $old = $oldValues[$key] ?? null;
            $new = $newValues[$key] ?? null;

            // Format array or objects
            if (is_array($old) || is_object($old)) {
                $old = json_encode($old, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            }
            if (is_array($new) || is_object($new)) {
                $new = json_encode($new, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            }

            $diff[] = [
                'field'      => $key,
                'old'        => $old,
                'new'        => $new,
                'is_changed' => ($old !== $new),
            ];
        }

        return response()->json([
            'status' => true,
            'data'   => [
                'log'  => $log,
                'diff' => $diff,
            ],
        ]);
    }

    /**
     * Export filtered audit logs as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = AuditLog::with(['user:id,name,email'])->orderBy('id', 'DESC');

        if ($request->filled('filter_type')) {
            $query->filterPeriod(
                $request->filter_type,
                $request->date,
                $request->month,
                $request->start_date,
                $request->end_date
            );
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        $fileName = 'audit_logs_' . date('Y_m_d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                'Log ID',
                'Date & Time',
                'User / Actor',
                'User Email',
                'Event / Action',
                'Module',
                'Description',
                'IP Address',
                'URL',
            ]);

            $query->chunk(200, function ($logs) use ($handle) {
                foreach ($logs as $log) {
                    fputcsv($handle, [
                        $log->id,
                        $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : 'N/A',
                        $log->user?->name ?? 'System / Unknown',
                        $log->user?->email ?? 'N/A',
                        strtoupper($log->event),
                        $log->module,
                        $log->description,
                        $log->ip_address ?? 'N/A',
                        $log->url ?? 'N/A',
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
