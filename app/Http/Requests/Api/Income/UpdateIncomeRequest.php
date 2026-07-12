<?php

namespace App\Http\Requests\Api\Income;

use App\Http\Requests\Api\ApiRequest;

class UpdateIncomeRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'exists:income_categories,id'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'date' => ['sometimes', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_recurring' => ['boolean'],
            'recurring_frequency' => ['nullable', 'required_if:is_recurring,true', 'in:daily,weekly,monthly,yearly'],
        ];
    }
}
