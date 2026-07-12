<?php

namespace App\Exports;

use App\Models\Budget;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BudgetExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(private array $filters = []) {}

    public function collection()
    {
        return Budget::with('categories.category')
            ->active()
            ->when(!empty($this->filters['search']), function ($q) {
                $term = '%' . $this->filters['search'] . '%';
                $q->where(function ($q) use ($term) {
                    $q->where('name_ar', 'like', $term)
                      ->orWhere('name_fr', 'like', $term)
                      ->orWhere('name_en', 'like', $term)
                      ->orWhere('notes', 'like', $term);
                });
            })
            ->latest()
            ->get()
            ->map(fn($budget) => [
                __('budget.name')         => $budget->name_en,
                __('budget.type')         => __("budget.{$budget->type}"),
                __('budget.total')        => $budget->total_amount,
                __('budget.total_spent')  => $budget->total_spent,
                __('budget.adherence')    => number_format($budget->adherence_rate, 1) . '%',
                __('budget.start_date')   => $budget->start_date->format('Y-m-d'),
                __('budget.end_date')     => $budget->end_date->format('Y-m-d'),
                __('general.notes')       => $budget->notes,
            ]);
    }

    public function headings(): array
    {
        return [
            __('budget.name'),
            __('budget.type'),
            __('budget.total'),
            __('budget.total_spent'),
            __('budget.adherence'),
            __('budget.start_date'),
            __('budget.end_date'),
            __('general.notes'),
        ];
    }
}
