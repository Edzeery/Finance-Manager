<?php

namespace App\Models;

use App\Enums\DebtStatus;
use App\Enums\DebtType;
use App\Models\Concerns\BelongsToWorkspace;
use App\Models\Scopes\WorkspaceScope;
use App\Services\DebtSettlementService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debt extends Model
{
    use BelongsToWorkspace, HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::addGlobalScope(new WorkspaceScope);

        static::forceDeleting(function (Debt $debt) {
            $payments = $debt->payments()->with(['expense', 'income'])->get();

            foreach ($payments as $payment) {
                app(DebtSettlementService::class)->reverse($payment);
            }
        });
    }

    protected $fillable = [
        'user_id', 'workspace_id', 'type', 'counterparty_name', 'total_amount', 'paid_amount',
        'due_date', 'status', 'description', 'reminder_date', 'notes',
        'count_at_incurrence', 'expense_category_id', 'income_category_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => DebtType::class,
            'status' => DebtStatus::class,
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_date' => 'date',
            'reminder_date' => 'date',
            'count_at_incurrence' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<DebtPayment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class);
    }

    /**
     * @return BelongsTo<ExpenseCategory, $this>
     */
    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    /**
     * @return BelongsTo<IncomeCategory, $this>
     */
    public function incomeCategory(): BelongsTo
    {
        return $this->belongsTo(IncomeCategory::class);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    public function getProgressAttribute(): float
    {
        if ($this->total_amount <= 0) {
            return 0;
        }

        return round(($this->paid_amount / $this->total_amount) * 100, 2);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== DebtStatus::Paid;
    }

    public function scopeOwed($query)
    {
        return $query->where('type', DebtType::Owed);
    }

    public function scopeOwing($query)
    {
        return $query->where('type', DebtType::Owing);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [DebtStatus::Active, DebtStatus::Partial, DebtStatus::Overdue]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', DebtStatus::Overdue);
    }
}
