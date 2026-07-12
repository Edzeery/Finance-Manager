<?php

namespace App\Http\Requests\Api\Asset;

use App\Enums\AssetType;
use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', Rule::in(AssetType::values())],
            'total_value' => ['sometimes', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'is_liquid' => ['boolean'],
            'is_zakatable' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
