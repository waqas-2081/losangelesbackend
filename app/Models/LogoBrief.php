<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LogoBrief extends Model
{
    protected $fillable = [
        'name',
        'email',
        'personal_phone',
        'company_phone',
        'logo_name',
        'company_slogan',
        'industry',
        'business_desc',
        'logo_description',
        'competitors_ref',
        'competitors_ref_two',
        'competitors_ref_three',
        'logo_type',
        'logo_fonts',
        'logo_color',       // singular — matches request
        'primary_color',
        'secondary_color',
        'status',
        'admin_notes',
    ];
    
    public function files(): HasMany
    {
        return $this->hasMany(LogoBriefFile::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'Pending',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
            'rejected'    => 'Rejected',
            default       => ucfirst($this->status),
        };
    }

}