<?php

namespace App\Http\Requests\Api\Income;

use App\Http\Requests\Api\ApiRequest;

class StoreIncomeRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:income_categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_recurring' => ['boolean'],
            'recurring_frequency' => ['nullable', 'required_if:is_recurring,true', 'in:daily,weekly,monthly,yearly'],
        ];
    }
}
