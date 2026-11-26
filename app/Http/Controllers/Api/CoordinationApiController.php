<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coordination;
use App\Models\CoordinationJoiningStaff;
use App\Models\User;
use App\Notifications\CoordinationCreatedNotification;
use App\Notifications\CoordinationAssignedNotification;
use App\Notifications\CoordinationJoinedNotification;
use App\Traits\HasApiPermissionCheck;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CoordinationApiController extends Controller
{
    use HasApiPermissionCheck;

    /**
     * Get form metadata for Coordination (staff options, current user).
     * GET /api/v1/coordinations/form-data
     */
    public function getFormData(Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'coordinations.view')) {
            return $this->permissionDeniedResponse('Coordination module');
        }

        $staffs = User::where('status', 1)->orderBy('name', 'asc')->get(['id', 'name', 'email', 'profile_image']);

        $staffOptions = $staffs->map(function ($s) {
            return [
                'id'            => (int) $s->id,
                'value'         => (int) $s->id,
                'name'          => $s->name,
                'email'         => $s->email,
                'label'         => $s->name . ($s->email ? " ({$s->email})" : ''),
                'profile_image' => $s->profile_image_url ?? asset('assets/img/avatars/1.png'),
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Coordination form metadata retrieved successfully.',
            'data'    => [
                'staff_options' => $staffOptions,
                'current_user'  => [
                    'id'    => (int) $currentUser->id,
                    'name'  => $currentUser->name,
                    'email' => $currentUser->email,
                ],
                'permissions' => [
                    'can_create' => $this->hasPermission($currentUser, 'coordinations.create'),
                    'can_edit'   => $this->hasPermission($currentUser, 'coordinations.edit'),
                    'can_delete' => $this->hasPermission($currentUser, 'coordinations.delete'),
                ],
            ],
        ]);
    }

    /**
     * List coordinations with filters, search, and pagination.
     * GET /api/v1/coordinations
     */
    public function index(Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'coordinations.view')) {
            return $this->permissionDeniedResponse('Coordination module');
        }

        $query = Coordination::query();

        // 1. Scoping rule: By default, ONLY return the logged-in staff's coordinations (staff_id or created_by)
        $scope = strtolower(trim((string)($request->input('scope') ?? $request->input('type') ?? '')));

        if ($currentUser->isAdmin() || $currentUser->isSuperAdmin()) {
            if ($request->filled('created_by') || $request->filled('staff_id')) {
                $filterStaffId = $request->input('created_by') ?? $request->input('staff_id');
                $query->where(function ($q) use ($filterStaffId) {
                    $q->where('staff_id', $filterStaffId)
                      ->orWhere('created_by', $filterStaffId);
                });
            } elseif ($request->boolean('all', false) || $scope === 'all') {
                // Return all coordinations for admin only when explicitly requested
            } else {
                // Default in API: strictly the logged-in user's coordinations
                $query->where(function ($q) use ($currentUser) {
                    $q->where('staff_id', $currentUser->id)
                      ->orWhere('created_by', $currentUser->id);
                });
            }
        } else {
            // Normal Staff: strictly only their own coordination
            if ($scope === 'joined') {
                $query->whereHas('joiningStaff', function ($jq) use ($currentUser) {
                    $jq->where('users.id', $currentUser->id);
                });
            } else {
                // Default: ONLY the logged-in staff's coordination
                $query->where(function ($q) use ($currentUser) {
                    $q->where('staff_id', $currentUser->id)
                      ->orWhere('created_by', $currentUser->id);
                });
            }
        }

        // Date filters (matching web CoordinationController)
        $filterType = $request->input('filter_type');
        $date       = $request->input('date');
        $month      = $request->input('month');
        $startDate  = $request->input('start_date') ?? $request->input('from_date');
        $endDate    = $request->input('end_date') ?? $request->input('to_date');

        if ($filterType === 'daily' && !empty($date)) {
            $query->whereDate('created_at', $date);
        } elseif ($filterType === 'weekly') {
            $refDate = !empty($startDate) ? Carbon::parse($startDate) : Carbon::today();
            $query->whereBetween('created_at', [
                $refDate->copy()->startOfWeek(),
                $refDate->copy()->endOfWeek(),
            ]);
        } elseif ($filterType === 'monthly' && !empty($month)) {
            [$year, $selectedMonth] = array_pad(explode('-', $month), 2, null);
            $query->whereYear('created_at', $year ?: date('Y'))
                  ->whereMonth('created_at', $selectedMonth ?: date('m'));
        } elseif ($filterType === 'custom' || (!empty($startDate) && !empty($endDate))) {
            if (!empty($startDate)) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        } elseif (!empty($date)) {
            $query->whereDate('created_at', $date);
        }

        // Search Filter (Title, Link, Staff Name)
        $search = trim((string)($request->input('search') ?? $request->input('q') ?? ''));
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('link', 'LIKE', "%{$search}%")
                  ->orWhereHas('staff', function ($sq) use ($search) {
                      $sq->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $query->with([
            'staff:id,name,email,profile_image',
            'creator:id,name,email',
            'joiningStaff:id,name,email,profile_image',
        ])->orderBy('coordination_id', 'DESC');

        $perPage = max(1, min((int) ($request->input('per_page', 15)), 100));
        $returnAll = in_array(strtolower((string)$request->input('all', '')), ['1', 'true', 'yes'], true);

        if ($returnAll) {
            $items = $query->get();
            $formatted = $items->map(fn($c) => $this->formatCoordinationItem($c, $currentUser));
            return response()->json([
                'status'  => true,
                'message' => 'Coordinations retrieved successfully.',
                'data'    => $formatted,
            ]);
        }

        $paginated = $query->paginate($perPage);
        $paginated->getCollection()->transform(fn($c) => $this->formatCoordinationItem($c, $currentUser));

        return response()->json([
            'status'  => true,
            'message' => 'Coordinations retrieved successfully.',
            'data'    => $paginated,
        ]);
    }

    /**
     * Create a new coordination record.
     * POST /api/v1/coordinations
     */
    public function store(Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'coordinations.create')) {
            return $this->permissionDeniedResponse('create Coordination');
        }

        $validator = Validator::make($request->all(), [
            'title'               => ['required', 'string', 'max:255'],
            'staff_id'            => ['nullable', 'exists:users,id'],
            'link'                => ['required', 'string', 'max:2048'],
            'joining_staff_ids'   => ['nullable', 'array'],
            'joining_staff_ids.*' => ['exists:users,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $targetStaffId = !empty($validated['staff_id']) ? (int) $validated['staff_id'] : (int) $currentUser->id;

        $coordination = Coordination::create([
            'title'      => $validated['title'],
            'staff_id'   => $targetStaffId,
            'link'       => $validated['link'],
            'created_by' => $currentUser->id,
        ]);

        // Auto-include staff_id and creator in joining staff
        $joiningIds = $validated['joining_staff_ids'] ?? [];
        $joiningIds[] = $targetStaffId;
        $joiningIds[] = (int) $currentUser->id;
        $joiningIds = array_values(array_unique(array_filter(array_map('intval', $joiningIds))));

        $syncData = [];
        foreach ($joiningIds as $uid) {
            $syncData[$uid] = ['status' => 'Pending'];
        }
        $coordination->joiningStaff()->sync($syncData);

        $coordination->load(['staff:id,name,email,profile_image', 'creator:id,name,email', 'joiningStaff:id,name,email,profile_image']);

        // Dispatch notifications
        try {
            $currentUser->notify(new CoordinationCreatedNotification($coordination));

            $joiningStaffUsers = User::whereIn('id', $joiningIds)
                ->where('id', '!=', $currentUser->id)
                ->get();

            foreach ($joiningStaffUsers as $staffMember) {
                $staffMember->notify(new CoordinationAssignedNotification($coordination));
            }
        } catch (\Throwable $e) {
            Log::error('CoordinationApiController: Error sending notifications: ' . $e->getMessage());
        }

        return response()->json([
            'status'  => true,
            'message' => 'Coordination record created successfully.',
            'data'    => $this->formatCoordinationItem($coordination, $currentUser),
        ], 201);
    }

    /**
     * Show single coordination details.
     * GET /api/v1/coordinations/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'coordinations.view')) {
            return $this->permissionDeniedResponse('view Coordination');
        }

        $coordination = Coordination::forUser($currentUser)
            ->with(['staff:id,name,email,profile_image', 'creator:id,name,email', 'joiningStaff:id,name,email,profile_image'])
            ->find($id);

        if (!$coordination) {
            return response()->json(['status' => false, 'message' => 'Coordination record not found.'], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Coordination details retrieved successfully.',
            'data'    => $this->formatCoordinationItem($coordination, $currentUser),
        ]);
    }

    /**
     * Update an existing coordination record.
     * POST /api/v1/coordinations/update/{id} or PUT /api/v1/coordinations/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'coordinations.edit')) {
            return $this->permissionDeniedResponse('edit Coordination');
        }

        $coordination = Coordination::forUser($currentUser)->find($id);
        if (!$coordination) {
            return response()->json(['status' => false, 'message' => 'Coordination record not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title'               => ['required', 'string', 'max:255'],
            'staff_id'            => ['required', 'exists:users,id'],
            'link'                => ['required', 'string', 'max:2048'],
            'joining_staff_ids'   => ['nullable', 'array'],
            'joining_staff_ids.*' => ['exists:users,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $coordination->update([
            'title'    => $validated['title'],
            'staff_id' => $validated['staff_id'],
            'link'     => $validated['link'],
        ]);

        $joiningIds = $validated['joining_staff_ids'] ?? [];
        $joiningIds[] = (int) $validated['staff_id'];
        if (!empty($coordination->created_by)) {
            $joiningIds[] = (int) $coordination->created_by;
        }
        $joiningIds = array_values(array_unique(array_filter(array_map('intval', $joiningIds))));

        // Preserve existing 'Joined' statuses
        $existingPivot = CoordinationJoiningStaff::where('coordination_id', $coordination->coordination_id)
            ->get()
            ->keyBy('user_id');

        $previouslyAssignedIds = $existingPivot->keys()->all();
        $newlyAddedIds = array_diff($joiningIds, $previouslyAssignedIds);

        $syncData = [];
        foreach ($joiningIds as $uid) {
            if (isset($existingPivot[$uid])) {
                $syncData[$uid] = [
                    'status'    => $existingPivot[$uid]->status,
                    'joined_at' => $existingPivot[$uid]->joined_at,
                ];
            } else {
                $syncData[$uid] = [
                    'status'    => 'Pending',
                    'joined_at' => null,
                ];
            }
        }
        $coordination->joiningStaff()->sync($syncData);
        $coordination->load(['staff:id,name,email,profile_image', 'creator:id,name,email', 'joiningStaff:id,name,email,profile_image']);

        // Notify newly added staff
        if (!empty($newlyAddedIds)) {
            try {
                $newStaffUsers = User::whereIn('id', $newlyAddedIds)
                    ->where('id', '!=', $currentUser->id)
                    ->get();

                foreach ($newStaffUsers as $staffMember) {
                    $staffMember->notify(new CoordinationAssignedNotification($coordination));
                }
            } catch (\Throwable $e) {
                Log::error('CoordinationApiController: Error sending update notification: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status'  => true,
            'message' => 'Coordination record updated successfully.',
            'data'    => $this->formatCoordinationItem($coordination, $currentUser),
        ]);
    }

    /**
     * Toggle join status for the currently authenticated staff member.
     * POST /api/v1/coordinations/toggle-join/{id}
     */
    public function toggleJoinStatus(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $coordination = Coordination::forUser($currentUser)->find($id);
        if (!$coordination) {
            return response()->json(['status' => false, 'message' => 'Coordination record not found or access denied.'], 404);
        }

        $pivot = CoordinationJoiningStaff::where('coordination_id', $coordination->coordination_id)
            ->where('user_id', $currentUser->id)
            ->first();

        $forceJoin = $request->boolean('force_join', false);

        if (!$pivot) {
            $pivot = CoordinationJoiningStaff::create([
                'coordination_id' => $coordination->coordination_id,
                'user_id'         => $currentUser->id,
                'status'          => 'Joined',
                'joined_at'       => now(),
            ]);
            $newStatus = 'Joined';
        } else {
            if ($forceJoin) {
                $newStatus = 'Joined';
            } else {
                $newStatus = ($pivot->status === 'Joined') ? 'Pending' : 'Joined';
            }

            $pivot->update([
                'status'    => $newStatus,
                'joined_at' => ($newStatus === 'Joined') ? ($pivot->joined_at ?? now()) : null,
            ]);
        }

        $msg = ($newStatus === 'Joined') ? 'You have joined this coordination.' : 'Status updated to pending.';

        if ($newStatus === 'Joined') {
            try {
                $currentUser->notify(new CoordinationJoinedNotification($coordination, $currentUser));

                if ($coordination->created_by && $coordination->created_by != $currentUser->id) {
                    $creator = User::find($coordination->created_by);
                    if ($creator) {
                        $creator->notify(new CoordinationJoinedNotification($coordination, $currentUser));
                    }
                }
            } catch (\Throwable $e) {
                Log::error('CoordinationApiController: Error in toggle join notification: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status'     => true,
            'message'    => $msg,
            'new_status' => $newStatus,
            'joined_at'  => $pivot->joined_at ? $pivot->joined_at->format('Y-m-d H:i:s') : null,
        ]);
    }

    /**
     * Delete coordination record.
     * DELETE /api/v1/coordinations/{id}
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'coordinations.delete')) {
            return $this->permissionDeniedResponse('delete Coordination');
        }

        $coordination = Coordination::forUser($currentUser)->find($id);
        if (!$coordination) {
            return response()->json(['status' => false, 'message' => 'Coordination record not found.'], 404);
        }

        $coordination->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Coordination record deleted successfully.',
        ]);
    }

    /**
     * Format a Coordination model for API response.
     */
    private function formatCoordinationItem(Coordination $c, User $currentUser): array
    {
        $joiningStaffList = $c->joiningStaff;

        if ($joiningStaffList->isEmpty()) {
            $defaultIds = array_values(array_unique(array_filter([$c->staff_id, $c->created_by])));
            $defaultUsers = User::whereIn('id', $defaultIds)->get();
            $joiningStaffData = $defaultUsers->map(function ($u) {
                return [
                    'id'            => (int) $u->id,
                    'name'          => $u->name,
                    'email'         => $u->email,
                    'profile_image' => $u->profile_image_url ?? asset('assets/img/avatars/1.png'),
                    'status'        => 'Pending',
                    'joined_at'     => null,
                ];
            });
        } else {
            $joiningStaffData = $joiningStaffList->map(function ($u) {
                return [
                    'id'            => (int) $u->id,
                    'name'          => $u->name,
                    'email'         => $u->email,
                    'profile_image' => $u->profile_image_url ?? asset('assets/img/avatars/1.png'),
                    'status'        => $u->pivot->status ?? 'Pending',
                    'joined_at'     => $u->pivot->joined_at ? (string) $u->pivot->joined_at : null,
                ];
            });
        }

        $totalJoining = $joiningStaffData->count();
        $joinedStaff  = $joiningStaffData->where('status', 'Joined')->values();
        $pendingStaff = $joiningStaffData->where('status', 'Pending')->values();

        $userJoinedStatus = 'Pending';
        $userEntry = $joiningStaffData->firstWhere('id', $currentUser->id);
        if ($userEntry) {
            $userJoinedStatus = $userEntry['status'];
        }

        return [
            'id'                 => (int) $c->coordination_id,
            'coordination_id'    => (int) $c->coordination_id,
            'title'              => $c->title,
            'link'               => $c->link,
            'staff_id'           => (int) $c->staff_id,
            'staff_name'         => $c->staff?->name,
            'staff_email'        => $c->staff?->email,
            'staff_avatar'       => $c->staff?->profile_image_url ?? asset('assets/img/avatars/1.png'),
            'created_by'         => (int) $c->created_by,
            'creator_name'       => $c->creator?->name,
            'total_joining'      => $totalJoining,
            'joined_count'       => $joinedStaff->count(),
            'pending_count'      => $pendingStaff->count(),
            'user_joined_status' => $userJoinedStatus,
            'is_joined'          => ($userJoinedStatus === 'Joined'),
            'joining_staff'      => $joiningStaffData->all(),
            'joined_staff'       => $joinedStaff->all(),
            'pending_staff'      => $pendingStaff->all(),
            'created_at'         => $c->created_at ? $c->created_at->format('Y-m-d H:i:s') : null,
            'created_at_formatted' => $c->created_at ? $c->created_at->format('d-m-Y h:i A') : null,
        ];
    }
}
