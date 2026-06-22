<?php
// app/Models/PackageFeature.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class PackageFeature extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'package_id',
        'feature_text',
        'has_asterisk',
        'sort_order'
    ];

    protected $casts = [
        'has_asterisk' => 'boolean'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}