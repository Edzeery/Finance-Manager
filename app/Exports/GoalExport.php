<?php

namespace App\Exports;

use App\Models\FinancialGoal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GoalExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(private array $filters = []) {}

    public function collection()
    {
        return FinancialGoal::query()
            ->when(!empty($this->filters['search']), function ($q) {
                $term = '%' . $this->filters['search'] . '%';
                $q->where(function ($q) use ($term) {
                    $q->where('name_ar', 'like', $term)
                      ->orWhere('name_fr', 'like', $term)
                      ->orWhere('name_en', 'like', $term)
                      ->orWhere('notes', 'like', $term);
                });
            })
            ->orderBy('target_date')
            ->get()
            ->map(fn($goal) => [
                __('goal.name')           => $goal->name_en,
                __('goal.target_amount')  => $goal->target_amount,
                __('goal.current_amount') => $goal->current_amount,
                __('goal.progress')        => number_format($goal->progress, 1) . '%',
                __('goal.target_date')    => $goal->target_date->format('Y-m-d'),
                __('goal.status')         => $goal->status->label(),
                __('general.notes')       => $goal->notes,
            ]);
    }

    public function headings(): array
    {
        return [
            __('goal.name'),
            __('goal.target_amount'),
            __('goal.current_amount'),
            __('goal.progress'),
            __('goal.target_date'),
            __('goal.status'),
            __('general.notes'),
        ];
    }
}
