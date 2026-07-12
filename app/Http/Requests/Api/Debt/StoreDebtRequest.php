<?php

namespace App\Http\Requests\Api\Debt;

use App\Http\Requests\Api\ApiRequest;

class StoreDebtRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:owed,owing'],
            'counterparty_name' => ['required', 'string', 'max:255'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'due_date' => ['nullable', 'date'],
        ];
    }
}
