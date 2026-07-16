<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(private array $filters = []) {}

    public function collection()
    {
        return Asset::query()
            ->when(! empty($this->filters['type']), fn ($q) => $q->byType($this->filters['type']))
            ->when(! empty($this->filters['search']), function ($q) {
                $term = '%'.$this->filters['search'].'%';
                $q->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)->orWhere('notes', 'like', $term);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($asset) => [
                __('asset.type') => $asset->type->label(),
                __('asset.name') => $asset->name,
                __('asset.quantity') => $asset->quantity,
                __('asset.value') => $asset->total_value,
                __('asset.liquid') => $asset->is_liquid ? __('general.yes') : __('general.no'),
                __('asset.zakatable') => $asset->is_zakatable ? __('general.yes') : __('general.no'),
                __('general.notes') => $asset->notes,
            ]);
    }

    public function headings(): array
    {
        return [
            __('asset.type'),
            __('asset.name'),
            __('asset.quantity'),
            __('asset.value'),
            __('asset.liquid'),
            __('asset.zakatable'),
            __('general.notes'),
        ];
    }
}
