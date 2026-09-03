<?php

namespace App\Http\Controllers;

use App\Models\LeadSetting;
use App\Models\DemoProcessCustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DemoProcessSettingController extends Controller
{
    public function index()
    {
        $setting = LeadSetting::getSettings();
        $customFields = DemoProcessCustomField::orderBy('id', 'asc')->get();

        $standardFields = [
            'customer_name'   => 'Customer Name',
            'customer_phone'  => 'Phone Number',
            'lead_source'     => 'Lead Source',
            'product_name'    => 'Product Name',
            'demo_date'       => 'Demo Date',
            'demo_time'       => 'Demo Timing',
            'customer_type'   => 'Customer Type',
            'created_by'      => 'Created By (Sales)',
            'assigned_by'     => 'Assigned By (PM)',
            'sub_assigned_by' => 'Sub Assigned By (Support)',
            'status'          => 'Status',
            'remarks'         => 'Remarks',
            'created_at'      => 'Created Date',
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

        $savedColumns = $setting->demo_process_list_columns;
        if (empty($savedColumns) || !is_array($savedColumns)) {
            $savedColumns = array_keys($allAvailableFields);
        } else {
            // Keep only keys that exist in allAvailableFields
            $savedColumns = array_values(array_filter($savedColumns, function ($key) use ($allAvailableFields) {
                return isset($allAvailableFields[$key]);
            }));
        }

        $data['setting']            = $setting;
        $data['customFields']       = $customFields;
        $data['allAvailableFields'] = $allAvailableFields;
        $data['selectedColumns']    = $savedColumns;

        return view('settings.demo_process', $data);
    }

    public function saveDemoProcessListColumns(Request $request)
    {
        $setting = LeadSetting::getSettings();

        $validated = $request->validate([
            'columns'   => ['nullable', 'array'],
            'columns.*' => ['string'],
        ]);

        $setting->demo_process_list_columns = $validated['columns'] ?? [];
        $setting->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => true,
                'message' => 'Demo Process List customization saved successfully.',
                'data'    => $setting->demo_process_list_columns
            ]);
        }

        session()->flash('success', 'Demo Process List customization saved successfully.');
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
        while (DemoProcessCustomField::where('field_name', $fieldName)->exists()) {
            $fieldName = $baseSlug . '_' . $counter;
            $counter++;
        }

        $customField = DemoProcessCustomField::create([
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
        $field = DemoProcessCustomField::find($id);
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
        $field = DemoProcessCustomField::find($id);
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
        $field = DemoProcessCustomField::find($id);
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
