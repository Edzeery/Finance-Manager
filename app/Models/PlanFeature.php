<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PlanFeature extends Model
{
    protected $fillable = [
        'slug', 'name_en', 'name_ar', 'name_fr',
        'type', 'icon', 'sort_order', 'is_core',
    ];

    protected function casts(): array
    {
        return [
            'is_core' => 'boolean',
        ];
    }

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'plan_plan_feature', 'plan_feature_id', 'plan_id')
            ->withPivot(['value', 'sort_order'])
            ->withTimestamps();
    }

    public function scopeCore($query)
    {
        return $query->where('is_core', true);
    }
}
