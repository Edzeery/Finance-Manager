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

    /**
     * يحسب إجمالي المصروف الفعلي لتصنيف مصروفات معيّن ضمن فترة زمنية،
     * على مستوى الـ workspace بالكامل (لا يُفلتر حسب user_id — قرار معماري مقصود
     * ليتوافق مع عمل الفريق: كل مصروفات الـ workspace بنفس التصنيف تُحتسب).
     */
    public static function calculateSpentAmount(
        int $expenseCategoryId,
        $startDate,
        $endDate,
        ?int $excludeExpenseId = null
    ): float {
        $query = Expense::where('category_id', $expenseCategoryId)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($excludeExpenseId) {
            $query->where('id', '!=', $excludeExpenseId);
        }

        return (float) $query->sum('amount');
    }

    public function recalculateSpentAmount(): void
    {
        $total = self::calculateSpentAmount(
            $this->expense_category_id,
            $this->budget->start_date,
            $this->budget->end_date ?? now()
        );

        $this->update(['spent_amount' => $total]);
    }
}
