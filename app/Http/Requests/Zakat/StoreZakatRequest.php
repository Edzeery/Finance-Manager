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
            'gold_items' => ['nullable', 'array'],
            'gold_items.*.karat' => ['required_with:gold_items.*', 'integer', 'in:24,22,21,18,14,10'],
            'gold_items.*.weight' => ['required_with:gold_items.*', 'numeric', 'min:0'],
            'gold_items.*.price' => ['nullable', 'numeric', 'min:0'],
            'silver_price' => ['required', 'numeric', 'min:0'],
            'silver_weight' => ['nullable', 'numeric', 'min:0'],
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
