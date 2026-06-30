<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile',
        'customer_name',
        'email',
        'phone',
        'package_name',
        'amount',
        'payment_method',
        'payment_type',              // 'front' | 'upsell'
        'status',
        'payment_link',
        'stripe_payment_intent_id',
        'stripe_client_secret',
        'paypal_order_id',
        'cashapp_payment_intent_id',
        'zelle_receipt_path',
        'transaction_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // ── Scopes ──────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // ── Helpers ─────────────────────────────────────────────

    public function amountInCents(): int
    {
        return (int) round($this->amount * 100);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isZelle(): bool
    {
        return $this->payment_method === 'zelle';
    }

    public function isVenmo(): bool
    {
        return $this->payment_method === 'venmo';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}