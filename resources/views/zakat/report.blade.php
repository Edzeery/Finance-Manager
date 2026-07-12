<x-app-layout>
    <x-slot:title>{{ __('zakat.report') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('zakat.report') }}</x-slot>

    @php
        $r = $zakatRecord;
    @endphp

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 d-flex align-items-center gap-2" style="font-size:inherit; font-weight:inherit">
                        <i class="bi bi-file-text"></i>
                        <span>{{ __('zakat.report') }}</span>
                    </h5>
                    <span style="font-size:13px; color:var(--text-muted)">{{ $r->calculation_date->format('Y/m/d') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-custom">
                        <tbody>
                            <tr><td>{{ __('zakat.gold_value') }}</td><td class="text-end">{{ number_format($r->gold_value, 2) }}</td></tr>
                            <tr><td>{{ __('zakat.silver_value') }}</td><td class="text-end">{{ number_format($r->silver_value, 2) }}</td></tr>
                            <tr><td>{{ __('zakat.cash_value') }}</td><td class="text-end">{{ number_format($r->cash_value, 2) }}</td></tr>
                            <tr><td>{{ __('zakat.bank_value') }}</td><td class="text-end">{{ number_format($r->bank_value, 2) }}</td></tr>
                            <tr><td>{{ __('zakat.ccp_value') }}</td><td class="text-end">{{ number_format($r->ccp_value, 2) }}</td></tr>
                            <tr><td>{{ __('zakat.business_goods') }}</td><td class="text-end">{{ number_format($r->business_goods_value, 2) }}</td></tr>
                            <tr><td>{{ __('zakat.stocks_value') }}</td><td class="text-end">{{ number_format($r->stocks_value, 2) }}</td></tr>
                            <tr><td>{{ __('zakat.crypto_value') }}</td><td class="text-end">{{ number_format($r->crypto_value, 2) }}</td></tr>
                            <tr><td>{{ __('zakat.real_estate_value') }}</td><td class="text-end">{{ number_format($r->real_estate_value, 2) }}</td></tr>
                            <tr><td>{{ __('zakat.expected_receivables') }}</td><td class="text-end">{{ number_format($r->expected_receivables, 2) }}</td></tr>
                         </tbody>
                         <tfoot>
                             <tr><td class="fw-bold">{{ __('zakat.total_wealth') }}</td><td class="text-end fw-bold">{{ number_format($r->total_wealth, 2) }}</td></tr>
                             <tr><td class="fw-bold">{{ __('zakat.total_zakatable') }}</td><td class="text-end fw-bold" style="color:{{ $r->exceeds_nisab ? 'var(--success)' : 'var(--warning)' }}">{{ number_format($r->total_zakatable, 2) }}</td></tr>
                         </tfoot>
                     </table>
                     </div>
                 </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-gem"></i>
                        <span>{{ __('zakat.nisab') }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-size:13px; color:var(--text-muted)">{{ __('zakat.nisab_gold') }}</span>
                        <span class="fw-bold">{{ number_format($r->nisab_gold, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-size:13px; color:var(--text-muted)">{{ __('zakat.nisab_silver') }}</span>
                        <span class="fw-bold">{{ number_format($r->nisab_silver, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('zakat.exceeds_nisab') }}</span>
                        <span class="fw-bold" style="color:{{ $r->exceeds_nisab ? 'var(--success)' : 'var(--danger)' }}">
                            {{ $r->exceeds_nisab ? __('general.yes') : __('general.no') }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between" style="font-size:18px">
                        <span class="fw-bold">{{ __('zakat.zakat_amount') }}</span>
                        <span class="fw-bold" style="color:var(--accent)">{{ number_format($r->zakat_amount, 2) }} {{ config('finance.currency_symbol') }}</span>
                    </div>
                </div>
            </div>

            @if($r->notes)
                <div class="card-custom">
                    <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-sticky"></i>
                        <span>{{ __('zakat.notes') }}</span>
                    </h5>
                </div>
                    <div class="card-body">
                        <p style="font-size:14px; margin:0">{{ $r->notes }}</p>
                    </div>
                </div>
            @endif

            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('zakat.calculator') }}" class="btn btn-accent btn-custom">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('zakat.calculate') }}
                </a>
                <a href="{{ route('zakat.history') }}" class="btn btn-outline-secondary btn-custom" style="flex:1">
                    <i class="bi bi-clock-history me-1"></i>{{ __('zakat.history') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
