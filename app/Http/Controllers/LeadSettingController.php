<?php

namespace App\Http\Controllers;

use App\Models\LeadSetting;
use App\Models\LeadCustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LeadSettingController extends Controller
{
    public function index()
    {
        $data['setting'] = LeadSetting::getSettings();
        $data['customFields'] = LeadCustomField::orderBy('id', 'asc')->get();
        return view('settings.lead', $data);
    }

    public function update(Request $request)
    {
        $setting = LeadSetting::getSettings();

        $validated = $request->validate([
            'referral_points' => ['required', 'integer', 'min:0'],
        ]);

        $setting->referral_points = $validated['referral_points'];
        $setting->save();

        session()->flash('success', 'Lead settings saved successfully.');
        return redirect()->back();
    }

    public function storeCustomField(Request $request)
    {
        $validated = $request->validate([
            'field_label'   => ['required', 'string', 'max:255'],
            'field_type'    => ['required', 'string', 'in:Text,Number,Dropdown,Textarea,Date,Checkbox'],
            'field_options' => ['nullable', 'string', 'max:1000'],
            'is_required'   => ['required', 'in:Yes,No'],
        ]);

        $baseSlug = Str::slug($validated['field_label'], '_');
        if (empty($baseSlug)) {
            $baseSlug = 'custom_field';
        }

        $fieldName = $baseSlug;
        $counter = 1;
        while (LeadCustomField::where('field_name', $fieldName)->exists()) {
            $fieldName = $baseSlug . '_' . $counter;
            $counter++;
        }

        $customField = LeadCustomField::create([
            'field_label'   => $validated['field_label'],
            'field_name'    => $fieldName,
            'field_type'    => $validated['field_type'],
            'field_options' => $validated['field_options'] ?? null,
            'is_required'   => $validated['is_required'],
            'status'        => 1,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => true,
                'message' => 'Custom field created successfully.',
                'data'    => $customField
            ]);
        }

        session()->flash('success', 'Custom field created successfully.');
        return redirect()->back();
    }

    public function editCustomField($id)
    {
        $field = LeadCustomField::find($id);
        if (!$field) {
            return response()->json([
                'status'  => false,
                'message' => 'Custom field not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $field
        ]);
    }

    public function updateCustomField(Request $request, $id)
    {
        $field = LeadCustomField::find($id);
        if (!$field) {
            return response()->json([
                'status'  => false,
                'message' => 'Custom field not found.'
            ], 404);
        }

        $validated = $request->validate([
            'field_label'   => ['required', 'string', 'max:255'],
            'field_type'    => ['required', 'string', 'in:Text,Number,Dropdown,Textarea,Date,Checkbox'],
            'field_options' => ['nullable', 'string', 'max:1000'],
            'is_required'   => ['required', 'in:Yes,No'],
        ]);

        $field->update([
            'field_label'   => $validated['field_label'],
            'field_type'    => $validated['field_type'],
            'field_options' => $validated['field_options'] ?? null,
            'is_required'   => $validated['is_required'],
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => true,
                'message' => 'Custom field updated successfully.',
                'data'    => $field
            ]);
        }

        session()->flash('success', 'Custom field updated successfully.');
        return redirect()->back();
    }

    public function destroyCustomField($id)
    {
        $field = LeadCustomField::find($id);
        if (!$field) {
            return response()->json([
                'status'  => false,
                'message' => 'Custom field not found.'
            ], 404);
        }

        $field->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Custom field deleted successfully.'
        ]);
    }
}
