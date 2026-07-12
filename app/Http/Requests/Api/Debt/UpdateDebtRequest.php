<?php

namespace App\Http\Requests\Api\Debt;

use App\Http\Requests\Api\ApiRequest;

class UpdateDebtRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'in:owed,owing'],
            'counterparty_name' => ['sometimes', 'string', 'max:255'],
            'total_amount' => ['sometimes', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'due_date' => ['nullable', 'date'],
        ];
    }
}
