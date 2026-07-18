<x-app-layout>
    <x-slot:title>{{ __('zakat.title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('zakat.title') }}</x-slot>
    <x-slot:page-description>{{ __('zakat.calculate') }}</x-slot>

    @include('zakat._nav')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-calculator"></i>
                        <span>{{ __('zakat.calculate') }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('zakat.calculate') }}" method="POST" id="zakatForm">
                        @csrf

                        <div class="alert alert-info d-flex align-items-center gap-2 mb-4" style="border-radius:8px; font-size:13px; background:rgba(59,130,246,0.08); border:1px solid rgba(59,130,246,0.2); color:var(--text)">
                            <i class="bi bi-currency-exchange"></i>
                            <span>
                                {{ __('zakat.currency_used', ['currency' => config('finance.currency')]) }}
                                <a href="{{ route('account.settings') }}" style="text-decoration:underline; color:var(--accent)">{{ __('zakat.change_currency') }}</a>
                            </span>
                        </div>

                        {{-- Section 1: Silver Price + Fetch --}}
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="font-size:14px; color:var(--accent)">
                                <i class="bi bi-gem"></i>
                                {{ __('zakat.prices') }}
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label-custom">{{ __('zakat.silver_price') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="silver_price" id="silver_price" value="{{ old('silver_price', $input['silver_price'] ?? config('zakat.prices.silver_per_gram', 0)) }}" class="form-custom" required placeholder="0.00">
                                        <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}/g</span>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="d-flex align-items-center gap-2" style="margin-top:22px">
                                        <button type="button" onclick="fetchPrices()" id="fetchBtn" class="btn btn-outline-accent btn-custom" style="white-space:nowrap">
                                            <i class="bi bi-arrow-clockwise me-1" id="fetchIcon"></i>{{ __('zakat.fetch_prices') }}
                                        </button>
                                        <small id="fetchStatus" class="text-muted d-none"></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Gold Holdings (multi-karat) --}}
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="font-size:14px; color:#FFC107">
                                <i class="bi bi-gem"></i>
                                {{ __('zakat.gold_holdings') }}
                            </h6>

                            <div id="goldRows">
                                @foreach($goldItems as $idx => $item)
                                <div class="gold-row row g-2 mb-2 align-items-end" data-index="{{ $idx }}">
                                    <div class="col-md-3">
                                        @if($idx === 0)
                                        <label class="form-label-custom" style="font-size:12px">{{ __('zakat.gold_karat') }}</label>
                                        @endif
                                        <select name="gold_items[{{ $idx }}][karat]" class="form-custom gold-karat-select" onchange="onGoldKaratChange(this)">
                                            @foreach($karatPurity as $k => $p)
                                                <option value="{{ $k }}" {{ ($item['karat'] ?? 21) == $k ? 'selected' : '' }}>{{ $k }}K</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        @if($idx === 0)
                                        <label class="form-label-custom" style="font-size:12px">{{ __('zakat.gold_weight') }} (g)</label>
                                        @endif
                                        <input type="number" step="0.0001" min="0" name="gold_items[{{ $idx }}][weight]" value="{{ $item['weight'] ?? '' }}" class="form-custom gold-weight" placeholder="0.0000" oninput="calcGoldTotal()">
                                    </div>
                                    <div class="col-md-3">
                                        @if($idx === 0)
                                        <label class="form-label-custom" style="font-size:12px">{{ __('zakat.price_per_gram') }}</label>
                                        @endif
                                        <div class="input-group">
                                            <input type="number" step="0.01" min="0" name="gold_items[{{ $idx }}][price]" value="{{ $item['price'] ?? '' }}" class="form-custom gold-price" placeholder="0.00" oninput="calcGoldTotal()">
                                            <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:12px">{{ config('finance.currency_symbol') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        @if($idx === 0)
                                        <label class="form-label-custom" style="font-size:12px">{{ __('zakat.gold_value') }}</label>
                                        @endif
                                        <div class="form-custom gold-row-value" style="background:var(--bg-secondary); display:flex; align-items:center; font-weight:600; font-size:13px; min-height:38px; padding:0 10px">0</div>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end" style="padding-bottom:2px">
                                        @if($idx > 0)
                                        <button type="button" onclick="removeGoldRow(this)" class="btn btn-outline-danger btn-sm" style="padding:4px 8px; font-size:12px" title="{{ __('general.remove') }}">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <button type="button" onclick="addGoldRow()" class="btn btn-outline-secondary btn-sm mt-2" style="font-size:12px">
                                <i class="bi bi-plus-lg me-1"></i>{{ __('zakat.add_gold_row') }}
                            </button>

                            <div class="mt-3 p-3" style="border-radius:8px; background:rgba(255,193,7,0.06); border:1px solid rgba(255,193,7,0.15)">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span style="font-size:13px; font-weight:600; color:#FFC107">
                                        <i class="bi bi-gem me-1"></i>{{ __('zakat.gold_total') }}
                                    </span>
                                    <span id="goldTotalDisplay" style="font-size:15px; font-weight:700; color:#FFC107">0 {{ config('finance.currency_symbol') }}</span>
                                </div>
                                <small id="goldTotalWeight" style="color:var(--text-muted); font-size:11px"></small>
                            </div>
                            <input type="hidden" name="gold_total_weight" id="gold_total_weight">
                        </div>

                        {{-- Section 3: Silver --}}
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="font-size:14px; color:#94A3B8">
                                <i class="bi bi-gem"></i>
                                {{ __('zakat.silver_value') }}
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-custom">{{ __('zakat.silver_weight') }} (g)</label>
                                    <input type="number" step="0.0001" min="0" name="silver_weight" id="silver_weight" value="{{ old('silver_weight', $input['silver_weight'] ?? ($assets['silver']['weight'] ?? '')) }}" class="form-custom" placeholder="0.0000">
                                </div>
                            </div>
                        </div>

                        {{-- Section 4: Cash & Bank Accounts --}}
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="font-size:14px; color:#22C55E">
                                <i class="bi bi-cash-stack"></i>
                                {{ __('zakat.cash_and_bank') }}
                            </h6>
                            <div class="row g-3">
                                @php
                                    $cashFields = [
                                        'cash_value' => ['icon' => 'bi-cash', 'asset_type' => 'cash'],
                                        'bank_value' => ['icon' => 'bi-bank', 'asset_type' => 'bank_account'],
                                        'ccp_value' => ['icon' => 'bi-envelope', 'asset_type' => 'ccp'],
                                    ];
                                @endphp
                                @foreach($cashFields as $field => $config)
                                    @php
                                        $autoValue = $assets[$config['asset_type']] ?? 0;
                                        $inputVal = old($field, $input[$field] ?? ($autoValue > 0 ? $autoValue : ''));
                                    @endphp
                                    <div class="col-md-4">
                                        <label class="form-label-custom d-flex align-items-center gap-1">
                                            <i class="{{ $config['icon'] }}" style="font-size:14px"></i>
                                            {{ __("zakat.{$field}") }}
                                        </label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" min="0" name="{{ $field }}" value="{{ $inputVal }}" class="form-custom" placeholder="0.00">
                                            <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Section 5: Business Goods --}}
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="font-size:14px; color:#3B82F6">
                                <i class="bi bi-shop"></i>
                                {{ __('zakat.business_goods') }}
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label-custom">{{ __('zakat.business_goods_value') }}</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="business_goods_value" value="{{ old('business_goods_value', $input['business_goods_value'] ?? '') }}" class="form-custom" placeholder="0.00">
                                        <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 6: Investments --}}
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="font-size:14px; color:#8B5CF6">
                                <i class="bi bi-graph-up-arrow"></i>
                                {{ __('zakat.investments') }}
                            </h6>
                            <div class="row g-3">
                                @php
                                    $investFields = [
                                        'stocks_value' => ['icon' => 'bi-bar-chart', 'asset_type' => 'stocks'],
                                        'crypto_value' => ['icon' => 'bi-currency-bitcoin', 'asset_type' => 'crypto'],
                                    ];
                                @endphp
                                @foreach($investFields as $field => $config)
                                    @php
                                        $autoValue = $assets[$config['asset_type']] ?? 0;
                                        $inputVal = old($field, $input[$field] ?? ($autoValue > 0 ? $autoValue : ''));
                                    @endphp
                                    <div class="col-md-6">
                                        <label class="form-label-custom d-flex align-items-center gap-1">
                                            <i class="{{ $config['icon'] }}" style="font-size:14px"></i>
                                            {{ __("zakat.{$field}") }}
                                        </label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" min="0" name="{{ $field }}" value="{{ $inputVal }}" class="form-custom" placeholder="0.00">
                                            <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Section 7: Other Assets --}}
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="font-size:14px; color:#F59E0B">
                                <i class="bi bi-box"></i>
                                {{ __('zakat.other_assets') }}
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-custom">{{ __('zakat.real_estate_value') }}</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="real_estate_value" value="{{ old('real_estate_value', $input['real_estate_value'] ?? '') }}" class="form-custom" placeholder="0.00">
                                        <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom d-flex align-items-center gap-1">
                                        <span>{{ __('zakat.expected_receivables') }}</span>
                                        @if($owedDebtsTotal > 0)
                                            <span style="font-size:11px; padding:2px 6px; border-radius:4px; background:rgba(20,184,166,0.1); color:#14b8a6; font-weight:500">
                                                <i class="bi bi-link-45deg"></i> {{ __('zakat.auto_from_debts') }}
                                            </span>
                                        @endif
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="expected_receivables"
                                            value="{{ old('expected_receivables', $input['expected_receivables'] ?? ($owedDebtsTotal > 0 ? $owedDebtsTotal : '')) }}"
                                            class="form-custom" placeholder="0.00">
                                        <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                    </div>
                                    @if($owedDebts->count() > 0)
                                        <div class="mt-2" style="font-size:12px; color:var(--text-muted)">
                                            <i class="bi bi-info-circle"></i>
                                            {{ $owedDebts->count() }} {{ __('zakat.active_receivables') }}:
                                            @foreach($owedDebts->take(3) as $debt)
                                                <span style="color:var(--text)">{{ $debt['counterparty_name'] }} ({{ number_format($debt['remaining_amount'], 2) }})</span>{{ $loop->last ? '' : '، ' }}
                                            @endforeach
                                            @if($owedDebts->count() > 3)
                                                +{{ $owedDebts->count() - 3 }}
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="col-12 mt-3">
                            <label class="form-label-custom">{{ __('zakat.notes') }}</label>
                            <textarea name="notes" class="form-custom" rows="2" maxlength="1000">{{ old('notes', $input['notes'] ?? '') }}</textarea>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-accent btn-custom">
                                <i class="bi bi-calculator me-1"></i>{{ __('zakat.calculate') }}
                            </button>
                            <button type="submit" name="save" value="1" class="btn btn-outline-accent btn-custom">
                                <i class="bi bi-save me-1"></i>{{ __('zakat.save_record') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @if(isset($result))
                {{-- Main Result --}}
                <div class="card-custom mb-4">
                    <div class="card-body text-center">
                        <h6 class="fw-bold" style="color:var(--text-muted)">{{ __('zakat.zakat_amount') }}</h6>
                        <h2 class="fw-bold my-3" style="color:var(--accent)">
                            {{ number_format($result['totalZakat'], 2) }} {{ config('finance.currency_symbol') }}
                        </h2>
                        @if($result['exceedsNisab'])
                            <p style="color:var(--success); font-size:14px" class="mb-0">
                                <i class="bi bi-check-circle-fill me-1"></i>
                                {{ __('zakat.exceeds_nisab') }}: <x-status-badge domain="general" status="yes" set="bi" />
                            </p>
                        @else
                            <p style="color:var(--text-muted); font-size:14px" class="mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                {{ __('zakat.exceeds_nisab') }}: <x-status-badge domain="general" status="no" set="bi" />
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Nisab & Debts --}}
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
                            <span class="fw-bold">{{ number_format($result['nisabGold'], 2) }} {{ config('finance.currency_symbol') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="font-size:13px; color:var(--text-muted)">{{ __('zakat.nisab_silver') }} (595g)</span>
                            <span class="fw-bold">{{ number_format($result['nisabSilver'], 2) }} {{ config('finance.currency_symbol') }}</span>
                        </div>

                        @if($owingDebts->count() > 0 || $owedDebts->count() > 0)
                        <hr class="my-3">
                        <div style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px">
                            <i class="bi bi-diagram-3"></i> {{ __('zakat.debt_details') }}
                        </div>
                        @endif

                        @if($owingDebts->count() > 0)
                            <div style="font-size:12px; font-weight:600; color:var(--danger); margin-bottom:6px">
                                <i class="bi bi-arrow-up-right"></i> {{ __('zakat.your_debts') }} ({{ __('debt.owing') }})
                            </div>
                            @foreach($owingDebts as $debt)
                                <div class="d-flex justify-content-between mb-1" style="font-size:12px">
                                    <span style="color:var(--text-muted)">
                                        <i class="bi bi-person" style="font-size:10px"></i> {{ $debt['counterparty_name'] }}
                                    </span>
                                    <span style="color:var(--danger)">{{ number_format($debt['remaining_amount'], 2) }}</span>
                                </div>
                            @endforeach
                            <div class="d-flex justify-content-between mt-1 mb-3" style="font-size:12px; font-weight:600">
                                <span style="color:var(--danger)">{{ __('zakat.total_debts') }}</span>
                                <span style="color:var(--danger)">- {{ number_format($result['totalDebts'], 2) }} {{ config('finance.currency_symbol') }}</span>
                            </div>
                        @endif

                        @if($owedDebts->count() > 0)
                            <div style="font-size:12px; font-weight:600; color:#14b8a6; margin-bottom:6px">
                                <i class="bi bi-arrow-down-left"></i> {{ __('zakat.your_receivables') }} ({{ __('debt.owed') }})
                            </div>
                            @foreach($owedDebts as $debt)
                                <div class="d-flex justify-content-between mb-1" style="font-size:12px">
                                    <span style="color:var(--text-muted)">
                                        <i class="bi bi-person" style="font-size:10px"></i> {{ $debt['counterparty_name'] }}
                                    </span>
                                    <span style="color:#14b8a6">{{ number_format($debt['remaining_amount'], 2) }}</span>
                                </div>
                            @endforeach
                            <div class="d-flex justify-content-between mt-1 mb-3" style="font-size:12px; font-weight:600">
                                <span style="color:#14b8a6">{{ __('zakat.expected_receivables') }}</span>
                                <span style="color:#14b8a6">+ {{ number_format($owedDebtsTotal, 2) }} {{ config('finance.currency_symbol') }}</span>
                            </div>
                            <div style="font-size:11px; color:var(--text-muted); padding:6px 8px; background:rgba(20,184,166,0.06); border-radius:6px; border:1px solid rgba(20,184,166,0.15)">
                                <i class="bi bi-info-circle"></i> {{ __('zakat.receivables_not_zakatable') }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Summary --}}
                <div class="card-custom mb-4">
                    <div class="card-header-custom">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-wallet2"></i>
                            <span>{{ __('zakat.total_wealth') }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span style="font-size:13px; color:var(--text-muted)">{{ __('zakat.total_wealth') }}</span>
                            <span class="fw-bold">{{ number_format($result['totalWealth'], 2) }} {{ config('finance.currency_symbol') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="font-size:13px; color:var(--text-muted)">{{ __('zakat.total_zakatable') }}</span>
                            <span class="fw-bold">{{ number_format($result['totalZakatable'], 2) }} {{ config('finance.currency_symbol') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="font-size:13px; color:var(--text-muted)">{{ __('zakat.net_zakatable') }}</span>
                            <span class="fw-bold" style="color:{{ $result['exceedsNisab'] ? 'var(--success)' : 'var(--warning)' }}">
                                {{ number_format($result['netZakatable'], 2) }} {{ config('finance.currency_symbol') }}
                            </span>
                        </div>
                        @if($result['totalDebts'] > 0)
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="font-size:13px; color:var(--text-muted)">{{ __('zakat.total_zakat_gross') }}</span>
                            <span class="fw-bold">{{ number_format($result['totalZakatGross'], 2) }} {{ config('finance.currency_symbol') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="font-size:13px; color:var(--text-muted)">- {{ __('zakat.total_debts') }}</span>
                            <span class="fw-bold" style="color:var(--danger)">- {{ number_format($result['totalDebts'], 2) }} {{ config('finance.currency_symbol') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Zakat Breakdown --}}
                <details class="card-custom mb-4" open>
                    <summary class="card-header-custom" style="cursor:pointer">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-heart-fill"></i>
                            <span>{{ __('zakat.zakat_breakdown') }}</span>
                        </h5>
                    </summary>
                    <div class="card-body">
                        @php
                            $breakdown = [
                                ['label' => 'zakat.cash_and_bank', 'amount' => $result['cashZakat'], 'color' => '#22C55E'],
                                ['label' => 'zakat.gold_value', 'amount' => $result['goldZakat'], 'color' => '#FFC107'],
                                ['label' => 'zakat.silver_value', 'amount' => $result['silverZakat'], 'color' => '#94A3B8'],
                                ['label' => 'zakat.business_goods', 'amount' => $result['businessZakat'], 'color' => '#3B82F6'],
                                ['label' => 'zakat.investments', 'amount' => $result['investmentsZakat'], 'color' => '#8B5CF6'],
                            ];
                        @endphp
                        @foreach($breakdown as $item)
                            @if($item['amount'] > 0)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="d-flex align-items-center gap-2">
                                        <span style="width:8px; height:8px; border-radius:50%; background:{{ $item['color'] }}"></span>
                                        <span style="font-size:13px">{{ __($item['label']) }}</span>
                                    </span>
                                    <span class="fw-bold">{{ number_format($item['amount'], 2) }}</span>
                                </div>
                            @endif
                        @endforeach
                        @if(!empty($result['goldBreakdown']) && count($result['goldBreakdown']) > 1)
                            <hr style="margin:8px 0">
                            <div style="font-size:11px; color:var(--text-muted); margin-bottom:4px">{{ __('zakat.gold_breakdown_detail') }}:</div>
                            @foreach($result['goldBreakdown'] as $gb)
                                <div class="d-flex justify-content-between" style="font-size:12px; color:var(--text-muted)">
                                    <span>{{ $gb['karat'] }}K — {{ $gb['weight'] }}g × {{ number_format($gb['price'], 2) }}</span>
                                    <span>{{ number_format($gb['value'], 2) }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </details>
            @else
                <div class="card-custom">
                    <div class="card-header-custom">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history"></i>
                            <span>{{ __('zakat.history') }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @include('components.empty-state', [
                            'icon' => 'bi-heart',
                            'title' => __('zakat.no_records'),
                            'message' => __('zakat.calculate_first'),
                        ])
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        const karatPurity = @json($karatPurity);
        const currencySymbol = @json(config('finance.currency_symbol'));
        let goldRowIndex = {{ count($goldItems) }};

        function addGoldRow() {
            const container = document.getElementById('goldRows');
            const idx = goldRowIndex++;
            const defaultKarat = 21;
            const options = Object.keys(karatPurity).map(k =>
                '<option value="' + k + '"' + (k == defaultKarat ? ' selected' : '') + '>' + k + 'K</option>'
            ).join('');

            const html = '<div class="gold-row row g-2 mb-2 align-items-end" data-index="' + idx + '">' +
                '<div class="col-md-3"><select name="gold_items[' + idx + '][karat]" class="form-custom gold-karat-select" onchange="onGoldKaratChange(this)">' + options + '</select></div>' +
                '<div class="col-md-3"><input type="number" step="0.0001" min="0" name="gold_items[' + idx + '][weight]" class="form-custom gold-weight" placeholder="0.0000" oninput="calcGoldTotal()"></div>' +
                '<div class="col-md-3"><div class="input-group"><input type="number" step="0.01" min="0" name="gold_items[' + idx + '][price]" class="form-custom gold-price" placeholder="0.00" oninput="calcGoldTotal()"><span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:12px">' + currencySymbol + '</span></div></div>' +
                '<div class="col-md-2"><div class="form-custom gold-row-value" style="background:var(--bg-secondary); display:flex; align-items:center; font-weight:600; font-size:13px; min-height:38px; padding:0 10px">0</div></div>' +
                '<div class="col-md-1 d-flex align-items-end" style="padding-bottom:2px"><button type="button" onclick="removeGoldRow(this)" class="btn btn-outline-danger btn-sm" style="padding:4px 8px; font-size:12px"><i class="bi bi-x-lg"></i></button></div>' +
                '</div>';
            container.insertAdjacentHTML('beforeend', html);
        }

        function removeGoldRow(btn) {
            btn.closest('.gold-row').remove();
            calcGoldTotal();
        }

        function onGoldKaratChange(select) {
            calcGoldTotal();
        }

        function calcGoldTotal() {
            const rows = document.querySelectorAll('.gold-row');
            let totalValue = 0;
            let totalWeight = 0;

            rows.forEach(row => {
                const weight = parseFloat(row.querySelector('.gold-weight')?.value) || 0;
                const price = parseFloat(row.querySelector('.gold-price')?.value) || 0;
                const valueEl = row.querySelector('.gold-row-value');
                const value = weight * price;
                if (valueEl) valueEl.textContent = value > 0 ? parseFloat(value).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0';
                totalValue += value;
                totalWeight += weight;
            });

            document.getElementById('goldTotalDisplay').textContent = parseFloat(totalValue).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ' + currencySymbol;
            document.getElementById('goldTotalWeight').textContent = totalWeight > 0 ? totalWeight.toFixed(4) + 'g' : '';
            document.getElementById('gold_total_weight').value = totalWeight;
        }

        async function fetchPrices() {
            const btn = document.getElementById('fetchBtn');
            const icon = document.getElementById('fetchIcon');
            const status = document.getElementById('fetchStatus');
            const karatSelects = document.querySelectorAll('.gold-karat-select');
            const karats = [...new Set([...karatSelects].map(s => s.value))].join(',');

            btn.disabled = true;
            icon.classList.add('animate-spin');
            status.className = 'text-muted';
            status.textContent = '{{ __("zakat.auto_fetching") }}';
            status.classList.remove('d-none');

            try {
                const response = await fetch('{{ route("zakat.fetch-prices") }}?karats=' + karats);
                const data = await response.json();

                if (data.success) {
                    karatSelects.forEach(select => {
                        const karat = select.value;
                        const price = data.gold_prices?.[karat];
                        if (price !== null && price !== undefined) {
                            const row = select.closest('.gold-row');
                            const priceInput = row?.querySelector('.gold-price');
                            if (priceInput) priceInput.value = parseFloat(price).toFixed(2);
                        }
                    });
                    calcGoldTotal();

                    if (data.silver !== null) {
                        document.getElementById('silver_price').value = parseFloat(data.silver).toFixed(2);
                    }

                    status.className = 'text-success';
                    status.textContent = data.symbol + ' ' + data.currency + ' — ' + Object.keys(data.gold_prices || {}).length + ' karat(s)';
                } else {
                    status.className = 'text-danger';
                    status.textContent = data.message || '{{ __("zakat.fetch_prices_error") }}';
                }
            } catch (e) {
                status.className = 'text-danger';
                status.textContent = '{{ __("zakat.fetch_prices_error") }}';
            } finally {
                btn.disabled = false;
                icon.classList.remove('animate-spin');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            calcGoldTotal();
            fetchPrices();
        });
    </script>
    @endpush
</x-app-layout>
