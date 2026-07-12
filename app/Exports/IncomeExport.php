<?php

namespace App\Exports;

use App\Models\Income;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class IncomeExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(private array $filters = []) {}

    public function collection()
    {
        return Income::with('category')
            ->active()
            ->when(!empty($this->filters['category']), fn($q) => $q->where('category_id', $this->filters['category']))
            ->when(!empty($this->filters['type']), fn($q) => $q->byType($this->filters['type']))
            ->when(!empty($this->filters['date_from']), fn($q) => $q->whereDate('date', '>=', $this->filters['date_from']))
            ->when(!empty($this->filters['date_to']), fn($q) => $q->whereDate('date', '<=', $this->filters['date_to']))
            ->when(!empty($this->filters['search']), function ($q) {
                $term = '%' . $this->filters['search'] . '%';
                $q->where(function ($q) use ($term) {
                    $q->where('description', 'like', $term)->orWhere('notes', 'like', $term);
                });
            })
            ->orderBy('date', 'desc')
            ->get()
            ->map(fn($income) => [
                __('general.date')        => $income->date->format('Y-m-d'),
                __('general.description') => $income->description,
                __('general.category')    => $income->category ? ($income->category->name_ar ?: $income->category->name_en) : '',
                __('income.type')         => $income->is_recurring ? __('income.recurring') : ($income->category?->type === 'fixed' ? __('income.fixed') : __('income.variable')),
                __('general.amount')      => $income->amount,
                __('general.recurring')   => $income->is_recurring ? __('general.yes') : __('general.no'),
                __('general.notes')       => $income->notes,
            ]);
    }

    public function headings(): array
    {
        return [
            __('general.date'),
            __('general.description'),
            __('general.category'),
            __('income.type'),
            __('general.amount'),
            __('general.recurring'),
            __('general.notes'),
        ];
    }
}
