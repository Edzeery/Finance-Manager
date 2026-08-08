<?php

namespace App\Http\Requests\Debt;

use App\Enums\DebtStatus;
use App\Enums\DebtType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDebtRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(DebtType::values())],
            'counterparty_name' => ['required', 'string', 'max:255'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(DebtStatus::values())],
            'description' => ['nullable', 'string', 'max:1000'],
            'reminder_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'count_at_incurrence' => ['boolean'],
            'expense_category_id' => ['nullable', 'exists:expense_categories,id'],
            'income_category_id' => ['nullable', 'exists:income_categories,id'],
        ];
    }
}
