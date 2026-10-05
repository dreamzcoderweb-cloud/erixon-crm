<?php

namespace App\Http\Controllers;

use App\Models\Commitment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommitmentController extends Controller
{
    /**
     * Display commitment management view or return JSON if requested.
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData($request);
        }

        $user = Auth::user();
        $staffList = User::where('status', 1)->orderBy('name')->get(['id', 'name', 'email', 'mobile_number']);

        return view('commitments.index', compact('staffList'));
    }

    /**
     * DataTable data endpoint with datepicker and staff dropdown filtering.
     */
    public function listData(Request $request = null)
    {
        $request = $request ?? request();
        $user = Auth::user();

        $query = Commitment::forUser($user)->with('user:id,name,email,mobile_number');

        // Staff Dropdown Filter
        if ($request->filled('staff_id')) {
            $query->where('user_id', $request->input('staff_id'));
        }

        // Period filtering (All, Daily, Weekly, Monthly, Custom) matching Customer module
        $filterType = $request->input('filter_type');
        $date       = $request->input('date');
        $month      = $request->input('month');
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');

        if ($filterType === 'daily' && !empty($date)) {
            $query->whereDate('date', $date);
        } elseif ($filterType === 'weekly') {
            $refDate = !empty($startDate) ? Carbon::parse($startDate) : Carbon::today();
            $query->whereBetween('date', [
                $refDate->copy()->startOfWeek()->toDateString(),
                $refDate->copy()->endOfWeek()->toDateString(),
            ]);
        } elseif ($filterType === 'monthly' && !empty($month)) {
            [$year, $selectedMonth] = array_pad(explode('-', $month), 2, null);
            $query->whereYear('date', $year ?: date('Y'))
                ->whereMonth('date', $selectedMonth ?: date('m'));
        } elseif ($filterType === 'custom') {
            if (!empty($startDate)) {
                $query->whereDate('date', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $query->whereDate('date', '<=', $endDate);
            }
        } elseif (!empty($date)) {
            $query->whereDate('date', $date);
        }

        // Text Search
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('mobile_number', 'like', "%{$search}%");
                  });
            });
        }

        $items = $query->orderBy('date', 'desc')->orderBy('commitment_id', 'desc')->get();

        return response()->json([
            'status' => true,
            'data'   => $items,
        ]);
    }

    /**
     * Store new commitment from Admin panel.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'date'    => ['nullable', 'date'],
            'notes'   => ['required', 'string'],
        ]);

        $userId = !empty($validated['user_id']) ? (int) $validated['user_id'] : Auth::id();
        $date = !empty($validated['date']) ? Carbon::parse($validated['date'])->toDateString() : Carbon::today()->toDateString();

        $item = Commitment::create([
            'user_id' => $userId,
            'date'    => $date,
            'notes'   => $validated['notes'],
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Commitment recorded successfully.',
            'data'    => $item->load('user:id,name,email,mobile_number'),
        ]);
    }

    /**
     * Get single commitment for edit modal.
     */
    public function edit($id)
    {
        $user = Auth::user();
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
     * Update commitment from Admin panel.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $item = Commitment::forUser($user)->find($id);

        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Commitment not found.'], 404);
        }

        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'date'    => ['nullable', 'date'],
            'notes'   => ['required', 'string'],
        ]);

        if (!empty($validated['user_id'])) {
            $item->user_id = (int) $validated['user_id'];
        }
        if (!empty($validated['date'])) {
            $item->date = Carbon::parse($validated['date'])->toDateString();
        }
        $item->notes = $validated['notes'];
        $item->save();

        return response()->json([
            'status'  => true,
            'message' => 'Commitment updated successfully.',
            'data'    => $item->load('user:id,name,email,mobile_number'),
        ]);
    }

    /**
     * Delete commitment.
     */
    public function destroy($id)
    {
        $user = Auth::user();
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
