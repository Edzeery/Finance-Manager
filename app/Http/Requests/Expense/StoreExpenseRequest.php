<?php

namespace App\Http\Requests\Expense;

use App\Enums\RecurringFrequency;
use App\Models\BudgetCategory;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:expense_categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_recurring' => ['boolean'],
            'recurring_frequency' => ['nullable', 'required_if:is_recurring,true', Rule::in(RecurringFrequency::values())],
            'recurring_end_date' => ['nullable', 'date', 'after_or_equal:date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $this->validateBudgetLimit($validator);
        });
    }

    protected function validateBudgetLimit(Validator $validator): void
    {
        $categoryId = $this->input('category_id');
        $amount = (float) ($this->input('amount') ?? 0);
        $date = $this->input('date');

        if (! $categoryId || ! $date || $amount <= 0) {
            return;
        }

        $expenseDate = Carbon::parse($date);
        $budgetCategories = BudgetCategory::whereHas('budget', fn ($q) => $q->active()
            ->where('start_date', '<=', $expenseDate)
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $expenseDate))
        )->where('expense_category_id', $categoryId)
            ->where('allocated_amount', '>', 0)
            ->get();

        foreach ($budgetCategories as $bc) {
            $start = $bc->budget->start_date;
            $end = $bc->budget->end_date ?? now();

            $totalSpent = Expense::where('category_id', $categoryId)
                ->whereBetween('date', [$start, $end])
                ->sum('amount');

            $remaining = $bc->allocated_amount - $totalSpent;

            if ($amount > $remaining) {
                $msg = __('validation.budget_exceeded', [
                    'allocated' => number_format($bc->allocated_amount, 2),
                    'spent' => number_format($totalSpent, 2),
                    'remaining' => number_format($remaining, 2),
                ]);
                $validator->errors()->add('amount', is_string($msg) ? $msg : 'Budget exceeded');

                return;
            }
        }
    }

    public function messages(): array
    {
        return [
            'category_id.required' => __('validation.required'),
            'amount.required' => __('validation.required'),
            'amount.numeric' => __('validation.numeric'),
            'amount.min' => __('validation.min.numeric'),
            'date.required' => __('validation.required'),
            'date.date' => __('validation.date'),
        ];
    }
}
