<?php

namespace App\Http\Controllers;

use App\Models\LeadSetting;
use App\Models\FollowupCustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FollowupSettingController extends Controller
{
    public function index()
    {
        $setting = LeadSetting::getSettings();
        $customFields = FollowupCustomField::orderBy('id', 'asc')->get();

        $standardFields = [
            'lead_info'          => 'Lead Info',
            'followup_type'      => 'Type',
            'duration'           => 'Duration',
            'next_followup_date' => 'Next Follow-up Date',
            'status'             => 'Status',
            'forward_to'         => 'Forward To',
            'created_by'         => 'Created By',
            'created_at'         => 'Created At',
            'remarks'            => 'Remarks',
        ];

        $allAvailableFields = [];
        foreach ($standardFields as $key => $label) {
            $allAvailableFields[$key] = [
                'key'   => $key,
                'label' => $label,
                'type'  => 'standard',
            ];
        }

        foreach ($customFields as $cf) {
            $allAvailableFields[$cf->field_name] = [
                'key'   => $cf->field_name,
                'label' => $cf->field_label,
                'type'  => 'custom',
                'field' => $cf,
            ];
        }

        $savedColumns = $setting->followup_list_columns;
        if (empty($savedColumns) || !is_array($savedColumns)) {
            $savedColumns = array_keys($allAvailableFields);
        } else {
            $savedColumns = array_values(array_filter($savedColumns, function ($key) use ($allAvailableFields) {
                return isset($allAvailableFields[$key]);
            }));
        }

        $data['setting']            = $setting;
        $data['customFields']       = $customFields;
        $data['allAvailableFields'] = $allAvailableFields;
        $data['selectedColumns']    = $savedColumns;

        return view('settings.followup', $data);
    }

    public function saveFollowupListColumns(Request $request)
    {
        $setting = LeadSetting::getSettings();

        $validated = $request->validate([
            'columns'   => ['nullable', 'array'],
            'columns.*' => ['string'],
        ]);

        $setting->followup_list_columns = $validated['columns'] ?? [];
        $setting->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => true,
                'message' => 'Followup List customization saved successfully.',
                'data'    => $setting->followup_list_columns
            ]);
        }

        session()->flash('success', 'Followup List customization saved successfully.');
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
        while (FollowupCustomField::where('field_name', $fieldName)->exists()) {
            $fieldName = $baseSlug . '_' . $counter;
            $counter++;
        }

        $customField = FollowupCustomField::create([
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
        $field = FollowupCustomField::find($id);
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
        $field = FollowupCustomField::find($id);
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
        $field = FollowupCustomField::find($id);
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
