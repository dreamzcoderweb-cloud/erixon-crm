<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Traits\HasApiPermissionCheck;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamApiController extends Controller
{
    use HasApiPermissionCheck;

    /**
     * List teams with branch filtering and role scoping.
     * Supports branch_id (single) or branch_ids (array or comma-separated).
     * Accessible via GET /api/v1/teams
     */
    public function index(Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $query = Team::with(['branch:id,name,code', 'leader:id,name,email'])
            ->withCount(['users', 'customers', 'leads'])
            ->orderBy('name', 'asc');

        // Scoping
        if (!$currentUser->isAdmin()) {
            $accessibleTeamIds = $currentUser->accessibleTeamIds();
            if ($accessibleTeamIds !== null) {
                $query->whereIn('id', $accessibleTeamIds);
            }
        }

        // Branch filter (supports branch_id or branch_ids)
        $branchIds = [];
        if ($request->filled('branch_ids')) {
            $val = $request->input('branch_ids');
            $branchIds = is_array($val) ? $val : explode(',', (string)$val);
        } elseif ($request->filled('branch_id')) {
            $val = $request->input('branch_id');
            $branchIds = is_array($val) ? $val : explode(',', (string)$val);
        }

        $branchIds = array_filter(array_map('intval', $branchIds));
        if (!empty($branchIds)) {
            $query->whereIn('branch_id', $branchIds);
        }

        // Status filter
        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('status', (int)$request->input('status'));
        } else {
            $query->where('status', 1);
        }

        // Search
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $teams = $query->get();

        $formatted = $teams->map(function ($t) {
            $branchLabel = $t->branch ? " - {$t->branch->name}" : "";
            return [
                'id'              => $t->id,
                'branch_id'       => $t->branch_id,
                'name'            => $t->name,
                'code'            => $t->code,
                'label'           => "{$t->name} ({$t->code}){$branchLabel}",
                'leader_id'       => $t->leader_id,
                'leader'          => $t->leader ? [
                    'id'    => $t->leader->id,
                    'name'  => $t->leader->name,
                    'email' => $t->leader->email,
                ] : null,
                'description'     => $t->description,
                'status'          => (int)$t->status,
                'branch'          => $t->branch ? [
                    'id'   => $t->branch->id,
                    'name' => $t->branch->name,
                    'code' => $t->branch->code,
                ] : null,
                'users_count'     => (int)($t->users_count ?? 0),
                'customers_count' => (int)($t->customers_count ?? 0),
                'leads_count'     => (int)($t->leads_count ?? 0),
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Teams retrieved successfully.',
            'data'    => $formatted,
        ]);
    }

    /**
     * Get teams by specific branch ID or comma-separated branch IDs.
     * Accessible via GET /api/v1/teams/by-branch/{branchId}
     */
    public function getByBranch(Request $request, $branchId): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $branchIds = is_array($branchId) ? $branchId : explode(',', (string)$branchId);
        $branchIds = array_filter(array_map('intval', $branchIds));

        $query = Team::with('branch:id,name,code')
            ->whereIn('branch_id', $branchIds)
            ->where('status', 1)
            ->orderBy('name', 'asc');

        if (!$currentUser->isAdmin()) {
            $accessibleTeamIds = $currentUser->accessibleTeamIds();
            if ($accessibleTeamIds !== null) {
                $query->whereIn('id', $accessibleTeamIds);
            }
        }

        $teams = $query->get(['id', 'branch_id', 'name', 'code', 'leader_id']);

        $formatted = $teams->map(function ($t) {
            $branchLabel = $t->branch ? " - {$t->branch->name}" : "";
            return [
                'id'        => $t->id,
                'branch_id' => $t->branch_id,
                'name'      => $t->name,
                'code'      => $t->code,
                'label'     => "{$t->name} ({$t->code}){$branchLabel}",
                'branch'    => $t->branch ? [
                    'id'   => $t->branch->id,
                    'name' => $t->branch->name,
                    'code' => $t->branch->code,
                ] : null,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Teams by branch retrieved successfully.',
            'data'    => $formatted,
        ]);
    }

    /**
     * Get single team details.
     * Accessible via GET /api/v1/teams/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $team = Team::with(['branch:id,name,code', 'leader:id,name,email', 'creator:id,name'])
            ->withCount(['users', 'customers', 'leads'])
            ->find($id);

        if (!$team) {
            return response()->json(['status' => false, 'message' => 'Team not found.'], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Team details retrieved successfully.',
            'data'    => $team,
        ]);
    }

    /**
     * Create a new team.
     * Accessible via POST /api/v1/teams
     */
    public function store(Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }
        if (!$currentUser->isAdmin()) {
            return response()->json(['status' => false, 'message' => 'Permission denied.'], 403);
        }

        $validated = $request->validate([
            'branch_id'   => ['nullable', 'exists:branches,id'],
            'name'        => ['nullable', 'string', 'max:255'],
            'code'        => ['nullable', 'string', 'max:50', \Illuminate\Validation\Rule::unique('teams', 'code')->withoutTrashed()],
            'leader_id'   => ['nullable', 'exists:users,id'],
            'description' => ['nullable', 'string'],
            'status'      => ['nullable', 'in:0,1'],
        ]);

        $validated['created_by'] = $currentUser->id;
        $validated['status']     = $validated['status'] ?? 1;

        $team = Team::create($validated);

        if (!empty($team->leader_id)) {
            \Illuminate\Support\Facades\DB::table('team_user')->updateOrInsert(
                ['user_id' => $team->leader_id, 'team_id' => $team->id],
                ['role_in_team' => 'leader', 'is_primary' => true, 'updated_at' => now()]
            );
        }

        return response()->json([
            'status'  => true,
            'message' => 'Team created successfully.',
            'data'    => $team,
        ], 201);
    }

    /**
     * Update an existing team.
     * Accessible via POST /api/v1/teams/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }
        if (!$currentUser->isAdmin()) {
            return response()->json(['status' => false, 'message' => 'Permission denied.'], 403);
        }

        $team = Team::find($id);
        if (!$team) {
            return response()->json(['status' => false, 'message' => 'Team not found.'], 404);
        }

        $validated = $request->validate([
            'branch_id'   => ['nullable', 'exists:branches,id'],
            'name'        => ['nullable', 'string', 'max:255'],
            'code'        => ['nullable', 'string', 'max:50', \Illuminate\Validation\Rule::unique('teams', 'code')->ignore($team->id)->withoutTrashed()],
            'leader_id'   => ['nullable', 'exists:users,id'],
            'description' => ['nullable', 'string'],
            'status'      => ['nullable', 'in:0,1'],
        ]);

        $team->update($validated);

        if (!empty($team->leader_id)) {
            \Illuminate\Support\Facades\DB::table('team_user')->updateOrInsert(
                ['user_id' => $team->leader_id, 'team_id' => $team->id],
                ['role_in_team' => 'leader', 'is_primary' => true, 'updated_at' => now()]
            );
        }

        return response()->json([
            'status'  => true,
            'message' => 'Team updated successfully.',
            'data'    => $team->fresh(),
        ]);
    }

    /**
     * Delete a team.
     * Accessible via DELETE /api/v1/teams/{id}
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }
        if (!$currentUser->isAdmin()) {
            return response()->json(['status' => false, 'message' => 'Permission denied.'], 403);
        }

        if ((int)$id === 1) {
            return response()->json(['status' => false, 'message' => 'Default Main Team cannot be deleted.'], 422);
        }

        $team = Team::find($id);
        if (!$team) {
            return response()->json(['status' => false, 'message' => 'Team not found.'], 404);
        }

        $team->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Team deleted successfully.',
        ]);
    }

    /**
     * Toggle team active/inactive status.
     * Accessible via POST /api/v1/teams/{id}/change-status
     */
    public function changeStatus(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }
        if (!$currentUser->isAdmin()) {
            return response()->json(['status' => false, 'message' => 'Permission denied.'], 403);
        }

        $team = Team::find($id);
        if (!$team) {
            return response()->json(['status' => false, 'message' => 'Team not found.'], 404);
        }

        $team->status = $team->status == 1 ? 0 : 1;
        $team->save();

        return response()->json([
            'status'     => true,
            'message'    => 'Team status changed successfully.',
            'new_status' => $team->status,
        ]);
    }
}
