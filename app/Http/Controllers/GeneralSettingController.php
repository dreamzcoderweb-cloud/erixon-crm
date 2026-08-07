<?php

namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GeneralSettingController extends Controller
{
    public function index()
    {
        $data['setting'] = GeneralSetting::getSettings();
        return view('settings.general', $data);
    }

    public function update(Request $request)
    {
        $setting = GeneralSetting::getSettings();

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,svg', 'max:2048'],
            'whatsapp_no' => ['nullable', 'string', 'max:20'],
            'theme_color' => ['required', 'string', 'regex:/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/i'],
        ]);

        $setting->company_name = $validated['company_name'];
        $setting->whatsapp_no = $validated['whatsapp_no'] ?? null;
        $setting->theme_color = strtolower($validated['theme_color']);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($setting->logo && File::exists(public_path($setting->logo))) {
                File::delete(public_path($setting->logo));
            }

            $file = $request->file('logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/settings'), $filename);
            $setting->logo = 'uploads/settings/' . $filename;
        }

        $setting->save();

        session()->flash('success', 'General settings saved successfully.');
        return redirect()->back();
    }
}
