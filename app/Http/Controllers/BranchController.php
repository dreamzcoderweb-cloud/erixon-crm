<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    public function index(?Request $request = null)
    {
        $request = $request ?? request();
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData();
        }

        $managers = User::staffOnly()->where('status', 1)->orderBy('name')->get();
        return view('branches.view', compact('managers'));
    }

    public function listData()
    {
        $user = Auth::user();
        $query = Branch::with(['manager:id,name,email', 'creator:id,name'])
            ->withCount(['teams', 'users', 'customers', 'leads'])
            ->orderBy('id', 'DESC');

        if (!$user->isAdmin()) {
            $accessibleIds = $user->accessibleBranchIds();
            if ($accessibleIds !== null) {
                $query->whereIn('id', $accessibleIds);
            }
        }

        $branches = $query->get();

        return response()->json([
            'status' => true,
            'data'   => $branches
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['nullable', 'string', 'max:255'],
            'code'       => ['nullable', 'string', 'max:50', Rule::unique('branches', 'code')->withoutTrashed()],
            'manager_id' => ['nullable', 'exists:users,id'],
            'phone'      => ['nullable', 'string', 'max:30'],
            'email'      => ['nullable', 'email', 'max:191'],
            'address'    => ['nullable', 'string'],
            'city'       => ['nullable', 'string', 'max:100'],
            'state'      => ['nullable', 'string', 'max:100'],
            'pincode'    => ['nullable', 'string', 'max:20'],
            'status'     => ['nullable', 'in:0,1'],
        ]);

        $validated['created_by'] = Auth::id();

        $branch = Branch::create($validated);

        // If manager selected, ensure user has branch_id set and mapped in pivot
        if (!empty($branch->manager_id)) {
            $manager = User::find($branch->manager_id);
            if ($manager && empty($manager->branch_id)) {
                $manager->branch_id = $branch->id;
                $manager->saveQuietly();
            }
            \Illuminate\Support\Facades\DB::table('branch_user')->updateOrInsert(
                ['user_id' => $branch->manager_id, 'branch_id' => $branch->id],
                ['is_primary' => true, 'updated_at' => now()]
            );
        }

        return response()->json([
            'status'  => true,
            'message' => 'Branch created successfully.',
            'data'    => $branch
        ]);
    }

    public function edit($id)
    {
        $branch = Branch::with('manager:id,name')->find($id);
        if (!$branch) {
            return response()->json([
                'status'  => false,
                'message' => 'Branch not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $branch
        ]);
    }

    public function update(Request $request, $id)
    {
        $branch = Branch::find($id);
        if (!$branch) {
            return response()->json([
                'status'  => false,
                'message' => 'Branch not found.'
            ], 404);
        }

        $validated = $request->validate([
            'name'       => ['nullable', 'string', 'max:255'],
            'code'       => ['nullable', 'string', 'max:50', Rule::unique('branches', 'code')->ignore($branch->id)->withoutTrashed()],
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
            'data'    => $branch
        ]);
    }

    public function destroy($id)
    {
        if ((int)$id === 1) {
            return response()->json([
                'status'  => false,
                'message' => 'Default Main Branch cannot be deleted.'
            ], 422);
        }

        $branch = Branch::find($id);
        if (!$branch) {
            return response()->json([
                'status'  => false,
                'message' => 'Branch not found.'
            ], 404);
        }

        $branch->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Branch deleted successfully.'
        ]);
    }

    public function changeStatus(Request $request, $id)
    {
        $branch = Branch::find($id);
        if (!$branch) {
            return response()->json([
                'status'  => false,
                'message' => 'Branch not found.'
            ], 404);
        }

        $branch->status = $branch->status == 1 ? 0 : 1;
        $branch->save();

        return response()->json([
            'status'  => true,
            'message' => 'Branch status changed successfully.',
            'new_status' => $branch->status
        ]);
    }
}
