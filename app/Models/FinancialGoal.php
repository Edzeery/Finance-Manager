<?php

namespace App\Models;

use App\Enums\GoalStatus;
use App\Models\Concerns\BelongsToWorkspace;
use App\Models\Scopes\WorkspaceScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialGoal extends Model
{
    use BelongsToWorkspace, HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::addGlobalScope(new WorkspaceScope);
    }

    protected $fillable = [
        'user_id', 'workspace_id', 'name_ar', 'name_fr', 'name_en', 'target_amount',
        'current_amount', 'target_date', 'status', 'icon', 'color', 'notes', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => GoalStatus::class,
            'target_amount' => 'decimal:2',
            'current_amount' => 'decimal:2',
            'target_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }

        return min(100, round(($this->current_amount / $this->target_amount) * 100, 2));
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (! $this->target_date) {
            return null;
        }

        return max(0, now()->startOfDay()->diffInDays($this->target_date, false));
    }

    public function getMonthlyTargetAttribute(): float
    {
        if (! $this->target_date || $this->daysRemaining <= 0) {
            return $this->target_amount;
        }
        $months = max(1, ceil($this->daysRemaining / 30));

        return round(($this->target_amount - $this->current_amount) / $months, 2);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', GoalStatus::InProgress);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', GoalStatus::Completed);
    }
}
