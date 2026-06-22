<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebsiteBrief extends Model
{
    protected $fillable = [
        'name', 'email', 'business_name', 'website_type',
        'products_count', 'services_count_no_payment', 'future_images_products',
        'services_count_with_price', 'accept_online_payments', 'payment_medium', 'future_images_services',
        'business_description', 'business_industry', 'target_audience',
        'overall_feel', 'competitors_references',
        'has_domain', 'pages_count', 'pages_list',
        'has_logo', 'wants_logo_revamp', 'needs_hosting', 'needs_responsive',
        'addon_features', 'status', 'admin_notes',
    ];

    protected $casts = [
        'overall_feel'           => 'array',
        'addon_features'         => 'array',
        'accept_online_payments' => 'boolean',
        'has_domain'             => 'boolean',
        'has_logo'               => 'boolean',
        'wants_logo_revamp'      => 'boolean',
        'needs_hosting'          => 'boolean',
        'needs_responsive'       => 'boolean',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(WebsiteBriefFile::class);
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

    public function getHumanSizeAttribute(): string
    {
        return '—';
    }
}