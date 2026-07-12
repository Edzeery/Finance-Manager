<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_amount',
        'max_uses', 'used_count', 'starts_at', 'expires_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_amount' => 'decimal:2',
            'max_uses' => 'integer',
            'used_count' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function paymentMethods(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethod::class, 'coupon_payment_method')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()));
    }

    public function scopeValid($query)
    {
        return $query->active()
            ->where(fn($q) => $q->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses'));
    }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;
        return true;
    }

    public function applyDiscount(float $amount): float
    {
        if ($this->type === 'percentage') {
            return round($amount * ($this->value / 100), 2);
        }

        return min($this->value, $amount);
    }

    public function markUsed(): void
    {
        Coupon::where('id', $this->id)
            ->where(function ($q) {
                $q->whereNull('max_uses')
                  ->orWhereColumn('used_count', '<', 'max_uses');
            })
            ->increment('used_count');
    }
}
