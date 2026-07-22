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
        $query = ZakatRecord::orderBy('calculation_date', 'desc');

        if (! empty($this->filters['date_from'])) {
            $query->whereDate('calculation_date', '>=', $this->filters['date_from']);
        }
        if (! empty($this->filters['date_to'])) {
            $query->whereDate('calculation_date', '<=', $this->filters['date_to']);
        }
        if (isset($this->filters['exceeds_nisab']) && $this->filters['exceeds_nisab'] !== 'all') {
            $query->where('exceeds_nisab', $this->filters['exceeds_nisab'] === 'yes');
        }

        return $query->get()
            ->map(fn ($record) => [
                __('zakat.date') => $record->calculation_date->format('Y-m-d'),
                __('zakat.hijri_year') => $record->hijri_year,
                __('zakat.calendar_type') => __('zakat.' . ($record->calendar_type ?? 'hijri')),
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
            __('zakat.calendar_type'),
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
