<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanPrice extends Model
{
    protected $fillable = [
        'plan_id', 'billing_period', 'currency',
        'price', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForPeriod($query, string $period)
    {
        return $query->where('billing_period', $period);
    }

    public function scopeForCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }
}
