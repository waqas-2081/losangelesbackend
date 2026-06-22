<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WebsiteBriefFile extends Model
{
    protected $fillable = [
        'website_brief_id', 'original_name', 'file_path', 'mime_type', 'file_size',
    ];

    public function getHumanSizeAttribute(): string
    {
        $size = $this->file_size;
        if ($size >= 1048576) return round($size / 1048576, 2) . ' MB';
        if ($size >= 1024)    return round($size / 1024, 2) . ' KB';
        return $size . ' B';
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}