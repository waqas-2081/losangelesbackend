<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebsiteBrief extends Model
{
    protected $fillable = [
        'name', 'email', 'business_name', 'website_type',
        // informative-no-payment
        'product_showcase_count', 'service_showcase_count', 'future_images_products',
        // informative-with-payment
        'services_prices', 'accept_online_payments', 'payment_medium', 'future_images_services',
        // ecommerce
        'product_categories', 'product_count', 'product_source',
        // ecommerce + web-app
        'platform_required',
        // brand & audience
        'business_desc', 'industry', 'target_audience', 'feel', 'competitors',
        // site structure
        'has_domain', 'page_count', 'page_names',
        'has_logo', 'revamp_logo', 'need_hosting', 'need_responsive',
        // addons
        'addon_features',
        // admin
        'status', 'admin_notes',
    ];

    protected $casts = [
        'feel'                    => 'array',
        'addon_features'          => 'array',
        'accept_online_payments'  => 'boolean',
        'has_domain'              => 'boolean',
        'has_logo'                => 'boolean',
        'revamp_logo'             => 'boolean',
        'need_hosting'            => 'boolean',
        'need_responsive'         => 'boolean',
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
}