<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData();
        }

        return view('templates.view');
    }

    public function listData()
    {
        $templates = Template::orderBy('template_id', 'DESC')->get();

        return response()->json([
            'status' => true,
            'data'   => $templates
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'    => ['required', 'string', 'max:100'],
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'status'  => ['required', 'in:Active,Inactive'],
        ]);

        $template = Template::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Template created successfully.',
            'data'    => $template
        ]);
    }

    public function edit($id)
    {
        $template = Template::find($id);
        if (!$template) {
            return response()->json([
                'status'  => false,
                'message' => 'Template not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $template
        ]);
    }

    public function update(Request $request, $id)
    {
        $template = Template::find($id);
        if (!$template) {
            return response()->json([
                'status'  => false,
                'message' => 'Template not found.'
            ], 404);
        }

        $validated = $request->validate([
            'type'    => ['required', 'string', 'max:100'],
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'status'  => ['required', 'in:Active,Inactive'],
        ]);

        $template->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Template updated successfully.',
            'data'    => $template
        ]);
    }

    public function destroy($id)
    {
        $template = Template::find($id);
        if (!$template) {
            return response()->json([
                'status'  => false,
                'message' => 'Template not found.'
            ], 404);
        }

        $template->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Template deleted successfully.'
        ]);
    }

    public function changeStatus(Request $request, $id)
    {
        $template = Template::find($id);
        if (!$template) {
            return response()->json([
                'status'  => false,
                'message' => 'Template not found.'
            ], 404);
        }

        $template->status = $template->status === 'Active' ? 'Inactive' : 'Active';
        $template->save();

        return response()->json([
            'status'     => true,
            'message'    => 'Template status updated successfully.',
            'new_status' => $template->status
        ]);
    }
}
