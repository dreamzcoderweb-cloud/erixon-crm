<?php

namespace App\Http\Controllers;

use App\Models\LeadSetting;
use Illuminate\Http\Request;

class LeadSettingController extends Controller
{
    public function index()
    {
        $data['setting'] = LeadSetting::getSettings();
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
}
