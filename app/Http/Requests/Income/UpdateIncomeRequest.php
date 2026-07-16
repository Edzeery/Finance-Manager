<?php

namespace App\Http\Requests\Income;

use App\Enums\RecurringFrequency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIncomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:income_categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'date' => ['required', 'date'],
            'is_recurring' => ['boolean'],
            'recurring_frequency' => ['required_if:is_recurring,true', 'nullable', Rule::in(RecurringFrequency::values())],
            'recurring_end_date' => ['nullable', 'date', 'after:date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        $locale = app()->getLocale();
        if ($locale === 'ar') {
            return [
                'category_id' => 'التصنيف',
                'amount' => 'المبلغ',
                'description' => 'الوصف',
                'date' => 'التاريخ',
                'is_recurring' => 'دخل متكرر',
                'recurring_frequency' => 'التكرار',
                'recurring_end_date' => 'تاريخ انتهاء التكرار',
                'notes' => 'ملاحظات',
            ];
        }
        if ($locale === 'fr') {
            return [
                'category_id' => 'Catégorie',
                'amount' => 'Montant',
                'description' => 'Description',
                'date' => 'Date',
                'is_recurring' => 'Récurrent',
                'recurring_frequency' => 'Fréquence',
                'recurring_end_date' => 'Date de fin',
                'notes' => 'Notes',
            ];
        }

        return [];
    }
}
