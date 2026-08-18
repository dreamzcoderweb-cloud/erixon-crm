<?php

namespace App\Http\Controllers;

use App\Models\CallRecording;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CallRecordingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData();
        }

        $data['leads'] = Lead::with('customer')->orderBy('lead_id', 'DESC')->get();

        return view('call_recordings.view', $data);
    }

    public function listData()
    {
        $recordings = CallRecording::with([
            'lead:lead_id,lead_title,customer_id',
            'lead.customer:customer_id,name',
            'creator:id,name'
        ])
        ->orderBy('call_id', 'DESC')
        ->get();

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
        $recording = CallRecording::with(['lead.customer', 'creator'])->find($id);
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
        $recording = CallRecording::find($id);
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
        $recording = CallRecording::find($id);
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
