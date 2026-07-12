<x-app-layout>
    <x-slot:title>{{ __('zakat.title') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('zakat.title') }}</x-slot>
    <x-slot:page-description>{{ __('zakat.calculate') }}</x-slot>


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

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('zakat.gold_price') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="gold_price" id="gold_price" value="{{ old('gold_price', $input['gold_price'] ?? config('zakat.prices.gold_per_gram', 0)) }}" class="form-custom" required placeholder="0.00">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}/g</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('zakat.silver_price') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="silver_price" id="silver_price" value="{{ old('silver_price', $input['silver_price'] ?? config('zakat.prices.silver_per_gram', 0)) }}" class="form-custom" required placeholder="0.00">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}/g</span>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3" style="font-size:14px">{{ __('zakat.assets') }}</h6>
                        <div class="row g-3">
                            @php
                                $assetFields = [
                                    'gold_value' => 'gold',
                                    'silver_value' => 'silver',
                                    'cash_value' => 'cash',
                                    'bank_value' => 'bank_account',
                                    'ccp_value' => 'ccp',
                                    'business_goods_value' => 'business_goods',
                                    'stocks_value' => 'stocks',
                                    'crypto_value' => 'crypto',
                                    'real_estate_value' => 'real_estate_value',
                                    'expected_receivables' => 'expected_receivables',
                                ];
                            @endphp

                            @foreach($assetFields as $field => $assetType)
                                @php
                                    $autoValue = $assets[$assetType] ?? 0;
                                    $inputVal = old($field, $result[$field] ?? $input[$field] ?? $autoValue);
                                @endphp
                                <div class="col-md-6">
                                    <label class="form-label-custom">{{ __("zakat.{$field}") }}</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="{{ $field }}" value="{{ $inputVal }}" class="form-custom" placeholder="0.00">
                                        <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="col-12 mt-3">
                            <label class="form-label-custom">{{ __('zakat.notes') }}</label>
                            <textarea name="notes" class="form-custom" rows="2" maxlength="1000">{{ old('notes', $input['notes'] ?? '') }}</textarea>
                        </div>

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
                <div class="card-custom mb-4">
                    <div class="card-header-custom">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-file-text"></i>
                            <span>{{ __('zakat.report') }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="result-row">
                            <span>{{ __('zakat.nisab_gold') }}</span>
                            <span class="fw-bold">{{ number_format($result['nisab_gold'], 2) }}</span>
                        </div>
                        <div class="result-row">
                            <span>{{ __('zakat.nisab_silver') }}</span>
                            <span class="fw-bold">{{ number_format($result['nisab_silver'], 2) }}</span>
                        </div>
                        <hr>
                        <div class="result-row">
                            <span>{{ __('zakat.total_wealth') }}</span>
                            <span class="fw-bold">{{ number_format($result['total_wealth'], 2) }}</span>
                        </div>
                        <div class="result-row">
                            <span>{{ __('zakat.total_zakatable') }}</span>
                            <span class="fw-bold" style="color:{{ $result['exceeds_nisab'] ? 'var(--success)' : 'var(--warning)' }}">
                                {{ number_format($result['total_zakatable'], 2) }}
                            </span>
                        </div>
                        <div class="result-row">
                            <span>{{ __('zakat.exceeds_nisab') }}</span>
                            <span class="fw-bold" style="color:{{ $result['exceeds_nisab'] ? 'var(--success)' : 'var(--danger)' }}">
                                {{ $result['exceeds_nisab'] ? __('general.yes') : __('general.no') }}
                            </span>
                        </div>
                        <hr>
                        <div class="result-row" style="font-size:18px">
                            <span>{{ __('zakat.zakat_amount') }}</span>
                            <span class="fw-bold" style="color:var(--accent)">{{ number_format($result['zakat_amount'], 2) }} {{ config('finance.currency_symbol') }}</span>
                        </div>
                    </div>
                </div>
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

</x-app-layout>
