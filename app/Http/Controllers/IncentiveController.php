<?php

namespace App\Http\Controllers;

use App\Models\Incentive;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncentiveController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData($request);
        }

        $user = Auth::user();
        if ($user->isSuperAdmin()) {
            $data['staffs'] = User::staffOnly()->orderBy('name')->get();
        } else {
            $data['staffs'] = User::where('id', $user->id)->get();
        }

        return view('incentives.index', $data);
    }

    public function listData(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();

        $query = Incentive::with(['staff:id,name,email', 'creator:id,name']);

        if (!$isSuperAdmin) {
            $query->where('staff_id', $user->id);
        } else {
            if ($request->filled('staff_id')) {
                $query->where('staff_id', $request->input('staff_id'));
            }
        }

        if ($request->filled('month')) {
            $query->where('month', $request->input('month'));
        }

        $incentives = $query->orderBy('incentive_id', 'DESC')->get();

        return response()->json([
            'status'     => true,
            'can_create' => $user->can('incentives.create') || $isSuperAdmin,
            'can_edit'   => $user->can('incentives.edit') || $isSuperAdmin,
            'can_delete' => $user->can('incentives.delete') || $isSuperAdmin,
            'data'       => $incentives
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('incentives.create') && !$user->isSuperAdmin()) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $validated = $request->validate([
            'staff_id' => ['required', 'exists:users,id'],
            'month'    => ['required', 'string', 'max:20'],
            'amount'   => ['required', 'numeric', 'min:0.01'],
            'remarks'  => ['nullable', 'string', 'max:1000'],
        ]);

        // Requirement 2: Automatically store the currently logged-in user's ID in created_by
        $validated['created_by'] = Auth::id();

        $incentive = Incentive::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Incentive created successfully.',
            'data'    => $incentive
        ]);
    }

    public function edit($id)
    {
        $user = Auth::user();
        if (!$user->can('incentives.edit') && !$user->isSuperAdmin()) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $incentive = Incentive::with(['staff', 'creator'])->find($id);
        if (!$incentive) {
            return response()->json(['status' => false, 'message' => 'Incentive record not found.'], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $incentive
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->can('incentives.edit') && !$user->isSuperAdmin()) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $incentive = Incentive::find($id);
        if (!$incentive) {
            return response()->json(['status' => false, 'message' => 'Incentive record not found.'], 404);
        }

        $validated = $request->validate([
            'staff_id' => ['required', 'exists:users,id'],
            'month'    => ['required', 'string', 'max:20'],
            'amount'   => ['required', 'numeric', 'min:0.01'],
            'remarks'  => ['nullable', 'string', 'max:1000'],
        ]);

        $incentive->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Incentive updated successfully.',
            'data'    => $incentive
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->can('incentives.delete') && !$user->isSuperAdmin()) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $incentive = Incentive::find($id);
        if (!$incentive) {
            return response()->json(['status' => false, 'message' => 'Incentive record not found.'], 404);
        }

        $incentive->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Incentive record deleted successfully.'
        ]);
    }
}
