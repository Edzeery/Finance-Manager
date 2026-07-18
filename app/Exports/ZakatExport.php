<?php

namespace App\Exports;

use App\Models\ZakatRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ZakatExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(private array $filters = []) {}

    public function collection()
    {
        return ZakatRecord::orderBy('calculation_date', 'desc')
            ->get()
            ->map(fn ($record) => [
                __('zakat.date') => $record->calculation_date->format('Y-m-d'),
                __('zakat.hijri_year') => $record->hijri_year,
                __('zakat.total_wealth') => $record->total_wealth,
                __('zakat.total_zakatable') => $record->total_zakatable,
                __('zakat.total_debts') => $record->total_debts,
                __('zakat.net_zakatable') => $record->net_zakatable,
                __('zakat.exceeds_nisab') => $record->exceeds_nisab ? __('general.yes') : __('general.no'),
                __('zakat.amount') => $record->zakat_amount,
                __('general.notes') => $record->notes,
            ]);
    }

    public function headings(): array
    {
        return [
            __('zakat.date'),
            __('zakat.hijri_year'),
            __('zakat.total_wealth'),
            __('zakat.total_zakatable'),
            __('zakat.total_debts'),
            __('zakat.net_zakatable'),
            __('zakat.exceeds_nisab'),
            __('zakat.amount'),
            __('general.notes'),
        ];
    }
}
