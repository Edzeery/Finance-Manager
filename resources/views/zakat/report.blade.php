<x-app-layout>
    <x-slot:title>{{ __('zakat.report') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('zakat.report') }}</x-slot>

    @php
        $r = $zakatRecord;
    @endphp

    @include('zakat._nav')

    {{-- Haul Info --}}
    <div class="mb-4 p-3" style="border-radius:8px; background:rgba(99,102,241,0.06); border:1px solid rgba(99,102,241,0.15)">
        <div class="d-flex flex-wrap gap-3" style="font-size:13px">
            <div>
                <span style="color:var(--text-muted)">{{ __('zakat.calendar_type') }}:</span>
                <span class="fw-bold">{{ __('zakat.' . ($r->calendar_type ?? 'hijri')) }}</span>
            </div>
            <div>
                <span style="color:var(--text-muted)">{{ __('zakat.hijri_year') }}:</span>
                <span class="fw-bold">{{ $r->hijri_year ?? '-' }}</span>
            </div>
            <div>
                <span style="color:var(--text-muted)">{{ __('zakat.calculation_date') }}:</span>
                <span class="fw-bold">{{ $r->calculation_date?->format('Y/m/d') ?? '-' }}</span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Gold & Silver Details --}}
            <div class="card-custom mb-4">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 d-flex align-items-center gap-2" style="font-size:inherit; font-weight:inherit">
                        <i class="bi bi-gem"></i>
                        <span>{{ __('zakat.gold_silver_holdings') }}</span>
                    </h5>
                    <span style="font-size:13px; color:var(--text-muted)">{{ $r->calculation_date->format('Y/m/d') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-custom">
                        <tbody>
                            @if($r->gold_weight)
                            <tr>
                                <td>{{ __('zakat.gold_weight') }}</td>
                                <td class="text-end">{{ number_format($r->gold_weight, 4) }}g × {{ number_format($r->gold_price_per_gram, 2) }}</td>
                                <td class="text-end fw-bold" style="color:#FFC107">{{ number_format($r->gold_value, 2) }}</td>
                            </tr>
                            @endif
                            @if($r->silver_weight)
                            <tr>
                                <td>{{ __('zakat.silver_weight') }}</td>
                                <td class="text-end">{{ number_format($r->silver_weight, 4) }}g × {{ number_format($r->silver_price_per_gram, 2) }}</td>
                                <td class="text-end fw-bold" style="color:#94A3B8">{{ number_format($r->silver_value, 2) }}</td>
                            </tr>
                            @endif
                            @if(!$r->gold_weight && !$r->silver_weight)
                            <tr>
                                <td>{{ __('zakat.gold_value') }}</td>
                                <td class="text-end" colspan="2">{{ number_format($r->gold_value, 2) }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('zakat.silver_value') }}</td>
                                <td class="text-end" colspan="2">{{ number_format($r->silver_value, 2) }}</td>
                            </tr>
                            @endif
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Cash & Bank --}}
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2" style="font-size:inherit; font-weight:inherit">
                        <i class="bi bi-cash-stack"></i>
                        <span>{{ __('zakat.cash_and_bank') }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-custom">
                        <tbody>
                            <tr><td>{{ __('zakat.cash_value') }}</td><td class="text-end">{{ number_format($r->cash_value, 2) }}</td></tr>
                            <tr><td>{{ __('zakat.bank_value') }}</td><td class="text-end">{{ number_format($r->bank_value, 2) }}</td></tr>
                            <tr><td>{{ __('zakat.ccp_value') }}</td><td class="text-end">{{ number_format($r->ccp_value, 2) }}</td></tr>
                            <tr class="fw-bold"><td>{{ __('zakat.cash_and_bank') }}</td><td class="text-end" style="color:#22C55E">{{ number_format($r->cash_value + $r->bank_value + $r->ccp_value, 2) }}</td></tr>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Business & Investments --}}
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2" style="font-size:inherit; font-weight:inherit">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>{{ __('zakat.business_and_investments') }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-custom">
                        <tbody>
                            <tr><td>{{ __('zakat.business_goods') }}</td><td class="text-end">{{ number_format($r->business_goods_value, 2) }}</td></tr>
                            <tr><td>{{ __('zakat.stocks_value') }}</td><td class="text-end">{{ number_format($r->stocks_value, 2) }}</td></tr>
                            <tr><td>{{ __('zakat.crypto_value') }}</td><td class="text-end">{{ number_format($r->crypto_value, 2) }}</td></tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="fw-bold">{{ __('zakat.total_wealth') }}</td>
                                <td class="text-end fw-bold">{{ number_format($r->total_wealth, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">{{ __('zakat.total_zakatable') }}</td>
                                <td class="text-end fw-bold" style="color:{{ $r->exceeds_nisab ? 'var(--success)' : 'var(--warning)' }}">{{ number_format($r->total_zakatable, 2) }}</td>
                            </tr>
                            @if($r->total_debts > 0)
                            <tr>
                                <td class="fw-bold">{{ __('zakat.total_debts') }}</td>
                                <td class="text-end fw-bold" style="color:var(--danger)">- {{ number_format($r->total_debts, 2) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="fw-bold">{{ __('zakat.net_zakatable') }}</td>
                                <td class="text-end fw-bold" style="color:var(--accent)">{{ number_format($r->net_zakatable ?? $r->total_zakatable, 2) }}</td>
                            </tr>
                        </tfoot>
                     </table>
                     </div>
                 </div>
            </div>

            {{-- Other Assets (non-zakatable) --}}
            @if($r->real_estate_value > 0 || $r->expected_receivables > 0)
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2" style="font-size:inherit; font-weight:inherit">
                        <i class="bi bi-box"></i>
                        <span>{{ __('zakat.other_assets') }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-custom">
                        <tbody>
                            @if($r->real_estate_value > 0)
                            <tr><td>{{ __('zakat.real_estate_value') }}</td><td class="text-end">{{ number_format($r->real_estate_value, 2) }}</td></tr>
                            @endif
                            @if($r->expected_receivables > 0)
                            <tr><td>{{ __('zakat.expected_receivables') }}</td><td class="text-end">{{ number_format($r->expected_receivables, 2) }}</td></tr>
                            @endif
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            {{-- Main Result --}}
            <div class="card-custom mb-4">
                <div class="card-body text-center">
                    <h6 class="fw-bold" style="color:var(--text-muted)">{{ __('zakat.zakat_amount') }}</h6>
                    <h2 class="fw-bold my-3" style="color:var(--accent)">
                        {{ number_format($r->zakat_amount, 2) }} {{ config('finance.currency_symbol') }}
                    </h2>
                    @if($r->exceeds_nisab)
                        <p style="color:var(--success); font-size:14px" class="mb-0">
                            <i class="bi bi-check-circle-fillms-1"></i>
                            {{ __('zakat.exceeds_nisab') }}: <x-status-badge domain="general" status="yes" set="bi" />
                        </p>
                    @else
                        <p style="color:var(--text-muted); font-size:14px" class="mb-0">
                            <i class="bi bi-info-circlems-1"></i>
                            {{ __('zakat.exceeds_nisab') }}: <x-status-badge domain="general" status="no" set="bi" />
                        </p>
                    @endif
                </div>
            </div>

            {{-- Nisab Section --}}
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-gem"></i>
                        <span>{{ __('zakat.nisab') }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-size:13px; color:var(--text-muted)">{{ __('zakat.nisab_gold') }} (85g)</span>
                        <span class="fw-bold">{{ number_format($r->nisab_gold, 2) }} {{ config('finance.currency_symbol') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-size:13px; color:var(--text-muted)">{{ __('zakat.nisab_silver') }} (595g)</span>
                        <span class="fw-bold">{{ number_format($r->nisab_silver, 2) }} {{ config('finance.currency_symbol') }}</span>
                    </div>
                </div>
            </div>

            @if($r->notes)
                <div class="card-custom mb-4">
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
                    <i class="bi bi-arrow-leftms-1"></i>{{ __('zakat.calculate') }}
                </a>
                <a href="{{ route('zakat.history') }}" class="btn btn-outline-secondary btn-custom" style="flex:1">
                    <i class="bi bi-clock-historyms-1"></i>{{ __('zakat.history') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
