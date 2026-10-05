<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commitment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommitmentApiController extends Controller
{
    /**
     * List commitments for the authenticated staff.
     * GET /api/v1/commitments
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $query = Commitment::forUser($user)->with('user:id,name,email,mobile_number');

        // Allow Admin to filter by specific staff user_id
        if ($request->filled('staff_id') && ($user->isAdmin() || $user->isSuperAdmin())) {
            $query->where('user_id', $request->input('staff_id'));
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('date', $request->input('date'));
        }

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->input('to_date'));
        }

        $list = $query->orderBy('date', 'desc')->orderBy('commitment_id', 'desc')->get();

        return response()->json([
            'status' => true,
            'count'  => $list->count(),
            'data'   => $list,
        ]);
    }

    /**
     * Create a new commitment entry from mobile app.
     * POST /api/v1/commitments
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'notes' => ['required', 'string'],
            'date'  => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $date = $request->filled('date')
            ? Carbon::parse($request->input('date'))->toDateString()
            : Carbon::today()->toDateString();

        $item = Commitment::create([
            'user_id' => $user->id,
            'date'    => $date,
            'notes'   => $request->input('notes'),
        ]);

        $item->load('user:id,name,email,mobile_number');

        return response()->json([
            'status'  => true,
            'message' => 'Commitment recorded successfully.',
            'data'    => $item,
        ], 201);
    }

    /**
     * View single commitment item.
     * GET /api/v1/commitments/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $item = Commitment::forUser($user)->with('user:id,name,email,mobile_number')->find($id);
        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Commitment not found.'], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $item,
        ]);
    }

    /**
     * Update commitment.
     * POST /api/v1/commitments/update/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $item = Commitment::forUser($user)->find($id);
        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Commitment not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'notes' => ['nullable', 'string'],
            'date'  => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        if ($request->filled('notes')) {
            $item->notes = $request->input('notes');
        }
        if ($request->filled('date')) {
            $item->date = Carbon::parse($request->input('date'))->toDateString();
        }

        $item->save();
        $item->load('user:id,name,email,mobile_number');

        return response()->json([
            'status'  => true,
            'message' => 'Commitment updated successfully.',
            'data'    => $item,
        ]);
    }

    /**
     * Delete commitment.
     * DELETE /api/v1/commitments/delete/{id}
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $item = Commitment::forUser($user)->find($id);
        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Commitment not found.'], 404);
        }

        $item->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Commitment deleted successfully.',
        ]);
    }
}
