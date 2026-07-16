<?php

namespace App\Models;

use App\Enums\RecurringFrequency;
use App\Models\Concerns\BelongsToWorkspace;
use App\Models\Scopes\WorkspaceScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use BelongsToWorkspace, HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::addGlobalScope(new WorkspaceScope);

        static::created(function (Expense $expense) {
            $expense->syncBudgetSpent();
        });

        static::updated(function (Expense $expense) {
            $expense->syncBudgetSpent();
        });

        static::deleted(function (Expense $expense) {
            $expense->syncBudgetSpent();
        });

        static::restored(function (Expense $expense) {
            $expense->syncBudgetSpent();
        });
    }

    public function syncBudgetSpent(): void
    {
        $categoryIds = [$this->category_id];
        $dates = [$this->date];

        if ($this->isDirty('category_id')) {
            $categoryIds[] = $this->getOriginal('category_id');
        }

        if ($this->isDirty('date')) {
            $dates[] = $this->getOriginal('date');
        }

        $categories = BudgetCategory::whereHas('budget', fn ($q) => $q->where('user_id', $this->user_id)
        )->whereIn('expense_category_id', array_unique($categoryIds))->get();

        foreach ($categories as $bc) {
            $start = $bc->budget->start_date;
            $end = $bc->budget->end_date ?? now();

            foreach ($dates as $date) {
                if ($date->between($start, $end)) {
                    $bc->recalculateSpentAmount();
                    break;
                }
            }
        }
    }

    protected $fillable = [
        'user_id', 'workspace_id', 'category_id', 'amount', 'description', 'date',
        'is_recurring', 'recurring_frequency', 'recurring_end_date',
        'is_archived', 'receipt_path', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date' => 'date',
            'is_recurring' => 'boolean',
            'recurring_frequency' => RecurringFrequency::class,
            'is_archived' => 'boolean',
            'recurring_end_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function scopeForPeriod($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)->whereYear('date', now()->year);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopeByType($query, $type)
    {
        if ($type === 'periodic') {
            return $query->where('is_recurring', true);
        }

        return $query->whereHas('category', fn ($q) => $q->where('type', $type));
    }
}
