<?php

namespace App\Models;

use App\Models\Concerns\BelongsToWorkspace;
use App\Models\Scopes\WorkspaceScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetCategory extends Model
{
    use BelongsToWorkspace, HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope(new WorkspaceScope);
    }

    protected $fillable = ['budget_id', 'workspace_id', 'expense_category_id', 'allocated_amount', 'spent_amount'];

    protected function casts(): array
    {
        return [
            'allocated_amount' => 'decimal:2',
            'spent_amount' => 'decimal:2',
        ];
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function recalculateSpentAmount(): void
    {
        $total = Expense::where('category_id', $this->expense_category_id)
            ->whereBetween('date', [$this->budget->start_date, $this->budget->end_date ?? now()])
            ->sum('amount');

        $this->update(['spent_amount' => $total]);
    }
}
