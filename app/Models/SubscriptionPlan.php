<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $attributes = [
        'sort_order' => 0,
        'trial_days' => null,
    ];

    protected $fillable = [
        'name', 'name_en', 'name_ar', 'name_fr',
        'slug',
        'description', 'description_en', 'description_ar', 'description_fr',
        'sort_order', 'is_free', 'trial_days',
        'yearly_discount_percent',
        'is_active', 'is_public',
        'button_text', 'button_text_en', 'button_text_ar', 'button_text_fr',
        'button_link',
    ];

    protected $appends = ['yearly_price'];

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        $column = "name_{$locale}";
        return $this->attributes[$column] ?? $this->name_en;
    }

    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = $value;
        $this->attributes['name_en'] = $value;
    }

    public function setNameEnAttribute(string $value): void
    {
        $this->attributes['name_en'] = $value;
        $this->attributes['name'] = $value;
    }

    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        $column = "description_{$locale}";
        return $this->attributes[$column] ?? $this->description_en;
    }

    public function setDescriptionAttribute(?string $value): void
    {
        $this->attributes['description'] = $value;
        $this->attributes['description_en'] = $value;
    }

    public function setDescriptionEnAttribute(?string $value): void
    {
        $this->attributes['description_en'] = $value;
        $this->attributes['description'] = $value;
    }

    public function getButtonTextAttribute(): ?string
    {
        $locale = app()->getLocale();
        $column = "button_text_{$locale}";
        return $this->attributes[$column] ?? $this->button_text_en;
    }

    public function setButtonTextAttribute(?string $value): void
    {
        $this->attributes['button_text'] = $value;
        $this->attributes['button_text_en'] = $value;
    }

    public function setButtonTextEnAttribute(?string $value): void
    {
        $this->attributes['button_text_en'] = $value;
        $this->attributes['button_text'] = $value;
    }

    protected function casts(): array
    {
        return [
            'is_free' => 'boolean',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'trial_days' => 'integer',
        ];
    }

    public function hasTrial(): bool
    {
        $days = $this->attributes['trial_days'] ?? null;
        return $days !== null && (int) $days > 0;
    }

    public function getTrialDaysAttribute(): ?int
    {
        $days = $this->attributes['trial_days'] ?? null;
        if ($days === null) return null;
        return (int) $days > 0 ? (int) $days : null;
    }

    public function getMonthlyPriceAttribute(): float
    {
        $price = $this->activePrices()->forPeriod('monthly')->value('price');
        if ($price !== null) {
            return (float) $price;
        }
        return (float) ($this->attributes['monthly_price'] ?? 0);
    }

    public function getYearlyPriceAttribute(): float
    {
        $price = $this->activePrices()->forPeriod('yearly')->value('price');
        if ($price !== null) {
            return (float) $price;
        }
        $monthly = (float) ($this->attributes['monthly_price'] ?? $this->getPrice('monthly') ?? 0);
        $discount = (float) ($this->attributes['yearly_discount_percent'] ?? $this->getFeatureValue('yearly_discount_percent') ?? 0);
        if ($discount > 0 && $monthly > 0) {
            return round($monthly * 12 * (1 - $discount / 100), 2);
        }
        return round($monthly * 12, 2);
    }

    public function getPrice(string $billingPeriod, string $currency = 'USD'): ?float
    {
        $price = $this->activePrices()
            ->forPeriod($billingPeriod)
            ->forCurrency($currency)
            ->value('price');
        if ($price !== null) {
            return (float) $price;
        }
        if ($billingPeriod === 'monthly') {
            return $this->monthly_price ?: null;
        }
        return $this->yearly_price ?: null;
    }

    public function getMaxUsersAttribute(): ?int
    {
        $val = $this->getFeatureValue('users');
        if ($val === null || $val === 'custom' || $val === 'unlimited') return null;
        return (int) $val;
    }

    public function getMaxWorkspacesAttribute(): ?int
    {
        $val = $this->getFeatureValue('workspaces');
        if ($val === null || $val === 'custom' || $val === 'unlimited') return null;
        return (int) $val;
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function planFeatures(): BelongsToMany
    {
        return $this->belongsToMany(PlanFeature::class, 'plan_plan_feature', 'plan_id', 'plan_feature_id')
            ->withPivot(['value', 'sort_order'])
            ->withTimestamps()
            ->orderBy('plan_plan_feature.sort_order');
    }

    public function planPrices(): HasMany
    {
        return $this->hasMany(PlanPrice::class, 'plan_id');
    }

    public function activePrices(): HasMany
    {
        return $this->hasMany(PlanPrice::class, 'plan_id')->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function isFree(): bool
    {
        return (bool) $this->is_free;
    }

    public function isPaid(): bool
    {
        return !$this->isFree();
    }

    public function getFeatureValue(string $slug): ?string
    {
        if (!$this->relationLoaded('planFeatures')) {
            return $this->planFeatures()->where('slug', $slug)->value('plan_plan_feature.value');
        }

        $feature = $this->planFeatures->firstWhere('slug', $slug);
        return $feature?->pivot->value;
    }

    public function hasFeature(string $slug): bool
    {
        if (!$this->relationLoaded('planFeatures')) {
            return $this->planFeatures()->where('slug', $slug)->exists();
        }

        return $this->planFeatures->contains(fn($f) => $f->slug === $slug);
    }

    public function featureSlugs(): array
    {
        if (!$this->relationLoaded('planFeatures')) {
            return $this->planFeatures()->pluck('slug')->toArray();
        }

        return $this->planFeatures->pluck('slug')->toArray();
    }
}
