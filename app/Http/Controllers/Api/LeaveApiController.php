<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\AdminLeaveRequestReceived;
use App\Notifications\LeaveQuotaCompleted;
use App\Notifications\LeaveRequestApproved;
use App\Notifications\LeaveRequestRejected;
use App\Notifications\LeaveRequestSubmitted;
use App\Traits\HasApiPermissionCheck;
use App\Traits\SendsPushNotifications;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LeaveApiController extends Controller
{
    use HasApiPermissionCheck, SendsPushNotifications;

    /**
     * Dedicated endpoint returning all form metadata, options, and defaults for "Apply Leave Request" in mobile app.
     * Accessible via GET /api/v1/leave-requests/form-data (or /api/v1/leaves/form-data)
     */
    public function getFormData(Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'leaves.view') && !$this->hasPermission($currentUser, 'leaves.create')) {
            return $this->permissionDeniedResponse('Leave module');
        }

        $isSuperAdmin = $currentUser->isSuperAdmin();
        $isTemporary = ($currentUser->staff_type === 'Temporary');

        $canManageStaffLeaves = $isSuperAdmin 
            || $currentUser->isAdmin() 
            || $currentUser->can('leaves.approve') 
            || $currentUser->hasRole(['manager', 'Manager']);

        // Staff options: Super Admin, Managers, and Approvers can select any active staff member; regular staff can only select themselves
        if ($canManageStaffLeaves) {
            $staffOptions = User::staffOnly()
                ->where('status', 1)
                ->orderBy('name', 'asc')
                ->get(['id', 'name', 'email', 'profile_image'])
                ->map(function ($u) {
                    $emailSuffix = !empty($u->email) ? " ({$u->email})" : "";
                    return [
                        'id'            => (int) $u->id,
                        'value'         => (int) $u->id,
                        'name'          => $u->name,
                        'email'         => $u->email,
                        'label'         => $u->name . $emailSuffix,
                        'profile_image' => $u->profile_image_url,
                    ];
                })->values();
        } else {
            $emailSuffix = !empty($currentUser->email) ? " ({$currentUser->email})" : "";
            $staffOptions = collect([[
                'id'            => (int) $currentUser->id,
                'value'         => (int) $currentUser->id,
                'name'          => $currentUser->name,
                'email'         => $currentUser->email,
                'label'         => $currentUser->name . $emailSuffix,
                'profile_image' => $currentUser->profile_image_url,
            ]]);
        }

        $leaveTypes = [
            'Casual Leave',
            'Sick Leave',
            'Earned Leave',
            'Paid Leave',
            'Unpaid Leave',
        ];

        $today = Carbon::today()->toDateString();
        $todayFormatted = Carbon::today()->format('d-m-Y');

        $canCreate  = !$isTemporary && ($this->hasPermission($currentUser, 'leaves.create'));
        $canApprove = $currentUser->can('leaves.approve') || $currentUser->isAdmin() || $isSuperAdmin;
        $canDelete  = $currentUser->can('leaves.delete') || $currentUser->isAdmin() || $isSuperAdmin;

        return response()->json([
            'status'  => true,
            'message' => 'Leave form metadata retrieved successfully.',
            'data'    => [
                'staff_options' => $staffOptions,
                'leave_types'   => $leaveTypes,
                'current_user'  => [
                    'id'                    => (int) $currentUser->id,
                    'name'                  => $currentUser->name,
                    'email'                 => $currentUser->email,
                    'staff_type'            => $currentUser->staff_type,
                    'is_temporary'          => $isTemporary,
                    'available_leave_count' => (float) ($currentUser->available_leave_count ?? 0),
                    'profile_image'         => $currentUser->profile_image_url,
                ],
                'permissions' => [
                    'can_create'     => $canCreate,
                    'can_approve'    => $canApprove,
                    'can_delete'     => $canDelete,
                    'is_super_admin' => $isSuperAdmin,
                ],
                'defaults' => [
                    'from_date'           => $today,
                    'to_date'             => $today,
                    'from_date_formatted' => $todayFormatted,
                    'to_date_formatted'   => $todayFormatted,
                    'number_of_days'      => 1,
                    'leave_type'          => 'Casual Leave',
                    'reason'              => '',
                ],
                'notice' => $isTemporary ? 'Temporary staff are not permitted to submit leave requests.' : null,
            ],
        ]);
    }

    /**
     * List leave requests with KPI summary counters, search, and filters.
     * Accessible via GET /api/v1/leave-requests (or /api/v1/leaves)
     */
    public function index(Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'leaves.view')) {
            return $this->permissionDeniedResponse('Leave module');
        }

        $isSuperAdmin = $currentUser->isSuperAdmin();
        $canManageStaffLeaves = $isSuperAdmin 
            || $currentUser->isAdmin() 
            || $currentUser->can('leaves.approve') 
            || $currentUser->hasRole(['manager', 'Manager']);

        $canApprove   = $currentUser->can('leaves.approve') || $currentUser->isAdmin() || $isSuperAdmin;
        $canDelete    = $currentUser->can('leaves.delete') || $currentUser->isAdmin() || $isSuperAdmin;
        $canCreate    = ($currentUser->staff_type !== 'Temporary') && ($this->hasPermission($currentUser, 'leaves.create'));

        $query = LeaveRequest::with([
            'user:id,name,email,profile_image',
            'approver:id,name,email',
        ]);

        // Base user scoping: Managers/Admins/Super Admins see all staff requests; normal staff see only their own
        if (!$canManageStaffLeaves) {
            $query->where('user_id', $currentUser->id);
        } else {
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->input('user_id'));
            }
        }

        // Compute KPI Summary statistics (Total, Pending, Approved, Rejected) based on user's scope
        $statsQuery = LeaveRequest::query();
        if (!$canManageStaffLeaves) {
            $statsQuery->where('user_id', $currentUser->id);
        } else {
            if ($request->filled('user_id')) {
                $statsQuery->where('user_id', $request->input('user_id'));
            }
        }

        $totalRequests    = (clone $statsQuery)->count();
        $pendingRequests  = (clone $statsQuery)->where('status', 'Pending')->count();
        $approvedRequests = (clone $statsQuery)->where('status', 'Approved')->count();
        $rejectedRequests = (clone $statsQuery)->where('status', 'Rejected')->count();

        // Status Filter ('Pending', 'Approved', 'Rejected')
        if ($request->filled('status') && strtolower(trim((string) $request->input('status'))) !== 'all') {
            $statusVal = ucfirst(strtolower(trim((string) $request->input('status'))));
            $query->where('status', $statusVal);
        }

        // Leave Type Filter
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->input('leave_type'));
        }

        // Search Filter (Staff name, email, reason, leave_type)
        $search = trim((string) ($request->input('search') ?? $request->input('q') ?? $request->input('keyword') ?? ''));
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhere('leave_type', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Date Range Filters
        if ($request->filled('from_date')) {
            $query->whereDate('from_date', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('to_date', '<=', $request->input('to_date'));
        }

        // Month Filter (e.g. '2026-09')
        if ($request->filled('month')) {
            try {
                $carbonMonth = Carbon::parse($request->input('month') . '-01');
                $startOfMonth = $carbonMonth->copy()->startOfMonth()->toDateString();
                $endOfMonth   = $carbonMonth->copy()->endOfMonth()->toDateString();

                $query->whereDate('from_date', '<=', $endOfMonth)
                      ->whereDate('to_date', '>=', $startOfMonth);
            } catch (\Exception $e) {
                // Ignore parse failure
            }
        }

        $perPage = (int) ($request->input('per_page', 20));
        if ($perPage <= 0) {
            $perPage = 20;
        }

        $leaves = $query->orderBy('id', 'desc')->paginate($perPage);

        $formattedData = collect($leaves->items())->map(function ($leave) use ($currentUser, $canApprove, $canDelete) {
            return $this->formatLeaveRecord($leave, $currentUser, $canApprove, $canDelete);
        })->values();

        return response()->json([
            'status'     => true,
            'message'    => 'Leave requests retrieved successfully.',
            'statistics' => [
                'total'    => $totalRequests,
                'pending'  => $pendingRequests,
                'approved' => $approvedRequests,
                'rejected' => $rejectedRequests,
            ],
            'permissions' => [
                'can_create'  => $canCreate,
                'can_approve' => $canApprove,
                'can_delete'  => $canDelete,
            ],
            'data'       => $formattedData,
            'pagination' => [
                'total'        => $leaves->total(),
                'per_page'     => $leaves->perPage(),
                'current_page' => $leaves->currentPage(),
                'last_page'    => $leaves->lastPage(),
            ],
        ]);
    }

    /**
     * Submit a new leave request.
     * Accessible via POST /api/v1/leave-requests (or /api/v1/leaves)
     */
    public function store(Request $request): JsonResponse
    {

        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'leaves.create')) {
            return $this->permissionDeniedResponse('create Leave request');
        }

        $validator = Validator::make($request->all(), [
            'user_id'        => ['nullable', 'exists:users,id'],
            'from_date'      => ['required', 'date'],
            'to_date'        => ['required', 'date', 'after_or_equal:from_date'],
            'number_of_days' => ['required', 'numeric', 'min:0.5'],
            'leave_type'     => ['required', 'string', 'max:50'],
            'reason'         => ['required', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $canManageStaffLeaves = $currentUser->isSuperAdmin() 
            || $currentUser->isAdmin() 
            || $currentUser->can('leaves.approve') 
            || $currentUser->hasRole(['manager', 'Manager']);

        $targetUserId = $currentUser->id;
        if (!empty($validated['user_id']) && $canManageStaffLeaves) {
            $targetUserId = (int) $validated['user_id'];
        }

        // Rule: If applicant is submitting for themselves and they are Temporary staff
        if ($targetUserId === $currentUser->id && $currentUser->staff_type === 'Temporary') {
            return response()->json([
                'status'  => false,
                'message' => 'Temporary staff members are not permitted to submit leave requests.',
            ], 403);
        }

        $targetUser = User::find($targetUserId);

        if (!$targetUser) {
            return response()->json(['status' => false, 'message' => 'Target staff member not found.'], 404);
        }

        if ($targetUser->staff_type === 'Temporary') {
            return response()->json([
                'status'  => false,
                'message' => 'Selected staff member has Temporary staff status and cannot apply for leave.',
            ], 422);
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

        // Dispatch notifications to target staff and Super Admins
        try {
            $fromDate  = Carbon::parse($leave->from_date)->format('d-m-Y');
            $toDate    = Carbon::parse($leave->to_date)->format('d-m-Y');
            $monthName = Carbon::parse($leave->from_date)->format('F Y');
            $days      = $leave->number_of_days;
            $staffName = $targetUser->name ?? 'Staff';
            $staffEmail = $targetUser->email ?? '';

            // 1. Notify target staff member about submission
            $targetUser->notify(new LeaveRequestSubmitted($leave));
            $this->sendPushNotification(
                $targetUser,
                'Leave Request Submitted',
                "Your leave request for {$fromDate} to {$toDate} ({$days} day(s)) [{$monthName}] has been submitted and is pending approval.",
                [
                    'leave_id' => (string) $leave->id,
                    'id'       => (string) $leave->id,
                    'status'   => (string) $leave->status,
                    'type'     => 'leave_request',
                ]
            );

            // 2. Notify Super Admins if request was submitted
            $superAdmins = User::where(function ($q) {
                $q->whereHas('roles', function ($rq) {
                    $rq->whereRaw('LOWER(name) IN (?, ?, ?)', ['super admin', 'super-admin', 'admin']);
                })->orWhere('id', 1);
            })
            ->where('id', '!=', $currentUser->id)
            ->get();

            foreach ($superAdmins as $admin) {
                $admin->notify(new AdminLeaveRequestReceived($leave, $targetUser));
                $this->sendPushNotification(
                    $admin,
                    'New Leave Request Pending Approval',
                    "{$staffName} ({$staffEmail}) submitted a leave request for {$fromDate} to {$toDate} ({$days} day(s)) pending approval.",
                    [
                        'leave_id' => (string) $leave->id,
                        'id'       => (string) $leave->id,
                        'status'   => (string) $leave->status,
                        'user_id'  => (string) $targetUser->id,
                        'type'     => 'leave_request',
                    ]
                );
            }

            // 3. Leave Quota check and notification (matching admin panel logic)
            $allowedLeaveDays = (float) ($targetUser->available_leave_count ?? 0);
            if ($allowedLeaveDays > 0) {
                $month = Carbon::parse($validated['from_date']);
                $usedLeaveDays = (float) LeaveRequest::where('user_id', $targetUser->id)
                    ->whereIn('status', ['Approved', 'Pending'])
                    ->whereDate('from_date', '<=', $month->copy()->endOfMonth())
                    ->whereDate('to_date', '>=', $month->copy()->startOfMonth())
                    ->sum('number_of_days');

                if ($usedLeaveDays >= $allowedLeaveDays) {
                    $targetUser->notify(new LeaveQuotaCompleted(
                        $month->format('F'),
                        round($usedLeaveDays, 2),
                        $allowedLeaveDays
                    ));
                    $this->sendPushNotification(
                        $targetUser,
                        'Leave Quota Alert',
                        "You have used " . round($usedLeaveDays, 2) . " of {$allowedLeaveDays} allowed leave days for " . $month->format('F') . ".",
                        [
                            'type'  => 'leave_quota',
                            'month' => $month->format('F'),
                        ]
                    );
                }
            }
        } catch (\Throwable $e) {
            Log::error('LeaveApiController: Error dispatching notifications: ' . $e->getMessage());
        }

        $leave->load(['user:id,name,email,profile_image', 'approver:id,name,email']);
        $canApprove = $currentUser->can('leaves.approve') || $currentUser->isAdmin() || $currentUser->isSuperAdmin();
        $canDelete  = $currentUser->can('leaves.delete') || $currentUser->isAdmin() || $currentUser->isSuperAdmin();

        return response()->json([
            'status'  => true,
            'message' => 'Leave request submitted successfully (Status: Pending).',
            'data'    => $this->formatLeaveRecord($leave, $currentUser, $canApprove, $canDelete),
        ], 201);
    }

    /**
     * View single leave request details.
     * Accessible via GET /api/v1/leave-requests/{id} (or /api/v1/leaves/{id})
     */
    public function show(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'leaves.view')) {
            return $this->permissionDeniedResponse('view Leave request');
        }

        $leave = LeaveRequest::with([
            'user:id,name,email,profile_image,staff_type,available_leave_count',
            'approver:id,name,email',
        ])->find($id);

        if (!$leave) {
            return response()->json(['status' => false, 'message' => 'Leave request not found.'], 404);
        }

        $canManageStaffLeaves = $currentUser->isSuperAdmin() 
            || $currentUser->isAdmin() 
            || $currentUser->can('leaves.approve') 
            || $currentUser->hasRole(['manager', 'Manager']);

        if (!$canManageStaffLeaves && $leave->user_id !== $currentUser->id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized. You can only view your own leave requests.'], 403);
        }

        $canApprove = $currentUser->can('leaves.approve') || $currentUser->isAdmin() || $currentUser->isSuperAdmin();
        $canDelete  = $currentUser->can('leaves.delete') || $currentUser->isAdmin() || $currentUser->isSuperAdmin();

        return response()->json([
            'status'  => true,
            'message' => 'Leave request details retrieved successfully.',
            'data'    => $this->formatLeaveRecord($leave, $currentUser, $canApprove, $canDelete),
        ]);
    }

    /**
     * Approve leave request (Admin action).
     * Accessible via POST /api/v1/leave-requests/approve/{id} (or /api/v1/leaves/approve/{id})
     */
    public function approve(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$currentUser->can('leaves.approve') && !$currentUser->hasRole(['Super Admin', 'Admin']) && !$currentUser->isAdmin() && !$currentUser->isSuperAdmin()) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action. Permission leaves.approve required.'], 403);
        }

        $leave = LeaveRequest::find($id);
        if (!$leave) {
            return response()->json(['status' => false, 'message' => 'Leave request not found.'], 404);
        }

        $leave->status = 'Approved';
        $leave->approved_by = $currentUser->id;
        if ($request->has('admin_remarks')) {
            $leave->admin_remarks = $request->input('admin_remarks');
        }
        $leave->save();

        // Update staff is_on_leave status if approved leave includes today
        $today = Carbon::today()->toDateString();
        $staff = User::find($leave->user_id);
        if ($leave->from_date && $leave->to_date) {
            $fromDateStr = Carbon::parse($leave->from_date)->toDateString();
            $toDateStr   = Carbon::parse($leave->to_date)->toDateString();
            if ($fromDateStr <= $today && $toDateStr >= $today) {
                if ($staff) {
                    $staff->is_on_leave = true;
                    $staff->save();
                }
            }
        }

        // Notify staff member that leave was approved
        try {
            if ($staff) {
                $fromDate  = Carbon::parse($leave->from_date)->format('d-m-Y');
                $toDate    = Carbon::parse($leave->to_date)->format('d-m-Y');
                $monthName = Carbon::parse($leave->from_date)->format('F Y');
                $days      = $leave->number_of_days;

                $staff->notify(new LeaveRequestApproved($leave));
                $this->sendPushNotification(
                    $staff,
                    'Leave Request Approved',
                    "Your leave request for {$fromDate} to {$toDate} ({$days} day(s)) [{$monthName}] has been approved by admin.",
                    [
                        'leave_id' => (string) $leave->id,
                        'id'       => (string) $leave->id,
                        'status'   => 'Approved',
                        'type'     => 'leave_request',
                    ]
                );
            }
        } catch (\Throwable $e) {
            Log::error('LeaveApiController: Error in approve notifications: ' . $e->getMessage());
        }

        $leave->load(['user:id,name,email,profile_image', 'approver:id,name,email']);
        $canApprove = true;
        $canDelete  = $currentUser->can('leaves.delete') || $currentUser->isAdmin() || $currentUser->isSuperAdmin();

        return response()->json([
            'status'  => true,
            'message' => 'Leave request approved successfully.',
            'data'    => $this->formatLeaveRecord($leave, $currentUser, $canApprove, $canDelete),
        ]);
    }

    /**
     * Reject leave request (Admin action).
     * Accessible via POST /api/v1/leave-requests/reject/{id} (or /api/v1/leaves/reject/{id})
     */
    public function reject(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$currentUser->can('leaves.approve') && !$currentUser->hasRole(['Super Admin', 'Admin']) && !$currentUser->isAdmin() && !$currentUser->isSuperAdmin()) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action. Permission leaves.approve required.'], 403);
        }

        $leave = LeaveRequest::find($id);
        if (!$leave) {
            return response()->json(['status' => false, 'message' => 'Leave request not found.'], 404);
        }

        $leave->status = 'Rejected';
        $leave->approved_by = $currentUser->id;
        if ($request->has('admin_remarks')) {
            $leave->admin_remarks = $request->input('admin_remarks');
        }
        $leave->save();

        // Notify staff member that leave was rejected
        try {
            $staff = User::find($leave->user_id);
            if ($staff) {
                $fromDate  = Carbon::parse($leave->from_date)->format('d-m-Y');
                $toDate    = Carbon::parse($leave->to_date)->format('d-m-Y');
                $monthName = Carbon::parse($leave->from_date)->format('F Y');
                $days      = $leave->number_of_days;

                $staff->notify(new LeaveRequestRejected($leave));
                $this->sendPushNotification(
                    $staff,
                    'Leave Request Rejected',
                    "Your leave request for {$fromDate} to {$toDate} ({$days} day(s)) [{$monthName}] has been rejected by admin.",
                    [
                        'leave_id' => (string) $leave->id,
                        'id'       => (string) $leave->id,
                        'status'   => 'Rejected',
                        'type'     => 'leave_request',
                    ]
                );
            }
        } catch (\Throwable $e) {
            Log::error('LeaveApiController: Error in reject notifications: ' . $e->getMessage());
        }

        $leave->load(['user:id,name,email,profile_image', 'approver:id,name,email']);
        $canApprove = true;
        $canDelete  = $currentUser->can('leaves.delete') || $currentUser->isAdmin() || $currentUser->isSuperAdmin();

        return response()->json([
            'status'  => true,
            'message' => 'Leave request rejected.',
            'data'    => $this->formatLeaveRecord($leave, $currentUser, $canApprove, $canDelete),
        ]);
    }

    /**
     * Delete leave request.
     * Accessible via DELETE /api/v1/leave-requests/delete/{id} (or /api/v1/leaves/delete/{id})
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$currentUser->can('leaves.delete') && !$currentUser->hasRole(['Super Admin', 'Admin']) && !$currentUser->isAdmin() && !$currentUser->isSuperAdmin()) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action. Permission leaves.delete required.'], 403);
        }

        $leave = LeaveRequest::find($id);
        if (!$leave) {
            return response()->json(['status' => false, 'message' => 'Leave request not found.'], 404);
        }

        $leave->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Leave request deleted successfully.',
        ]);
    }

    /**
     * Helper to format a LeaveRequest object specifically tailored to the mobile app screens.
     */
    private function formatLeaveRecord(LeaveRequest $leave, User $currentUser, bool $canApprove, bool $canDelete): array
    {
        $staffName  = $leave->user?->name ?? 'Staff';
        $staffEmail = $leave->user?->email ?? '';
        $initial    = strtoupper(substr(trim($staffName), 0, 1) ?: 'S');

        $fromDateCarbon = $leave->from_date ? Carbon::parse($leave->from_date) : null;
        $toDateCarbon   = $leave->to_date ? Carbon::parse($leave->to_date) : null;

        $fromDateFormatted = $fromDateCarbon ? $fromDateCarbon->format('d-m-Y') : '';
        $toDateFormatted   = $toDateCarbon ? $toDateCarbon->format('d-m-Y') : '';
        $dateRange         = "{$fromDateFormatted} to {$toDateFormatted}";

        $days = (float) $leave->number_of_days;
        $daysText = "{$days} day(s)";

        $badgeColor = match (strtolower($leave->status)) {
            'approved' => 'success',
            'rejected' => 'danger',
            default    => 'warning', // Pending
        };

        return [
            'id'                 => (int) $leave->id,
            'leave_id'           => (int) $leave->id,
            'user_id'            => (int) $leave->user_id,
            'staff_name'         => $staffName,
            'staff_email'        => $staffEmail,
            'staff_initial'      => $initial,
            'staff_avatar'       => $leave->user?->profile_image_url ?? asset('assets/img/avatars/1.png'),
            'leave_type'         => $leave->leave_type,
            'from_date'          => $fromDateCarbon ? $fromDateCarbon->toDateString() : null,
            'to_date'            => $toDateCarbon ? $toDateCarbon->toDateString() : null,
            'from_date_formatted'=> $fromDateFormatted,
            'to_date_formatted'  => $toDateFormatted,
            'date_range'         => $dateRange,
            'number_of_days'     => $days,
            'days_text'          => $daysText,
            'reason'             => $leave->reason ?? '',
            'status'             => $leave->status,
            'status_badge_color' => $badgeColor,
            'approved_by'        => $leave->approved_by ? (int) $leave->approved_by : null,
            'approver_name'      => $leave->approver?->name,
            'admin_remarks'      => $leave->admin_remarks,
            'created_at'         => $leave->created_at ? $leave->created_at->format('Y-m-d H:i:s') : null,
            'created_at_formatted'=> $leave->created_at ? $leave->created_at->format('d-m-Y h:i A') : null,
            'actions'            => [
                'can_approve' => $canApprove && ($leave->status === 'Pending'),
                'can_reject'  => $canApprove && ($leave->status === 'Pending'),
                'can_delete'  => $canDelete,
            ],
        ];
    }
}
