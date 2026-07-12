<?php

namespace App\Http\Requests\Budget;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_fr' => ['nullable', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['monthly', 'yearly', 'custom'])],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'categories' => ['nullable', 'array'],
            'categories.*.category_id' => ['required', 'exists:expense_categories,id'],
            'categories.*.allocated_amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
