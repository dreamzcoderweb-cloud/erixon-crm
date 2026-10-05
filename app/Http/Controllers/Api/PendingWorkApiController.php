<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PendingWork;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PendingWorkApiController extends Controller
{
    /**
     * List pending works for the authenticated staff.
     * GET /api/v1/pending-works
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $query = PendingWork::forUser($user)->with('user:id,name,email,mobile_number');

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

        // Status filter (0 = Pending, 1 = Process, 2 = Finished)
        if ($request->filled('status')) {
            $query->where('status', (int) $request->input('status'));
        }

        $list = $query->orderBy('date', 'desc')->orderBy('pending_id', 'desc')->get();

        return response()->json([
            'status' => true,
            'count'  => $list->count(),
            'data'   => $list,
        ]);
    }

    /**
     * Create a new pending work from mobile app.
     * POST /api/v1/pending-works
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'notes'  => ['required', 'string'],
            'date'   => ['nullable', 'date'],
            'status' => ['nullable', 'integer', 'in:0,1,2'],
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

        $status = $request->filled('status') ? (int) $request->input('status') : PendingWork::STATUS_PENDING;

        $pendingWork = PendingWork::create([
            'user_id' => $user->id,
            'date'    => $date,
            'notes'   => $request->input('notes'),
            'status'  => $status,
        ]);

        $pendingWork->load('user:id,name,email,mobile_number');

        return response()->json([
            'status'  => true,
            'message' => 'Pending work created successfully.',
            'data'    => $pendingWork,
        ], 201);
    }

    /**
     * View single pending work item.
     * GET /api/v1/pending-works/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $item = PendingWork::forUser($user)->with('user:id,name,email,mobile_number')->find($id);
        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Pending work not found.'], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $item,
        ]);
    }

    /**
     * Update pending work.
     * POST /api/v1/pending-works/update/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $item = PendingWork::forUser($user)->find($id);
        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Pending work not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'notes'  => ['nullable', 'string'],
            'date'   => ['nullable', 'date'],
            'status' => ['nullable', 'integer', 'in:0,1,2'],
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
        if ($request->has('status') && $request->input('status') !== null && $request->input('status') !== '') {
            $item->status = (int) $request->input('status');
        }

        $item->save();
        $item->load('user:id,name,email,mobile_number');

        return response()->json([
            'status'  => true,
            'message' => 'Pending work updated successfully.',
            'data'    => $item,
        ]);
    }

    /**
     * Change status of pending work (0: Pending, 1: Process, 2: Finished).
     * POST /api/v1/pending-works/change-status/{id}
     */
    public function changeStatus(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $item = PendingWork::forUser($user)->find($id);
        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Pending work not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => ['required', 'integer', 'in:0,1,2'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $item->status = (int) $request->input('status');
        $item->save();
        $item->load('user:id,name,email,mobile_number');

        return response()->json([
            'status'  => true,
            'message' => 'Status updated successfully.',
            'data'    => $item,
        ]);
    }

    /**
     * Delete pending work.
     * DELETE /api/v1/pending-works/delete/{id}
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $item = PendingWork::forUser($user)->find($id);
        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Pending work not found.'], 404);
        }

        $item->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Pending work deleted successfully.',
        ]);
    }
}
