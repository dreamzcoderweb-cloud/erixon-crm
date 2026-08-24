<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeadDocumentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData();
        }

        $data['leads'] = Lead::forUser(Auth::user())->with('customer')->orderBy('lead_id', 'DESC')->get();

        return view('lead_documents.view', $data);
    }

    public function listData()
    {
        $documents = LeadDocument::forUser(Auth::user())->with([
            'lead:lead_id,lead_title,customer_id',
            'lead.customer:customer_id,name',
            'uploader:id,name'
        ])
        ->orderBy('lead_documents_id', 'DESC')
        ->get();

        return response()->json([
            'status' => true,
            'data'   => $documents
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id'       => ['required', 'exists:leads,lead_id'],
            'document_type' => ['required', 'string', 'max:100'],
            'document_file' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,rar'],
        ]);

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $originalName = $file->getClientOriginalName();
            $fileName = time() . '_' . str_replace(' ', '_', $originalName);
            $destinationPath = public_path('uploads/lead_documents');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $fileName);
            $filePath = 'uploads/lead_documents/' . $fileName;

            $document = LeadDocument::create([
                'lead_id'       => $validated['lead_id'],
                'document_type' => $validated['document_type'],
                'file_name'     => $originalName,
                'file_path'     => $filePath,
                'uploaded_by'   => Auth::id(),
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Document uploaded successfully.',
                'data'    => $document
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'File upload failed.'
        ], 422);
    }

    public function edit($id)
    {
        $document = LeadDocument::forUser(Auth::user())->with(['lead.customer', 'uploader'])->find($id);
        if (!$document) {
            return response()->json([
                'status'  => false,
                'message' => 'Document not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $document
        ]);
    }

    public function update(Request $request, $id)
    {
        $document = LeadDocument::forUser(Auth::user())->find($id);
        if (!$document) {
            return response()->json([
                'status'  => false,
                'message' => 'Document not found.'
            ], 404);
        }

        $validated = $request->validate([
            'lead_id'       => ['required', 'exists:leads,lead_id'],
            'document_type' => ['required', 'string', 'max:100'],
            'document_file' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,rar'],
        ]);

        $document->lead_id       = $validated['lead_id'];
        $document->document_type = $validated['document_type'];

        if ($request->hasFile('document_file')) {
            // Delete old file if exists
            if (!empty($document->file_path) && file_exists(public_path($document->file_path))) {
                @unlink(public_path($document->file_path));
            }

            $file = $request->file('document_file');
            $originalName = $file->getClientOriginalName();
            $fileName = time() . '_' . str_replace(' ', '_', $originalName);
            $destinationPath = public_path('uploads/lead_documents');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $fileName);
            $document->file_name = $originalName;
            $document->file_path = 'uploads/lead_documents/' . $fileName;
        }

        $document->save();

        return response()->json([
            'status'  => true,
            'message' => 'Document updated successfully.',
            'data'    => $document
        ]);
    }

    public function destroy($id)
    {
        $document = LeadDocument::forUser(Auth::user())->find($id);
        if (!$document) {
            return response()->json([
                'status'  => false,
                'message' => 'Document not found.'
            ], 404);
        }

        // Delete physical file from storage
        delete_file($document->file_path);

        $document->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Document and associated file deleted successfully.'
        ]);
    }

    public function download($id)
    {
        $document = LeadDocument::forUser(Auth::user())->find($id);
        if (!$document || empty($document->file_path) || !file_exists(public_path($document->file_path))) {
            abort(404, 'Document file not found.');
        }

        return response()->download(public_path($document->file_path), $document->file_name);
    }
}
