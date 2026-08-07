<?php

namespace App\Http\Controllers;

use App\Models\ReferralSetting;
use Illuminate\Http\Request;

class ReferralSettingController extends Controller
{
    public function index()
    {
        $data['setting'] = ReferralSetting::getSettings();
        return view('settings.referral', $data);
    }

    public function update(Request $request)
    {
        $setting = ReferralSetting::getSettings();

        $validated = $request->validate([
            'referral_points' => ['required', 'integer', 'min:0'],
        ]);

        $setting->referral_points = $validated['referral_points'];
        $setting->save();

        session()->flash('success', 'Referral settings saved successfully.');
        return redirect()->back();
    }
}
