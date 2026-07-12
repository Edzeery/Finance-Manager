<x-app-layout>
    <x-slot:title>{{ __('zakat.history') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('zakat.history') }}</x-slot>

    <div class="d-flex justify-content-end gap-2 mb-3">
        @php $canExportZakat = auth()->user()->hasPermission('zakat.export'); @endphp
        <x-data-toolbar entity="zakat" :show-import="false" :show-export="$canExportZakat" />
    </div>

    <div class="card-custom">
        <div class="card-body p-0">
            @if($records->count())
                <div class="table-responsive">
                    <table class="table-custom">
                    <thead>
                        <tr>
                            <th>{{ __('zakat.calculation_date') }}</th>
                            <th class="text-end">{{ __('zakat.total_wealth') }}</th>
                            <th class="text-end">{{ __('zakat.total_zakatable') }}</th>
                            <th>{{ __('zakat.exceeds_nisab') }}</th>
                            <th class="text-end">{{ __('zakat.zakat_amount') }}</th>
                            <th class="text-center" style="width:80px">{{ __('general.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                            <tr>
                                <td>{{ $record->calculation_date->format('Y/m/d') }}</td>
                                <td class="text-end">{{ number_format($record->total_wealth, 2) }}</td>
                                <td class="text-end">{{ number_format($record->total_zakatable, 2) }}</td>
                                <td>
                                    @if($record->exceeds_nisab)
                                        <span style="color:var(--success)"><i class="bi bi-check-circle"></i></span>
                                    @else
                                        <span style="color:var(--danger)"><i class="bi bi-x-circle"></i></span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold" style="color:var(--accent)">{{ number_format($record->zakat_amount, 2) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('zakat.report', $record) }}" class="action-btn" title="{{ __('zakat.report') }}">
                                        <i class="bi bi-file-text"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                <div class="p-3">
                    {{ $records->links() }}
                </div>
            @else
                @include('components.empty-state', [
                    'icon' => 'bi-clock-history',
                    'title' => __('zakat.no_records'),
                    'message' => __('zakat.calculate_first'),
                    'action' => route('zakat.calculator'),
                    'actionText' => __('zakat.calculate'),
                ])
            @endif
        </div>
    </div>
</x-app-layout>
