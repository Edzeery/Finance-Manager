<x-app-layout>
    <x-slot:title>{{ __('general.search_results') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('general.search_results') }}</x-slot>
    <x-slot:page-description>@lang('general.search_for'): <strong>{{ $q }}</strong></x-slot>

    <div class="card-custom">
        <div class="card-body p-0">
            @if($results->count())
                <div class="table-responsive">
                    <table class="table-custom">
                    <thead>
                        <tr>
                            <th>{{ __('general.type') }}</th>
                            <th>{{ __('general.description') }}</th>
                            <th>{{ __('general.category') }}</th>
                            <th>{{ __('general.date') }}</th>
                            <th class="text-end">{{ __('general.amount') }}</th>
                            <th class="text-center">{{ __('general.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $r)
                            <tr>
                                <td>
                                    @php
                                        $typeStyles = [
                                            'income' => 'success',
                                            'expense' => 'danger',
                                            'debt' => 'warning',
                                            'asset' => 'info',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $typeStyles[$r->type] ?? 'secondary' }}">
                                        {{ __("{$r->type}.title") }}
                                    </span>
                                </td>
                                <td>{{ $r->description ?: '—' }}</td>
                                <td>{{ $r->category }}</td>
                                <td style="white-space:nowrap">{{ $r->date?->format('Y/m/d') ?: '—' }}</td>
                                <td text-start fw-bold>{{ number_format($r->amount, 2) }} {{ config('finance.currency_symbol') }}</td>
                                <td class="text-center">
                                    <a href="{{ $r->url }}" class="action-btn">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            @else
                <div class="text-center py-5">
                    <x-empty-state icon="bi bi-search" :title="__('messages.no_search_results')" />
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
