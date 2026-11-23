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
}
