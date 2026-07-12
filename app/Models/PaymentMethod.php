<?php

namespace App\Models;

use App\Enums\PaymentMethodType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PaymentMethod extends Model
{
    protected $fillable = [
        'key', 'name', 'description', 'icon', 'type',
        'is_active', 'is_public', 'sort_order',
        'credentials',
    ];

    protected function casts(): array
    {
        return [
            'type' => PaymentMethodType::class,
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'sort_order' => 'integer',
            'credentials' => 'json',
        ];
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class, 'key', 'key');
    }

    public function credential(string $key, mixed $fallback = null): mixed
    {
        $value = $this->credentials[$key] ?? null;

        if ($value === null || $value === '') {
            return $fallback;
        }

        if (is_string($value)) {
            try {
                return decrypt($value);
            } catch (\Exception) {
            }
        }

        return $value;
    }

    public function taxRates(): BelongsToMany
    {
        return $this->belongsToMany(TaxRate::class, 'payment_method_tax_rate')
            ->withPivot('charge_type')
            ->withTimestamps();
    }

    public function gatewayFees(): BelongsToMany
    {
        return $this->belongsToMany(TaxRate::class, 'payment_method_tax_rate')
            ->withPivot('charge_type')
            ->wherePivot('charge_type', 'gateway_fee')
            ->withTimestamps();
    }

    public function taxesAdded(): BelongsToMany
    {
        return $this->belongsToMany(TaxRate::class, 'payment_method_tax_rate')
            ->withPivot('charge_type')
            ->wherePivot('charge_type', 'tax_added')
            ->withTimestamps();
    }

    public function taxesDisclosed(): BelongsToMany
    {
        return $this->belongsToMany(TaxRate::class, 'payment_method_tax_rate')
            ->withPivot('charge_type')
            ->wherePivot('charge_type', 'tax_disclosed')
            ->withTimestamps();
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_payment_method')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCurrency($query, string $currency)
    {
        return $query->whereHas('gateway', function ($q) use ($currency) {
            $q->whereNull('supported_currencies')
              ->orWhere('supported_currencies', '[]')
              ->orWhereJsonContains('supported_currencies', $currency);
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function isOnline(): bool
    {
        return $this->type === PaymentMethodType::Online;
    }

    public function isManual(): bool
    {
        return $this->type === PaymentMethodType::Manual;
    }

    public function isAutoComplete(): bool
    {
        return $this->type === PaymentMethodType::AutoComplete;
    }
}
