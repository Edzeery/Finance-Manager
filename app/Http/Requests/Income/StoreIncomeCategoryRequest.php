<?php

namespace App\Http\Requests\Income;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncomeCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_fr' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:7'],
            'type' => ['required', 'in:fixed,variable,recurring'],
            'is_active' => ['boolean'],
        ];
    }
}
