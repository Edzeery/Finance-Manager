<?php

namespace App\Exports;

use App\Models\Expense;
use App\Models\Income;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionsExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(private array $filters = []) {}

    public function collection()
    {
        $incomes = Income::with('category')
            ->active()
            ->get()
            ->map(fn ($i) => [
                'date' => $i->date->format('Y-m-d'),
                'type' => __('general.income'),
                'category' => $i->category ? ($i->category->name_ar ?: $i->category->name_en) : '',
                'description' => $i->description,
                'amount' => $i->amount,
                'sign' => '+',
            ]);

        $expenses = Expense::with('category')
            ->active()
            ->get()
            ->map(fn ($e) => [
                'date' => $e->date->format('Y-m-d'),
                'type' => __('general.expense'),
                'category' => $e->category ? ($e->category->name_ar ?: $e->category->name_en) : '',
                'description' => $e->description,
                'amount' => $e->amount,
                'sign' => '-',
            ]);

        return $incomes->concat($expenses)
            ->sortByDesc('date')
            ->values()
            ->map(fn ($t) => [
                __('general.date') => $t['date'],
                __('general.type') => $t['type'],
                __('general.category') => $t['category'],
                __('general.description') => $t['description'],
                __('general.amount') => $t['sign'].number_format($t['amount'], 2),
            ]);
    }

    public function headings(): array
    {
        return [
            __('general.date'),
            __('general.type'),
            __('general.category'),
            __('general.description'),
            __('general.amount'),
        ];
    }
}
