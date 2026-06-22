<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Image URL Accessor
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    // Categories list for dropdown
    public static function categories(): array
    {
        return [
            'Logo' => 'Logo',
            'Websites' => 'Websites',
            'Mobile App' => 'Mobile App',
            'Social Media' => 'Social Media',
            'SEO' => 'SEO',
            'Video Animation' => 'Video Animation',
            'Branding' => 'Branding',
        ];
    }
}