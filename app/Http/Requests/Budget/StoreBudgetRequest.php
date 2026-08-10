<?php

namespace App\Http\Requests\Budget;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $workspaceId = auth()->user()->currentWorkspace->id;

        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_fr' => ['nullable', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['monthly', 'yearly', 'custom'])],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'categories' => ['nullable', 'array'],
            'categories.*.category_id' => [
                'required',
                Rule::exists('expense_categories', 'id')
                    ->where(fn ($query) => $query
                        ->where('workspace_id', $workspaceId)
                        ->orWhereNull('workspace_id')),
            ],
            'categories.*.allocated_amount' => ['nullable', 'numeric', 'min:0'],
            'categories.*.percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'categories.*.use_percentage' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $total = 0;

            foreach ($this->input('categories', []) as $cat) {
                if ((string) ($cat['use_percentage'] ?? '0') !== '1') {
                    continue;
                }

                if (isset($cat['percentage']) && $cat['percentage'] !== '') {
                    $total += (float) $cat['percentage'];
                }
            }

            if ($total > 100) {
                $validator->errors()->add('categories', __('budget.percentage_sum_exceeds', ['total' => rtrim(rtrim(number_format($total, 2), '0'), '.')]));
            }
        });
    }
}
