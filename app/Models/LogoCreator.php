<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogoCreator extends Model
{
    use HasFactory;

    protected $table = 'logo_creator';

    protected $fillable = [
        'business_name',
        'slogan',
        'industry',
        'email',
        'phone',
        'current_step',
        'is_complete',
        'session_token',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'is_complete'  => 'boolean',
        'current_step' => 'integer',
    ];

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('business_name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhere('industry', 'like', "%{$term}%");
        });
    }
}