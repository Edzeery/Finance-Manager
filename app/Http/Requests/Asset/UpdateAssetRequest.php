<?php

namespace App\Http\Requests\Asset;

use App\Enums\AssetType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(AssetType::values())],
            'karat' => ['nullable', 'integer', 'in:24,22,21,18,14,10'],
            'weight_grams' => ['nullable', 'numeric', 'min:0'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'total_value' => ['nullable', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'is_liquid' => ['boolean'],
            'is_zakatable' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
