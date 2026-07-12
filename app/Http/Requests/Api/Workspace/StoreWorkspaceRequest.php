<?php

namespace App\Http\Requests\Api\Workspace;

use App\Http\Requests\Api\ApiRequest;

class StoreWorkspaceRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'in:personal,business'],
            'currency' => ['nullable', 'string', 'size:3'],
            'timezone' => ['nullable', 'string', 'max:64'],
        ];
    }
}
