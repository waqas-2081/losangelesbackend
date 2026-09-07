<?php
// app/Http/Controllers/Api/SiteSettingController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;

class SiteSettingController extends Controller
{
    public function show(): JsonResponse
    {
        $setting = SiteSetting::current();

        return response()->json([
            'data' => [
                'popup_image' => $setting->popup_image_url,
                'email'       => $setting->email,
                'phone'       => $setting->phone,
                'location'    => $setting->location,
                'social'      => [
                    'facebook'  => $setting->facebook_url,
                    'instagram' => $setting->instagram_url,
                    'x'         => $setting->x_url,
                    'linkedin'  => $setting->linkedin_url,
                    'tiktok'    => $setting->tiktok_url,
                    'youtube'   => $setting->youtube_url,
                ],
            ],
        ]);
    }
}