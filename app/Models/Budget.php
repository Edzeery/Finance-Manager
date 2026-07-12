<?php

namespace App\Models;

use App\Models\Concerns\BelongsToWorkspace;
use App\Models\Scopes\WorkspaceScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasFactory, SoftDeletes, BelongsToWorkspace;

    protected static function booted(): void
    {
        static::addGlobalScope(new WorkspaceScope);
    }

    protected $fillable = [
        'user_id', 'workspace_id', 'name_ar', 'name_fr', 'name_en', 'type',
        'total_amount', 'start_date', 'end_date', 'is_active', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(BudgetCategory::class);
    }

    public function getTotalAllocatedAttribute(): float
    {
        return $this->relationLoaded('categories')
            ? (float) $this->categories->sum('allocated_amount')
            : (float) $this->categories()->sum('allocated_amount');
    }

    public function getTotalSpentAttribute(): float
    {
        return $this->relationLoaded('categories')
            ? (float) $this->categories->sum('spent_amount')
            : (float) $this->categories()->sum('spent_amount');
    }

    public function getAdherenceRateAttribute(): float
    {
        if ($this->total_amount <= 0) return 0;
        return round(($this->totalSpent / $this->total_amount) * 100, 2);
    }

    public function getIsExceededAttribute(): bool
    {
        return $this->totalSpent > $this->total_amount;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeCurrent($query)
    {
        return $query->where('start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });
    }
}
