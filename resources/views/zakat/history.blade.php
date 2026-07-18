<x-app-layout>
    <x-slot:title>{{ __('zakat.history') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('zakat.history') }}</x-slot>

    @include('zakat._nav')

    <x-filter-tabs :tabs="$tabs" current="{{ $exceedsNisab }}" keyParam="exceeds_nisab" defaultKey="all" :preserve="['search','date_from','date_to','per_page']" />

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <form method="GET" action="{{ route('zakat.history') }}" class="d-flex flex-wrap align-items-center gap-2">
            <x-search-filter name="search" :value="request('search')" size="sm" />

            <input type="date" name="date_from" class="form-custom" style="width:auto;padding:6px 12px;font-size:13px" value="{{ request('date_from') }}" onchange="this.form.submit()">
            <input type="date" name="date_to" class="form-custom" style="width:auto;padding:6px 12px;font-size:13px" value="{{ request('date_to') }}" onchange="this.form.submit()">

            <x-clear-filters :filters="['exceeds_nisab','search','date_from','date_to']" :route="route('zakat.history')" />
        </form>

        <div class="d-flex gap-2 align-items-center">
            @php $canExportZakat = auth()->user()->hasPermission('zakat.export'); @endphp
            <x-data-toolbar entity="zakat" :show-import="false" :show-export="$canExportZakat" />
            <x-per-page :current="request('per_page', 15)" :route="route('zakat.history')" :preserve="['exceeds_nisab','search','date_from','date_to']" />
        </div>
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
                                        <x-status-icon domain="general" status="success" set="bi" />
                                    @else
                                        <x-status-icon domain="general" status="failed" set="bi" />
                                    @endif
                                </td>
                                <td class="text-start fw-bold" style="color:var(--accent)">{{ number_format($record->zakat_amount, 2) }}</td>
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
                <div class="p-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <x-pagination-info :items="$records" />
                    <div>
                        {{ $records->appends(request()->except('page'))->links() }}
                    </div>
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
