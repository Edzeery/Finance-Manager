<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TaxRate extends Model
{
    protected $fillable = [
        'name', 'slug', 'rate', 'type', 'country', 'region', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function paymentMethods(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethod::class, 'payment_method_tax_rate')
            ->withPivot('charge_type')
            ->withTimestamps();
    }

    /**
     * Calculate the fee/tax for a given amount.
     * For percentage type: amount * rate / 100
     * For fixed type: returns the rate directly
     */
    public function calculateForAmount(float $amount): float
    {
        if ($this->type === 'percentage') {
            return round($amount * ($this->rate / 100), 2);
        }

        return $this->rate;
    }

    /**
     * @deprecated Use calculateForAmount() instead
     */
    public function calculateTax(float $amount): float
    {
        return $this->calculateForAmount($amount);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCountry($query, ?string $country)
    {
        if ($country) {
            return $query->where('country', $country);
        }

        return $query;
    }
}
