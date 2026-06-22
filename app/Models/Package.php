<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Package extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'slug',
        'service_type',
        'price',
        'price_type',
        'badge',
        'features',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2'
    ];

    public function packageFeatures()
    {
        return $this->hasMany(PackageFeature::class)->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByServiceType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }
}