<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Traits\HasApiPermissionCheck;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchApiController extends Controller
{
    use HasApiPermissionCheck;

    /**
     * List all branches (scoped by user role).
     * Accessible via GET /api/v1/branches
     */
    public function index(Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $query = Branch::with(['manager:id,name,email', 'creator:id,name'])
            ->withCount(['teams', 'users'])
            ->orderBy('name', 'asc');

        // Scoping
        if (!$currentUser->isAdmin()) {
            $accessibleBranchIds = $currentUser->accessibleBranchIds();
            if ($accessibleBranchIds !== null) {
                $query->whereIn('id', $accessibleBranchIds);
            }
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
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('with_teams')) {
            $query->with(['teams' => function ($tq) {
                $tq->where('status', 1)->select('id', 'branch_id', 'name', 'code', 'leader_id');
            }]);
        }

        $branches = $query->get();

        $formatted = $branches->map(function ($b) {
            return [
                'id'          => $b->id,
                'name'        => $b->name,
                'code'        => $b->code,
                'label'       => "{$b->name} ({$b->code})",
                'manager_id'  => $b->manager_id,
                'manager'     => $b->manager ? [
                    'id'    => $b->manager->id,
                    'name'  => $b->manager->name,
                    'email' => $b->manager->email,
                ] : null,
                'phone'       => $b->phone,
                'email'       => $b->email,
                'address'     => $b->address,
                'city'        => $b->city,
                'state'       => $b->state,
                'pincode'     => $b->pincode,
                'status'      => (int)$b->status,
                'teams_count' => (int)($b->teams_count ?? 0),
                'users_count' => (int)($b->users_count ?? 0),
                'teams'       => $b->relationLoaded('teams') ? $b->teams : null,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Branches retrieved successfully.',
            'data'    => $formatted,
        ]);
    }

    /**
     * Get single branch details.
     * Accessible via GET /api/v1/branches/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $branch = Branch::with(['manager:id,name,email', 'creator:id,name', 'teams' => function ($tq) {
            $tq->where('status', 1)->with('leader:id,name,email');
        }])->withCount(['teams', 'users'])->find($id);

        if (!$branch) {
            return response()->json(['status' => false, 'message' => 'Branch not found.'], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Branch details retrieved successfully.',
            'data'    => $branch,
        ]);
    }

    /**
     * Create a new branch.
     * Accessible via POST /api/v1/branches
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
            'name'       => ['nullable', 'string', 'max:255'],
            'code'       => ['nullable', 'string', 'max:50', \Illuminate\Validation\Rule::unique('branches', 'code')->withoutTrashed()],
            'manager_id' => ['nullable', 'exists:users,id'],
            'phone'      => ['nullable', 'string', 'max:30'],
            'email'      => ['nullable', 'email', 'max:191'],
            'address'    => ['nullable', 'string'],
            'city'       => ['nullable', 'string', 'max:100'],
            'state'      => ['nullable', 'string', 'max:100'],
            'pincode'    => ['nullable', 'string', 'max:20'],
            'status'     => ['nullable', 'in:0,1'],
        ]);

        $validated['created_by'] = $currentUser->id;
        $validated['status']     = $validated['status'] ?? 1;

        $branch = Branch::create($validated);

        if (!empty($branch->manager_id)) {
            \Illuminate\Support\Facades\DB::table('branch_user')->updateOrInsert(
                ['user_id' => $branch->manager_id, 'branch_id' => $branch->id],
                ['is_primary' => true, 'updated_at' => now()]
            );
        }

        return response()->json([
            'status'  => true,
            'message' => 'Branch created successfully.',
            'data'    => $branch,
        ], 201);
    }

    /**
     * Update an existing branch.
     * Accessible via POST /api/v1/branches/{id}
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

        $branch = Branch::find($id);
        if (!$branch) {
            return response()->json(['status' => false, 'message' => 'Branch not found.'], 404);
        }

        $validated = $request->validate([
            'name'       => ['nullable', 'string', 'max:255'],
            'code'       => ['nullable', 'string', 'max:50', \Illuminate\Validation\Rule::unique('branches', 'code')->ignore($branch->id)->withoutTrashed()],
            'manager_id' => ['nullable', 'exists:users,id'],
            'phone'      => ['nullable', 'string', 'max:30'],
            'email'      => ['nullable', 'email', 'max:191'],
            'address'    => ['nullable', 'string'],
            'city'       => ['nullable', 'string', 'max:100'],
            'state'      => ['nullable', 'string', 'max:100'],
            'pincode'    => ['nullable', 'string', 'max:20'],
            'status'     => ['nullable', 'in:0,1'],
        ]);

        $branch->update($validated);

        if (!empty($branch->manager_id)) {
            \Illuminate\Support\Facades\DB::table('branch_user')->updateOrInsert(
                ['user_id' => $branch->manager_id, 'branch_id' => $branch->id],
                ['is_primary' => true, 'updated_at' => now()]
            );
        }

        return response()->json([
            'status'  => true,
            'message' => 'Branch updated successfully.',
            'data'    => $branch->fresh(),
        ]);
    }

    /**
     * Delete a branch.
     * Accessible via DELETE /api/v1/branches/{id}
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
            return response()->json(['status' => false, 'message' => 'Default Main Branch cannot be deleted.'], 422);
        }

        $branch = Branch::find($id);
        if (!$branch) {
            return response()->json(['status' => false, 'message' => 'Branch not found.'], 404);
        }

        $branch->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Branch deleted successfully.',
        ]);
    }

    /**
     * Toggle branch active/inactive status.
     * Accessible via POST /api/v1/branches/{id}/change-status
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

        $branch = Branch::find($id);
        if (!$branch) {
            return response()->json(['status' => false, 'message' => 'Branch not found.'], 404);
        }

        $branch->status = $branch->status == 1 ? 0 : 1;
        $branch->save();

        return response()->json([
            'status'     => true,
            'message'    => 'Branch status changed successfully.',
            'new_status' => $branch->status,
        ]);
    }
}
