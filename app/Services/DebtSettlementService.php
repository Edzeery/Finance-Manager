<?php

namespace App\Services;

use App\Enums\DebtType;
use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Income;
use App\Models\IncomeCategory;
use Carbon\Carbon;

class DebtSettlementService
{
    /**
     * Materialise the expense/income entry for a debt payment.
     *
     * In the default "settlement" recognition mode (count_at_incurrence = false),
     * each payment creates one linked transaction (expense for a payable,
     * income for a receivable). The operation is idempotent.
     */
    public function settle(DebtPayment $payment): void
    {
        $debt = $payment->debt;

        if (! $debt || $debt->count_at_incurrence) {
            return;
        }

        if ($debt->type === DebtType::Owing) {
            if ($payment->expense_id !== null) {
                return;
            }

            $expense = Expense::create([
                'user_id' => $debt->user_id,
                'workspace_id' => $debt->workspace_id,
                'category_id' => $this->resolveExpenseCategoryId($debt),
                'amount' => $payment->amount,
                'description' => $this->descriptionFor($debt),
                'date' => $payment->payment_date,
                'is_recurring' => false,
                'notes' => $this->notesFor($debt),
                'debt_id' => $debt->id,
            ]);

            $payment->update(['expense_id' => $expense->id]);

            return;
        }

        if ($payment->income_id !== null) {
            return;
        }

        $income = Income::create([
            'user_id' => $debt->user_id,
            'workspace_id' => $debt->workspace_id,
            'category_id' => $this->resolveIncomeCategoryId($debt),
            'amount' => $payment->amount,
            'description' => $this->descriptionFor($debt),
            'date' => $payment->payment_date,
            'is_recurring' => false,
            'notes' => $this->notesFor($debt),
            'debt_id' => $debt->id,
        ]);

        $payment->update(['income_id' => $income->id]);
    }

    /**
     * Keep the linked transaction in sync with the payment (amount / date).
     */
    public function refresh(DebtPayment $payment): void
    {
        $transaction = match (true) {
            $payment->expense_id !== null => $payment->expense,
            $payment->income_id !== null => $payment->income,
            default => null,
        };

        if (! $transaction) {
            return;
        }

        $transaction->amount = $payment->amount;
        $transaction->date = Carbon::parse($payment->payment_date);

        if ($transaction->isDirty(['amount', 'date'])) {
            $transaction->save();
        }
    }

    /**
     * Reverse the linked transaction when a payment is removed.
     */
    public function reverse(DebtPayment $payment): void
    {
        if ($payment->expense_id !== null) {
            $payment->expense()->delete();
        }

        if ($payment->income_id !== null) {
            $payment->income()->delete();
        }
    }

    public function resolveExpenseCategoryId(Debt $debt): int
    {
        if ($debt->expense_category_id !== null) {
            return $debt->expense_category_id;
        }

        return $this->fallbackCategoryId(ExpenseCategory::class, $debt, [
            'name_ar' => 'سداد ديون',
            'name_fr' => 'Règlement de dettes',
            'name_en' => 'Debt settlement',
        ]);
    }

    public function resolveIncomeCategoryId(Debt $debt): int
    {
        if ($debt->income_category_id !== null) {
            return $debt->income_category_id;
        }

        return $this->fallbackCategoryId(IncomeCategory::class, $debt, [
            'name_ar' => 'تحصيل ديون',
            'name_fr' => 'Recouvrement de créances',
            'name_en' => 'Debt collection',
        ]);
    }

    /**
     * @param  class-string<ExpenseCategory|IncomeCategory>  $modelClass
     * @param  array{name_ar: string, name_fr: string, name_en: string}  $names
     */
    private function fallbackCategoryId(string $modelClass, Debt $debt, array $names): int
    {
        $category = $modelClass::withoutWorkspace()
            ->where('workspace_id', $debt->workspace_id)
            ->where('name_ar', $names['name_ar'])
            ->first();

        if ($category) {
            return $category->id;
        }

        $category = $modelClass::create([
            'user_id' => $debt->user_id,
            'workspace_id' => $debt->workspace_id,
            'name_ar' => $names['name_ar'],
            'name_fr' => $names['name_fr'],
            'name_en' => $names['name_en'],
            'icon' => 'bi-credit-card',
            'color' => '#6b7280',
            'type' => 'debt',
            'is_active' => true,
            'sort_order' => 999,
        ]);

        return $category->id;
    }

    private function descriptionFor(Debt $debt): string
    {
        return $debt->description
            ?: (string) __('debt.settlement_description', ['name' => $debt->counterparty_name]);
    }

    private function notesFor(Debt $debt): string
    {
        return (string) __('debt.settlement_notes', ['name' => $debt->counterparty_name]);
    }
}
