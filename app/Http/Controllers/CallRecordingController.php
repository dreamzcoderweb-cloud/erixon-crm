<?php

namespace App\Http\Controllers;

use App\Models\CallRecording;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CallRecordingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData($request);
        }

        $user         = Auth::user();
        $isSuperAdmin = $user && $user->isSuperAdmin();

        $data['leads']     = Lead::forUser($user)->with('customer')->orderBy('lead_id', 'DESC')->get();
        $data['staffs']    = $isSuperAdmin ? User::staffOnly()->orderBy('name')->get() : User::where('id', $user->id)->get();
        $data['customers'] = Customer::forUser($user)->where('status', 1)->orderBy('name')->get();

        return view('call_recordings.view', $data);
    }

    public function listData(Request $request = null)
    {
        $request = $request ?? request();

        $query = CallRecording::forUser(Auth::user())->with([
            'lead:lead_id,lead_title,customer_id',
            'lead.customer:customer_id,name',
            'creator:id,name'
        ]);

        if ($request->filled('user_id')) {
            $query->where('created_by', $request->input('user_id'));
        }

        if ($request->filled('lead_id')) {
            $query->where('lead_id', $request->input('lead_id'));
        }

        if ($request->filled('customer_id')) {
            $custId = $request->input('customer_id');
            $query->where(function ($q) use ($custId) {
                $q->where('customer_id', $custId)
                  ->orWhereHas('lead', function ($lq) use ($custId) {
                      $lq->where('customer_id', $custId);
                  });
            });
        }

        $filterType = $request->input('filter_type', 'all');
        $date       = $request->input('date');
        $week       = $request->input('week');
        $month      = $request->input('month');
        $year       = $request->input('year');
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');

        if ($filterType === 'daily' && !empty($date)) {
            $query->whereDate('created_at', $date);
        } elseif ($filterType === 'weekly') {
            if (!empty($week) && preg_match('/^(\d{4})-W(\d{2})$/', $week, $matches)) {
                $wYear = (int)$matches[1];
                $wWeek = (int)$matches[2];
                $startOfWeek = Carbon::now()->setISODate($wYear, $wWeek)->startOfWeek();
                $endOfWeek   = Carbon::now()->setISODate($wYear, $wWeek)->endOfWeek();
                $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            } else {
                $refDate = !empty($startDate) ? Carbon::parse($startDate) : (!empty($date) ? Carbon::parse($date) : Carbon::today());
                $query->whereBetween('created_at', [
                    $refDate->copy()->startOfWeek(),
                    $refDate->copy()->endOfWeek(),
                ]);
            }
        } elseif ($filterType === 'monthly' && !empty($month)) {
            [$mYear, $mMonth] = array_pad(explode('-', $month), 2, null);
            $query->whereYear('created_at', $mYear ?: date('Y'))
                  ->whereMonth('created_at', $mMonth ?: date('m'));
        } elseif ($filterType === 'yearly') {
            $targetYear = !empty($year) ? $year : date('Y');
            $query->whereYear('created_at', $targetYear);
        } elseif ($filterType === 'custom') {
            if (!empty($startDate)) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }

        $recordings = $query->orderBy('call_id', 'DESC')->get();

        return response()->json([
            'status' => true,
            'data'   => $recordings
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id'        => ['required', 'exists:leads,lead_id'],
            'duration'       => ['nullable', 'string', 'max:100'],
            'recording_file' => ['required', 'file', 'max:20480', 'mimes:mp3,wav,m4a,ogg,aac,webm'],
        ]);

        if ($request->hasFile('recording_file')) {
            $file = $request->file('recording_file');
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $destinationPath = public_path('uploads/call_recordings');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $fileName);
            $filePath = 'uploads/call_recordings/' . $fileName;

            $recording = CallRecording::create([
                'lead_id'        => $validated['lead_id'],
                'duration'       => $validated['duration'] ?? null,
                'recording_file' => $filePath,
                'created_by'     => Auth::id(),
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Call recording uploaded successfully.',
                'data'    => $recording
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Audio file upload failed.'
        ], 422);
    }

    public function edit($id)
    {
        $recording = CallRecording::forUser(Auth::user())->with(['lead.customer', 'creator'])->find($id);
        if (!$recording) {
            return response()->json([
                'status'  => false,
                'message' => 'Call recording not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $recording
        ]);
    }

    public function update(Request $request, $id)
    {
        $recording = CallRecording::forUser(Auth::user())->find($id);
        if (!$recording) {
            return response()->json([
                'status'  => false,
                'message' => 'Call recording not found.'
            ], 404);
        }

        $validated = $request->validate([
            'lead_id'        => ['required', 'exists:leads,lead_id'],
            'duration'       => ['nullable', 'string', 'max:100'],
            'recording_file' => ['nullable', 'file', 'max:20480', 'mimes:mp3,wav,m4a,ogg,aac,webm'],
        ]);

        $recording->lead_id  = $validated['lead_id'];
        $recording->duration = $validated['duration'] ?? null;

        if ($request->hasFile('recording_file')) {
            // Delete old file
            if (!empty($recording->recording_file) && file_exists(public_path($recording->recording_file))) {
                @unlink(public_path($recording->recording_file));
            }

            $file = $request->file('recording_file');
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $destinationPath = public_path('uploads/call_recordings');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $fileName);
            $recording->recording_file = 'uploads/call_recordings/' . $fileName;
        }

        $recording->save();

        return response()->json([
            'status'  => true,
            'message' => 'Call recording updated successfully.',
            'data'    => $recording
        ]);
    }

    public function destroy($id)
    {
        $recording = CallRecording::forUser(Auth::user())->find($id);
        if (!$recording) {
            return response()->json([
                'status'  => false,
                'message' => 'Call recording not found.'
            ], 404);
        }

        // Delete physical audio file from storage
        delete_file($recording->recording_file);

        $recording->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Call recording and audio file deleted successfully.'
        ]);
    }
}
