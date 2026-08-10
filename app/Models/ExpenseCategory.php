<?php

namespace App\Models;

use App\Models\Concerns\BelongsToWorkspace;
use App\Models\Concerns\HasCategories;
use App\Models\Scopes\WorkspaceScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    use BelongsToWorkspace, HasCategories, HasFactory;

    protected bool $allowsNullWorkspace = true;

    protected static function booted(): void
    {
        static::addGlobalScope(new WorkspaceScope);
    }

    protected $fillable = ['user_id', 'workspace_id', 'name_ar', 'name_fr', 'name_en', 'icon', 'color', 'type', 'is_active', 'sort_order', 'default_budget_percentage'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'default_budget_percentage' => 'decimal:2',
        ];
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'category_id');
    }

    public function budgetCategories(): HasMany
    {
        return $this->hasMany(BudgetCategory::class, 'expense_category_id');
    }

    public function getActiveBudgetInfo(): ?array
    {
        $bc = BudgetCategory::whereHas('budget', fn ($q) => $q->active()->current())
            ->where('expense_category_id', $this->id)
            ->where('allocated_amount', '>', 0)
            ->with('budget')
            ->first();

        if (! $bc) {
            return null;
        }

        $totalSpent = Expense::where('category_id', $this->id)
            ->whereBetween('date', [$bc->budget->start_date, $bc->budget->end_date ?? now()])
            ->sum('amount');

        return [
            'budget_name' => locale_name($bc->budget),
            'allocated' => (float) $bc->allocated_amount,
            'spent' => (float) $totalSpent,
            'remaining' => max(0, $bc->allocated_amount - $totalSpent),
        ];
    }
}
