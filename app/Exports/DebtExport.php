<?php

namespace App\Exports;

use App\Enums\DebtType;
use App\Models\Debt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DebtExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(private array $filters = []) {}

    public function collection()
    {
        return Debt::with('payments')
            ->when(!empty($this->filters['status']), fn($q) => $q->where('status', $this->filters['status']))
            ->when(!empty($this->filters['type']), fn($q) => $q->where('type', $this->filters['type']))
            ->when(!empty($this->filters['search']), function ($q) {
                $term = '%' . $this->filters['search'] . '%';
                $q->where(function ($q) use ($term) {
                    $q->where('counterparty_name', 'like', $term)->orWhere('notes', 'like', $term);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($debt) => [
                __('debt.type')            => $debt->type === DebtType::Owed ? __('debt.owed') : __('debt.owing'),
                __('debt.counterparty')    => $debt->counterparty_name,
                __('debt.total_amount')    => $debt->total_amount,
                __('debt.paid_amount')     => $debt->paid_amount,
                __('debt.remaining')       => $debt->remaining_amount,
                __('debt.due_date')        => $debt->due_date?->format('Y-m-d'),
                __('debt.status')          => $debt->status->label(),
                __('general.notes')        => $debt->notes,
            ]);
    }

    public function headings(): array
    {
        return [
            __('debt.type'),
            __('debt.counterparty'),
            __('debt.total_amount'),
            __('debt.paid_amount'),
            __('debt.remaining'),
            __('debt.due_date'),
            __('debt.status'),
            __('general.notes'),
        ];
    }
}
