<?php

namespace App\Http\Controllers;

use App\Models\Coordination;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoordinationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData();
        }

        $staffList = User::orderBy('name', 'asc')->get();

        return view('coordinations.view', compact('staffList'));
    }

    public function listData()
    {
        $coordinations = Coordination::with([
            'staff:id,name,email',
            'creator:id,name'
        ])->orderBy('coordination_id', 'DESC')->get();

        return response()->json([
            'status' => true,
            'data'   => $coordinations
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => ['required', 'exists:users,id'],
            'link'     => ['required', 'string', 'max:2048'],
        ]);

        $validated['created_by'] = Auth::id();

        $coordination = Coordination::create($validated);
        $coordination->load(['staff:id,name', 'creator:id,name']);

        return response()->json([
            'status'  => true,
            'message' => 'Coordination record created successfully.',
            'data'    => $coordination
        ]);
    }

    public function edit($id)
    {
        $coordination = Coordination::with(['staff:id,name', 'creator:id,name'])->find($id);
        if (!$coordination) {
            return response()->json([
                'status'  => false,
                'message' => 'Coordination record not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $coordination
        ]);
    }

    public function update(Request $request, $id)
    {
        $coordination = Coordination::find($id);
        if (!$coordination) {
            return response()->json([
                'status'  => false,
                'message' => 'Coordination record not found.'
            ], 404);
        }

        $validated = $request->validate([
            'staff_id' => ['required', 'exists:users,id'],
            'link'     => ['required', 'string', 'max:2048'],
        ]);

        $coordination->update($validated);
        $coordination->load(['staff:id,name', 'creator:id,name']);

        return response()->json([
            'status'  => true,
            'message' => 'Coordination record updated successfully.',
            'data'    => $coordination
        ]);
    }

    public function destroy($id)
    {
        $coordination = Coordination::find($id);
        if (!$coordination) {
            return response()->json([
                'status'  => false,
                'message' => 'Coordination record not found.'
            ], 404);
        }

        $coordination->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Coordination record deleted successfully.'
        ]);
    }
}
