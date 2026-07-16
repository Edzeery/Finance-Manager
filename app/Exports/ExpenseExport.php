<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpenseExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(private array $filters = []) {}

    public function collection()
    {
        return Expense::with('category')
            ->active()
            ->when(! empty($this->filters['category']), fn ($q) => $q->where('category_id', $this->filters['category']))
            ->when(! empty($this->filters['type']), fn ($q) => $q->byType($this->filters['type']))
            ->when(! empty($this->filters['date_from']), fn ($q) => $q->whereDate('date', '>=', $this->filters['date_from']))
            ->when(! empty($this->filters['date_to']), fn ($q) => $q->whereDate('date', '<=', $this->filters['date_to']))
            ->when(! empty($this->filters['search']), function ($q) {
                $term = '%'.$this->filters['search'].'%';
                $q->where(function ($q) use ($term) {
                    $q->where('description', 'like', $term)->orWhere('notes', 'like', $term);
                });
            })
            ->orderBy('date', 'desc')
            ->get()
            ->map(fn ($expense) => [
                __('general.date') => $expense->date->format('Y-m-d'),
                __('general.description') => $expense->description,
                __('general.category') => $expense->category ? ($expense->category->name_ar ?: $expense->category->name_en) : '',
                __('expense.type') => $expense->is_recurring ? __('expense.recurring') : ($expense->category?->type === 'fixed' ? __('expense.fixed') : __('expense.variable')),
                __('general.amount') => $expense->amount,
                __('general.recurring') => $expense->is_recurring ? __('general.yes') : __('general.no'),
                __('general.notes') => $expense->notes,
            ]);
    }

    public function headings(): array
    {
        return [
            __('general.date'),
            __('general.description'),
            __('general.category'),
            __('expense.type'),
            __('general.amount'),
            __('general.recurring'),
            __('general.notes'),
        ];
    }
}
