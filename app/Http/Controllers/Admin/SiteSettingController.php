<?php
// app/Http/Controllers/Admin/SiteSettingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SiteSettingController extends Controller
{
    public function edit()
    {
        $setting = SiteSetting::current();

        return view('admin.site-settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = SiteSetting::current();

        $validator = Validator::make($request->all(), [
            'popup_image'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'email'         => ['nullable', 'email', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:50'],
            'location'      => ['nullable', 'string', 'max:255'],
            'facebook_url'  => ['nullable', 'url'],
            'instagram_url' => ['nullable', 'url'],
            'x_url'         => ['nullable', 'url'],
            'linkedin_url'  => ['nullable', 'url'],
            'tiktok_url'    => ['nullable', 'url'],
            'youtube_url'   => ['nullable', 'url'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->safe()->except('popup_image');

        if ($request->hasFile('popup_image')) {
            if ($setting->popup_image) {
                Storage::disk('public')->delete($setting->popup_image);
            }
            $data['popup_image'] = $request->file('popup_image')->store('site-settings', 'public');
        }

        $setting->update($data);

        return redirect()->route('admin.site-settings.edit')->with('success', 'Settings updated successfully.');
    }
}