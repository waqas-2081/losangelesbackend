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

    public function getBusinessDescriptionAttribute(): ?string
    {
        return $this->attributes['business_desc'] ?? null;
    }

    public function getBusinessIndustryAttribute(): ?string
    {
        return $this->attributes['industry'] ?? null;
    }

    public function getOverallFeelAttribute(): ?array
    {
        $value = $this->attributes['feel'] ?? null;
        if (is_array($value)) return $value;
        return $value ? json_decode($value, true) : null;
    }

    public function getCompetitorsReferencesAttribute(): ?string
    {
        return $this->attributes['competitors'] ?? null;
    }

    public function getPagesCountAttribute(): ?int
    {
        return isset($this->attributes['page_count']) ? (int) $this->attributes['page_count'] : null;
    }

    public function getPagesListAttribute(): ?string
    {
        return $this->attributes['page_names'] ?? null;
    }

    public function getWantsLogoRevampAttribute(): ?bool
    {
        return isset($this->attributes['revamp_logo']) ? (bool) $this->attributes['revamp_logo'] : null;
    }

    public function getNeedsHostingAttribute(): ?bool
    {
        return isset($this->attributes['need_hosting']) ? (bool) $this->attributes['need_hosting'] : null;
    }

    public function getNeedsResponsiveAttribute(): ?bool
    {
        return isset($this->attributes['need_responsive']) ? (bool) $this->attributes['need_responsive'] : null;
    }

    public function getProductsCountAttribute(): ?string
    {
        return $this->attributes['product_showcase_count'] ?? null;
    }

    public function getServicesCountNoPaymentAttribute(): ?string
    {
        return $this->attributes['service_showcase_count'] ?? null;
    }

    public function getServicesCountWithPriceAttribute(): ?string
    {
        return $this->attributes['services_prices'] ?? null;
    }

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