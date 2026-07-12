<?php

namespace App\Http\Requests\Api\Subscription;

use App\Http\Requests\Api\ApiRequest;

class ValidateCouponRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50'],
            'amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
