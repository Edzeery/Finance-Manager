<?php

namespace App\Http\Requests\Api\Budget;

use App\Http\Requests\Api\ApiRequest;

class StoreBudgetRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'name_fr' => ['nullable', 'string', 'max:255'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'in:monthly,yearly,custom'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
