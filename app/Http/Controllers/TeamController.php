<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    public function index(?Request $request = null)
    {
        $request = $request ?? request();
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData($request);
        }

        $user = Auth::user();
        if ($user->isAdmin()) {
            $branches = Branch::where('status', 1)->orderBy('name')->get();
            $leaders = User::staffOnly()->where('status', 1)->orderBy('name')->get();
        } else {
            $accessibleBranchIds = $user->accessibleBranchIds() ?? [];
            $branches = Branch::whereIn('id', $accessibleBranchIds)->where('status', 1)->orderBy('name')->get();
            $leaders = User::staffOnly()->whereIn('branch_id', $accessibleBranchIds)->where('status', 1)->orderBy('name')->get();
        }

        $managers = $leaders;
        return view('teams.view', compact('branches', 'leaders', 'managers'));
    }

    public function listData(Request $request = null)
    {
        $request = $request ?? request();
        $user = Auth::user();

        $query = Team::with(['branch:id,name,code', 'leader:id,name,email', 'creator:id,name'])
            ->withCount(['users', 'customers', 'leads'])
            ->orderBy('id', 'DESC');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->input('branch_id'));
        }

        if (!$user->isAdmin()) {
            $accessibleTeamIds = $user->accessibleTeamIds();
            if ($accessibleTeamIds !== null) {
                $query->whereIn('id', $accessibleTeamIds);
            }
        }

        $teams = $query->get();

        return response()->json([
            'status' => true,
            'data'   => $teams
        ]);
    }

    public function getByBranch($branchId)
    {
        $branchIds = is_array($branchId) ? $branchId : explode(',', (string) $branchId);
        $branchIds = array_filter(array_map('intval', $branchIds));

        $query = Team::with('branch:id,name,code')
            ->whereIn('branch_id', $branchIds)
            ->where('status', 1)
            ->orderBy('name');

        $user = Auth::user();
        if ($user && !$user->isAdmin()) {
            $accessibleTeamIds = $user->accessibleTeamIds();
            if ($accessibleTeamIds !== null) {
                $query->whereIn('id', $accessibleTeamIds);
            }
        }

        $teams = $query->get(['id', 'branch_id', 'name', 'code', 'leader_id']);

        return response()->json([
            'status' => true,
            'data'   => $teams
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id'   => ['nullable', 'exists:branches,id'],
            'name'        => ['nullable', 'string', 'max:255'],
            'code'        => ['nullable', 'string', 'max:50', Rule::unique('teams', 'code')->withoutTrashed()],
            'leader_id'   => ['nullable', 'exists:users,id'],
            'description' => ['nullable', 'string'],
            'status'      => ['nullable', 'in:0,1'],
        ]);

        $validated['created_by'] = Auth::id();

        $team = Team::create($validated);

        if (!empty($team->leader_id)) {
            $leader = User::find($team->leader_id);
            if ($leader && empty($leader->team_id)) {
                $leader->team_id = $team->id;
                $leader->saveQuietly();
            }
            \Illuminate\Support\Facades\DB::table('team_user')->updateOrInsert(
                ['user_id' => $team->leader_id, 'team_id' => $team->id],
                ['role_in_team' => 'leader', 'is_primary' => true, 'updated_at' => now()]
            );
        }

        return response()->json([
            'status'  => true,
            'message' => 'Team created successfully.',
            'data'    => $team
        ]);
    }

    public function edit($id)
    {
        $team = Team::with(['branch:id,name', 'leader:id,name'])->find($id);
        if (!$team) {
            return response()->json([
                'status'  => false,
                'message' => 'Team not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $team
        ]);
    }

    public function update(Request $request, $id)
    {
        $team = Team::find($id);
        if (!$team) {
            return response()->json([
                'status'  => false,
                'message' => 'Team not found.'
            ], 404);
        }

        $validated = $request->validate([
            'branch_id'   => ['nullable', 'exists:branches,id'],
            'name'        => ['nullable', 'string', 'max:255'],
            'code'        => ['nullable', 'string', 'max:50', Rule::unique('teams', 'code')->ignore($team->id)->withoutTrashed()],
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
            'data'    => $team
        ]);
    }

    public function destroy($id)
    {
        if ((int)$id === 1) {
            return response()->json([
                'status'  => false,
                'message' => 'Default Main Team cannot be deleted.'
            ], 422);
        }

        $team = Team::find($id);
        if (!$team) {
            return response()->json([
                'status'  => false,
                'message' => 'Team not found.'
            ], 404);
        }

        $team->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Team deleted successfully.'
        ]);
    }

    public function changeStatus(Request $request, $id)
    {
        $team = Team::find($id);
        if (!$team) {
            return response()->json([
                'status'  => false,
                'message' => 'Team not found.'
            ], 404);
        }

        $team->status = $team->status == 1 ? 0 : 1;
        $team->save();

        return response()->json([
            'status'  => true,
            'message' => 'Team status changed successfully.',
            'new_status' => $team->status
        ]);
    }
}
