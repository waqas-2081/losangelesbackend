<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogoBriefFile extends Model
{
    protected $fillable = [
        'logo_brief_id',
        'file_name',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function logoBrief(): BelongsTo
    {
        return $this->belongsTo(LogoBrief::class);
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->file_size ?? 0;

        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';

        return $bytes . ' B';
    }
}