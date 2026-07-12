<?php

namespace App\Http\Requests\Api\Goal;

use App\Http\Requests\Api\ApiRequest;

class UpdateGoalRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_en' => ['sometimes', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'name_fr' => ['nullable', 'string', 'max:255'],
            'target_amount' => ['sometimes', 'numeric', 'min:0'],
            'current_amount' => ['nullable', 'numeric', 'min:0'],
            'target_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:in_progress,completed,cancelled'],
        ];
    }
}
