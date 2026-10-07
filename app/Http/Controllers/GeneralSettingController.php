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
            'favicon' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,svg,ico,cur', 'max:2048'],
            'whatsapp_no' => ['nullable', 'string', 'max:20'],
            'theme_color' => ['required', 'string', 'regex:/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/i'],
            'salary' => ['nullable'],
        ]);

        $setting->company_name = $validated['company_name'];
        $setting->whatsapp_no = $validated['whatsapp_no'] ?? null;
        $setting->theme_color = strtolower($validated['theme_color']);
        $setting->salary = ($request->has('salary') && in_array($request->input('salary'), [1, '1', true, 'true', 'on'], true)) ? 1 : 0;

        if ($request->hasFile('logo')) {
            $setting->logo = upload_file($request->file('logo'), 'settings', $setting->logo, 'logo');
        }

        if ($request->hasFile('favicon')) {
            $setting->favicon = upload_file($request->file('favicon'), 'settings', $setting->favicon, 'favicon');
        }

        $setting->save();
        GeneralSetting::clearCache();

        session()->flash('success', 'General settings saved successfully.');
        return redirect()->back();
    }
}
