<?php

namespace App\Http\Requests\Zakat;

use Illuminate\Foundation\Http\FormRequest;

class StoreZakatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gold_price' => ['required', 'numeric', 'min:0'],
            'silver_price' => ['required', 'numeric', 'min:0'],
            'gold_value' => ['nullable', 'numeric', 'min:0'],
            'silver_value' => ['nullable', 'numeric', 'min:0'],
            'cash_value' => ['nullable', 'numeric', 'min:0'],
            'bank_value' => ['nullable', 'numeric', 'min:0'],
            'ccp_value' => ['nullable', 'numeric', 'min:0'],
            'business_goods_value' => ['nullable', 'numeric', 'min:0'],
            'stocks_value' => ['nullable', 'numeric', 'min:0'],
            'crypto_value' => ['nullable', 'numeric', 'min:0'],
            'real_estate_value' => ['nullable', 'numeric', 'min:0'],
            'expected_receivables' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'save' => ['boolean'],
        ];
    }
}
