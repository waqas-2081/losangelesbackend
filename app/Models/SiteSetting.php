<?php
// app/Models/SiteSetting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'popup_image',
        'email',
        'phone',
        'location',
        'facebook_url',
        'instagram_url',
        'x_url',
        'linkedin_url',
        'tiktok_url',
        'youtube_url',
    ];

    protected $appends = ['popup_image_url'];

    public function getPopupImageUrlAttribute()
    {
        return $this->popup_image ? asset('storage/' . $this->popup_image) : null;
    }

    public static function current(): self
    {
        return static::firstOrCreate([]);
    }
}