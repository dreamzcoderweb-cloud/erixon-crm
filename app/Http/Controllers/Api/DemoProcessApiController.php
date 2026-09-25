<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DemoProcess;
use App\Models\DemoProcessCustomField;
use App\Models\Lead;
use App\Models\LeadRequirement;
use App\Models\LeadSetting;
use App\Models\LeadSource;
use App\Models\User;
use App\Notifications\DemoProcessCreated;
use App\Notifications\DemoProcessFinished;
use App\Notifications\DemoProcessPending;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Traits\HasApiPermissionCheck;

class DemoProcessApiController extends Controller
{
    use HasApiPermissionCheck;
    /**
     * Get dropdown data, custom fields, and configuration for Demo Process form.
     * GET /api/v1/demo-processes/form-data
     */
    public function getFormData(Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'demo-processes.view')) {
            return response()->json(['status' => false, 'message' => 'Access denied. You do not have permission to view Demo Process.'], 403);
        }

        // Active Staff List
        $staffList = User::where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email', 'mobile_number', 'designation']);

        if ($staffList->isEmpty()) {
            $staffList = User::orderBy('name', 'asc')->get(['id', 'name', 'email', 'mobile_number', 'designation']);
        }

        // Product Managers (Assigned By options)
        $productManagers = User::whereHas('roles', function ($q) {
            $q->where('name', 'like', '%Product Manager%')
              ->orWhere('name', 'like', '%Super Admin%')
              ->orWhere('name', 'like', '%Admin%');
        })->orWhere('id', 1)->orderBy('name', 'asc')->get(['id', 'name', 'email', 'designation']);

        if ($productManagers->isEmpty()) {
            $productManagers = $staffList;
        }

        // Support Team (Sub Assigned By options)
        $supportTeam = User::whereHas('roles', function ($q) {
            $q->where('name', 'like', '%Support%')
              ->orWhere('name', 'like', '%Product Manager%')
              ->orWhere('name', 'like', '%Super Admin%')
              ->orWhere('name', 'like', '%Admin%');
        })->orWhere('id', 1)->orderBy('name', 'asc')->get(['id', 'name', 'email', 'designation']);

        if ($supportTeam->isEmpty()) {
            $supportTeam = $staffList;
        }

        // Lead Sources
        $leadSources = LeadSource::orderBy('name', 'asc')
            ->get(['lead_sources_id as id', 'name']);

        // Customers list
        $customers = Customer::orderBy('name', 'asc')
            ->get(['customer_id as id', 'name', 'mobile', 'email']);

        // Products / Lead Requirements
        $leadRequirements = LeadRequirement::orderBy('name', 'asc')
            ->get(['lead_requirements_id as id', 'name']);

        // Custom Fields
        $customFields = DemoProcessCustomField::where('status', 1)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $isSalesTeam = $this->isSalesTeamUser($currentUser);

        return response()->json([
            'status' => true,
            'data' => [
                'staff_list'        => $staffList,
                'product_managers'  => $productManagers,
                'support_team'      => $supportTeam,
                'lead_sources'      => $leadSources,
                'customers'         => $customers,
                'lead_requirements' => $leadRequirements,
                'products'          => $leadRequirements,
                'custom_fields'     => $customFields,
                'customer_types'    => [
                    'New Customer',
                    'Existing Customer',
                    'Referral',
                    'Corporate',
                    'Individual',
                ],
                'status_options'    => ['Pending', 'Finished'],
                'is_sales_team'     => $isSalesTeam,
            ],
        ]);
    }

    /**
     * Get paginated list of Demo Processes with filtering and search.
     * GET /api/v1/demo-processes
     */
    public function index(Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'demo-processes.view')) {
            return response()->json(['status' => false, 'message' => 'Access denied. You do not have permission to access Demo Process.'], 403);
        }

        // Scoped to current user (Admin sees all, Staff sees created/assigned/sub-assigned)
        $query = DemoProcess::forUser($currentUser)
            ->with([
                'creator:id,name,email',
                'assignedUser:id,name,email',
                'subAssignedUser:id,name,email',
                'leadSource:lead_sources_id,name',
                'leadRequirement:lead_requirements_id,name',
            ]);

        // Status filter
        if ($request->filled('status') && strtolower($request->input('status')) !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // Creator filter
        if ($request->filled('created_by')) {
            $query->where('created_by', $request->input('created_by'));
        }

        // Assigned PM filter
        if ($request->filled('assigned_by')) {
            $query->where('assigned_by', $request->input('assigned_by'));
        }

        // Sub-assigned Support filter
        if ($request->filled('sub_assigned_by')) {
            $query->where('sub_assigned_by', $request->input('sub_assigned_by'));
        }

        // Lead Requirement / Product filter
        if ($request->filled('lead_requirement_id')) {
            $query->where('lead_requirement_id', $request->input('lead_requirement_id'));
        }

        // Search query
        if ($request->filled('search')) {
            $s = trim($request->input('search'));
            $query->where(function ($q) use ($s) {
                $q->where('customer_name', 'LIKE', "%{$s}%")
                  ->orWhere('customer_phone', 'LIKE', "%{$s}%")
                  ->orWhere('remarks', 'LIKE', "%{$s}%")
                  ->orWhereHas('leadRequirement', function ($rq) use ($s) {
                      $rq->where('name', 'LIKE', "%{$s}%");
                  });
            });
        }

        // Date Range filters (supports from_date/to_date, start_date/end_date, date, or month)
        $rawStart = $request->input('start_date') ?? $request->input('from_date');
        $rawEnd   = $request->input('end_date') ?? $request->input('to_date');
        $rawDate  = $request->input('date');
        $month    = $request->input('month');

        if (!empty($rawDate)) {
            $query->where(function ($q) use ($rawDate) {
                $q->whereDate('demo_date', $rawDate)
                  ->orWhereDate('created_at', $rawDate);
            });
        } elseif (!empty($rawStart) && !empty($rawEnd)) {
            $query->where(function ($q) use ($rawStart, $rawEnd) {
                $q->whereBetween('demo_date', [$rawStart, $rawEnd])
                  ->orWhereBetween('created_at', [$rawStart . ' 00:00:00', $rawEnd . ' 23:59:59']);
            });
        } elseif (!empty($rawStart)) {
            $query->where(function ($q) use ($rawStart) {
                $q->whereDate('demo_date', '>=', $rawStart)
                  ->orWhereDate('created_at', '>=', $rawStart);
            });
        } elseif (!empty($rawEnd)) {
            $query->where(function ($q) use ($rawEnd) {
                $q->whereDate('demo_date', '<=', $rawEnd)
                  ->orWhereDate('created_at', '<=', $rawEnd);
            });
        } elseif (!empty($month)) {
            [$year, $selectedMonth] = array_pad(explode('-', $month), 2, null);
            $y = $year ?: date('Y');
            $m = $selectedMonth ?: date('m');
            $query->where(function ($q) use ($y, $m) {
                $q->where(function ($sub) use ($y, $m) {
                    $sub->whereYear('demo_date', $y)->whereMonth('demo_date', $m);
                })->orWhere(function ($sub) use ($y, $m) {
                    $sub->whereYear('created_at', $y)->whereMonth('created_at', $m);
                });
            });
        }

        $perPage = max(1, min((int) ($request->input('per_page', 15)), 100));
        $paginated = $query->orderBy('demo_process_id', 'desc')->paginate($perPage);

        // Transform items
        $paginated->getCollection()->transform(function ($dp) {
            return $this->formatDemoProcessItem($dp);
        });

        // Summary counts for current user
        $summaryQuery = DemoProcess::forUser($currentUser);
        $totalDemos = (clone $summaryQuery)->count();
        $pendingDemos = (clone $summaryQuery)->where('status', 'Pending')->count();
        $finishedDemos = (clone $summaryQuery)->where('status', 'Finished')->count();

        return response()->json([
            'status'  => true,
            'message' => 'Demo processes fetched successfully.',
            'summary' => [
                'total'    => $totalDemos,
                'pending'  => $pendingDemos,
                'finished' => $finishedDemos,
            ],
            'data'    => $paginated,
        ]);
    }

    /**
     * Store a new Demo Process.
     * POST /api/v1/demo-processes
     */
    public function store(Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'demo-processes.create')) {
            return $this->permissionDeniedResponse('create Demo Process');
        }

        $rules = [
            'customer_name'       => 'required|string|max:255',
            'customer_phone'      => 'required|string|max:30',
            'lead_requirement_id' => 'nullable|exists:lead_requirements,lead_requirements_id',
            'lead_source_id'      => 'nullable|exists:lead_sources,lead_sources_id',
            'demo_date'           => 'required|date',
            'demo_time'           => 'required|string',
            'customer_type'       => 'nullable|string|max:100',
            'assigned_by'         => 'nullable|exists:users,id',
            'sub_assigned_by'     => 'nullable|exists:users,id',
            'remarks'             => 'nullable|string',
            'status'              => 'nullable|in:Pending,Finished',
            'custom_fields'       => 'nullable|array',
        ];

        // Required custom fields validation
        $requiredCustomFields = DemoProcessCustomField::where('status', 1)->where('is_required', 'Yes')->get();
        $customAttributes = [];
        foreach ($requiredCustomFields as $rcf) {
            $rules["custom_fields.{$rcf->field_name}"] = 'required';
            $customAttributes["custom_fields.{$rcf->field_name}"] = $rcf->field_label;
        }

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($customAttributes);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $isSalesTeam = $this->isSalesTeamUser($currentUser);

        // Auto-resolve product / lead requirement from customer phone if omitted
        $leadReqId = $request->filled('lead_requirement_id') ? $request->input('lead_requirement_id') : null;
        if (!$leadReqId && $request->filled('customer_phone')) {
            $matchedLead = Lead::whereHas('customer', function ($q) use ($request) {
                $q->where('mobile', $request->input('customer_phone'));
            })->latest('lead_id')->first();
            if ($matchedLead && $matchedLead->lead_requirement_id) {
                $leadReqId = $matchedLead->lead_requirement_id;
            }
        }

        $demoProcess = DemoProcess::create([
            'customer_name'       => trim($request->input('customer_name')),
            'customer_phone'      => trim($request->input('customer_phone')),
            'lead_requirement_id' => $leadReqId,
            'lead_source_id'      => $request->filled('lead_source_id') ? $request->input('lead_source_id') : null,
            'demo_date'           => $request->input('demo_date'),
            'demo_time'           => $request->input('demo_time'),
            'customer_type'       => $request->input('customer_type'),
            'created_by'          => $currentUser->id,
            'assigned_by'         => $request->filled('assigned_by') ? $request->input('assigned_by') : null,
            'sub_assigned_by'     => $isSalesTeam ? null : ($request->filled('sub_assigned_by') ? $request->input('sub_assigned_by') : null),
            'status'              => $request->input('status') ?? 'Pending',
            'remarks'             => $request->input('remarks'),
            'custom_fields'       => $request->input('custom_fields'),
        ]);

        // Send notifications
        $this->sendDemoNotifications($demoProcess, 'created');

        $fresh = $demoProcess->fresh(['creator', 'assignedUser', 'subAssignedUser', 'leadSource', 'leadRequirement']);

        return response()->json([
            'status'  => true,
            'message' => 'Demo Process created successfully.',
            'data'    => $this->formatDemoProcessItem($fresh),
        ], 201);
    }

    /**
     * Get single Demo Process record details.
     * GET /api/v1/demo-processes/{id}
     */
    public function show($id, Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'demo-processes.view')) {
            return response()->json(['status' => false, 'message' => 'Access denied. You do not have permission to view Demo Process.'], 403);
        }

        $demoProcess = DemoProcess::forUser($currentUser)
            ->with(['creator', 'assignedUser', 'subAssignedUser', 'leadSource', 'leadRequirement'])
            ->find($id);

        if (!$demoProcess) {
            return response()->json([
                'status'  => false,
                'message' => 'Demo Process record not found or access denied.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $this->formatDemoProcessItem($demoProcess),
        ]);
    }

    /**
     * Get Demo Process record formatted for Edit.
     * GET /api/v1/demo-processes/edit/{id}
     */
    public function edit($id, Request $request): JsonResponse
    {
        return $this->show($id, $request);
    }

    /**
     * Update an existing Demo Process.
     * POST /api/v1/demo-processes/update/{id} (or PUT /api/v1/demo-processes/{id})
     */
    public function update(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'demo-processes.edit')) {
            return response()->json(['status' => false, 'message' => 'Access denied. You do not have permission to edit Demo Process.'], 403);
        }

        $demoProcess = DemoProcess::forUser($currentUser)->find($id);
        if (!$demoProcess) {
            return response()->json([
                'status'  => false,
                'message' => 'Demo Process record not found or access denied.',
            ], 404);
        }

        $rules = [
            'customer_name'       => 'required|string|max:255',
            'customer_phone'      => 'required|string|max:30',
            'lead_requirement_id' => 'nullable|exists:lead_requirements,lead_requirements_id',
            'lead_source_id'      => 'nullable|exists:lead_sources,lead_sources_id',
            'demo_date'           => 'required|date',
            'demo_time'           => 'required|string',
            'customer_type'       => 'nullable|string|max:100',
            'assigned_by'         => 'nullable|exists:users,id',
            'sub_assigned_by'     => 'nullable|exists:users,id',
            'status'              => 'required|in:Pending,Finished',
            'remarks'             => 'nullable|string',
            'custom_fields'       => 'nullable|array',
        ];

        // Required custom fields validation
        $requiredCustomFields = DemoProcessCustomField::where('status', 1)->where('is_required', 'Yes')->get();
        $customAttributes = [];
        foreach ($requiredCustomFields as $rcf) {
            $rules["custom_fields.{$rcf->field_name}"] = 'required';
            $customAttributes["custom_fields.{$rcf->field_name}"] = $rcf->field_label;
        }

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($customAttributes);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $oldStatus = $demoProcess->status;
        $newStatus = $request->input('status');
        $isSalesTeam = $this->isSalesTeamUser($currentUser);

        $demoProcess->update([
            'customer_name'       => trim($request->input('customer_name')),
            'customer_phone'      => trim($request->input('customer_phone')),
            'lead_requirement_id' => $request->filled('lead_requirement_id') ? $request->input('lead_requirement_id') : null,
            'lead_source_id'      => $request->filled('lead_source_id') ? $request->input('lead_source_id') : null,
            'demo_date'           => $request->input('demo_date'),
            'demo_time'           => $request->input('demo_time'),
            'customer_type'       => $request->input('customer_type'),
            'assigned_by'         => $request->filled('assigned_by') ? $request->input('assigned_by') : null,
            'sub_assigned_by'     => $isSalesTeam ? $demoProcess->sub_assigned_by : ($request->filled('sub_assigned_by') ? $request->input('sub_assigned_by') : null),
            'status'              => $newStatus,
            'remarks'             => $request->input('remarks'),
            'custom_fields'       => $request->input('custom_fields'),
        ]);

        if ($oldStatus !== 'Finished' && $newStatus === 'Finished') {
            $this->sendDemoNotifications($demoProcess, 'finished');
        }

        $fresh = $demoProcess->fresh(['creator', 'assignedUser', 'subAssignedUser', 'leadSource', 'leadRequirement']);

        return response()->json([
            'status'  => true,
            'message' => 'Demo Process updated successfully.',
            'data'    => $this->formatDemoProcessItem($fresh),
        ]);
    }

    /**
     * Toggle or update Demo Process status (Pending / Finished).
     * POST /api/v1/demo-processes/change-status/{id}
     */
    public function changeStatus(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'demo-processes.edit')) {
            return response()->json(['status' => false, 'message' => 'Access denied. You do not have permission to edit Demo Process.'], 403);
        }

        $demoProcess = DemoProcess::forUser($currentUser)->find($id);
        if (!$demoProcess) {
            return response()->json([
                'status'  => false,
                'message' => 'Demo Process record not found or access denied.',
            ], 404);
        }

        $newStatus = $request->input('status');
        if (!in_array($newStatus, ['Pending', 'Finished'])) {
            $newStatus = ($demoProcess->status === 'Finished') ? 'Pending' : 'Finished';
        }

        $oldStatus = $demoProcess->status;
        $demoProcess->update(['status' => $newStatus]);

        if ($oldStatus !== 'Finished' && $newStatus === 'Finished') {
            $this->sendDemoNotifications($demoProcess, 'finished');
        }

        return response()->json([
            'status'     => true,
            'message'    => "Demo Process status updated to {$newStatus}.",
            'new_status' => $newStatus,
            'data'       => $this->formatDemoProcessItem($demoProcess->fresh(['creator', 'assignedUser', 'subAssignedUser', 'leadSource', 'leadRequirement'])),
        ]);
    }

    /**
     * Delete (soft-delete) a Demo Process record.
     * DELETE /api/v1/demo-processes/delete/{id} (or DELETE /api/v1/demo-processes/{id})
     */
    public function destroy($id, Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'demo-processes.delete')) {
            return response()->json(['status' => false, 'message' => 'Access denied. You do not have permission to delete Demo Process.'], 403);
        }

        $demoProcess = DemoProcess::forUser($currentUser)->find($id);
        if (!$demoProcess) {
            return response()->json([
                'status'  => false,
                'message' => 'Demo Process record not found or access denied.',
            ], 404);
        }

        $demoProcess->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Demo Process deleted successfully.',
        ]);
    }

    /**
     * Format a DemoProcess item consistently for API consumption.
     */
    private function formatDemoProcessItem(DemoProcess $dp): array
    {
        $tz = config('app.timezone', 'Asia/Kolkata');
        $reqName = $dp->leadRequirement ? $dp->leadRequirement->name : ($dp->leadSource ? $dp->leadSource->name : 'N/A');

        $formattedTime = $dp->demo_time;
        if (!empty($dp->demo_time)) {
            try {
                $formattedTime = Carbon::parse($dp->demo_time)->format('h:i A');
            } catch (\Exception $e) {}
        }

        return [
            'demo_process_id'     => (int) $dp->demo_process_id,
            'customer_name'       => $dp->customer_name,
            'customer_phone'      => $dp->customer_phone,
            'lead_source_id'      => $dp->lead_source_id ? (int) $dp->lead_source_id : null,
            'lead_source'         => $dp->leadSource ? $dp->leadSource->name : 'N/A',
            'lead_requirement_id' => $dp->lead_requirement_id ? (int) $dp->lead_requirement_id : null,
            'lead_requirement'    => $reqName,
            'product_name'        => $reqName,
            'demo_date'           => $dp->demo_date ? $dp->demo_date->format('Y-m-d') : null,
            'demo_date_formatted' => $dp->demo_date ? $dp->demo_date->format('d M Y') : 'N/A',
            'demo_time'           => $dp->demo_time,
            'demo_time_formatted' => $formattedTime,
            'customer_type'       => $dp->customer_type ?? 'N/A',
            'created_by'          => $dp->created_by ? (int) $dp->created_by : null,
            'creator_name'        => $dp->creator ? $dp->creator->name : 'N/A',
            'assigned_by'         => $dp->assigned_by ? (int) $dp->assigned_by : null,
            'assigned_user_name'  => $dp->assignedUser ? $dp->assignedUser->name : 'Unassigned',
            'sub_assigned_by'     => $dp->sub_assigned_by ? (int) $dp->sub_assigned_by : null,
            'sub_assigned_user_name' => $dp->subAssignedUser ? $dp->subAssignedUser->name : 'Unassigned',
            'status'              => $dp->status,
            'status_color'        => strtolower($dp->status) === 'finished' ? 'success' : 'warning',
            'remarks'             => $dp->remarks,
            'custom_fields'       => $dp->custom_fields ?? [],
            'created_at'          => $dp->created_at ? $dp->created_at->copy()->setTimezone($tz)->format('Y-m-d H:i:s') : null,
            'created_at_formatted'=> $dp->created_at ? $dp->created_at->copy()->setTimezone($tz)->format('d M Y, h:i A') : '',
        ];
    }

    /**
     * Send targeted notifications to involved users (creator, PM, Support, Super Admin).
     */
    private function sendDemoNotifications(DemoProcess $demoProcess, string $type)
    {
        try {
            $demoProcess->loadMissing('leadRequirement', 'leadSource', 'creator');

            $recipientIds = array_values(array_filter(array_unique([
                $demoProcess->created_by,
                $demoProcess->assigned_by,
                $demoProcess->sub_assigned_by,
                1, // Super Admin
            ])));

            $recipients = User::whereIn('id', $recipientIds)->get()->unique('id');

            foreach ($recipients as $recipient) {
                if ($type === 'created') {
                    $recipient->notify(new DemoProcessCreated($demoProcess));
                    $recipient->notify(new DemoProcessPending($demoProcess));
                } elseif ($type === 'finished') {
                    $recipient->notify(new DemoProcessFinished($demoProcess));
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error sending demo process notifications: ' . $e->getMessage());
        }
    }

    /**
     * Check if user belongs to sales team role.
     */
    protected function isSalesTeamUser($user = null): bool
    {
        $user = $user ?? Auth::user();
        if (!$user || $user->isSuperAdmin()) {
            return false;
        }

        return $user->hasRole('sales team') || $user->roles->contains(function ($r) {
            return strtolower($r->name) === 'sales team';
        });
    }
}
