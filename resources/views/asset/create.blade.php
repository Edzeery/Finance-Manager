<x-app-layout>
    <x-slot:title>{{ __('asset.add') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('asset.add') }}</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-body p-4">

                    {{-- Important Notes --}}
                    <div style="padding:14px 16px; border-radius:var(--radius); background:rgba(59,130,246,0.06); border:1px solid rgba(59,130,246,0.15); margin-bottom:24px">
                        <div style="font-size:13px; font-weight:600; color:var(--accent); margin-bottom:8px; display:flex; align-items:center; gap:6px">
                            <i class="bi bi-info-circle"></i>
                            {{ __('asset.important_notes') }}
                        </div>
                        <ul style="margin:0; padding:0 0 0 16px; font-size:12px; color:var(--text-muted); line-height:1.8">
                            <li>{{ __('asset.important_notes_1') }}</li>
                            <li>{{ __('asset.important_notes_2') }}</li>
                            <li>{{ __('asset.important_notes_3') }}</li>
                        </ul>
                    </div>

                    <form action="{{ route('asset.store') }}" method="POST" id="assetForm">
                        @csrf

                        <div class="row g-3">

                            {{-- ═══ Asset Type ═══ --}}
                            <div class="col-md-6">
                                <label class="form-label-custom d-flex align-items-center gap-1">
                                    <span>{{ __('asset.type') }} <span class="text-danger">*</span></span>
                                    <span style="cursor:help" title="{{ __('asset.type_help') }}">
                                        <i class="bi bi-question-circle" style="font-size:12px; color:var(--text-muted)"></i>
                                    </span>
                                </label>
                                <select name="type" id="assetType"
                                    class="form-custom @error('type') is-invalid @enderror" required
                                    onchange="toggleAssetFields()">
                                    <option value="">{{ __('general.select') }}</option>
                                    @foreach ($types as $t)
                                        <option value="{{ $t->value }}"
                                            {{ old('type') === $t->value ? 'selected' : '' }}>
                                            {{ $t->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="typeBadge" class="mt-1" style="min-height:22px"></div>
                                @error('type')
                                    <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ═══ Name ═══ --}}
                            <div class="col-md-6">
                                <label class="form-label-custom d-flex align-items-center gap-1">
                                    <span>{{ __('asset.name') }} <span class="text-danger">*</span></span>
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="form-custom @error('name') is-invalid @enderror" required maxlength="255">
                                @error('name')
                                    <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ═══ Bank Fields (conditional) ═══ --}}
                            <div class="col-md-6" id="field-bank_name" style="display:none">
                                <label class="form-label-custom d-flex align-items-center gap-1">
                                    <span>{{ __('asset.bank_name') }}</span>
                                    <span style="cursor:help" title="{{ __('asset.bank_name_help') }}">
                                        <i class="bi bi-question-circle" style="font-size:12px; color:var(--text-muted)"></i>
                                    </span>
                                </label>
                                <input type="text" name="bank_name" value="{{ old('bank_name') }}"
                                    class="form-custom" maxlength="255">
                            </div>

                            <div class="col-md-6" id="field-account_number" style="display:none">
                                <label class="form-label-custom d-flex align-items-center gap-1">
                                    <span>{{ __('asset.account_number') }}</span>
                                    <span style="cursor:help" title="{{ __('asset.account_number_help') }}">
                                        <i class="bi bi-question-circle" style="font-size:12px; color:var(--text-muted)"></i>
                                    </span>
                                </label>
                                <div class="input-group">
                                    <input type="text" name="account_number" value="{{ old('account_number') }}"
                                        class="form-custom" maxlength="255">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:12px">
                                        <i class="bi bi-shield-lock"></i> {{ __('asset.security_notice') }}
                                    </span>
                                </div>
                            </div>

                            {{-- ═══ Gold/Silver Fields (conditional) ═══ --}}
                            <div class="col-md-6" id="field-karat" style="display:none">
                                <label class="form-label-custom d-flex align-items-center gap-1">
                                    <span>{{ __('asset.karat') }}</span>
                                    <span style="cursor:help" title="{{ __('asset.karat_help') }}">
                                        <i class="bi bi-question-circle" style="font-size:12px; color:var(--text-muted)"></i>
                                    </span>
                                </label>
                                <select name="karat" id="karat" class="form-custom" onchange="updateKaratBadge()">
                                    <option value="">--</option>
                                    @foreach($karatPurity as $k => $purity)
                                        <option value="{{ $k }}"
                                            {{ old('karat', 21) == $k ? 'selected' : '' }}>
                                            {{ $k }} {{ __('zakat.karat_' . $k) }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="karatBadge" class="mt-1" style="min-height:22px"></div>
                            </div>

                            <div class="col-md-6" id="field-weight_grams" style="display:none">
                                <label class="form-label-custom d-flex align-items-center gap-1">
                                    <span>{{ __('asset.weight_grams') }} (g)</span>
                                    <span style="cursor:help" title="{{ __('asset.weight_help') }}">
                                        <i class="bi bi-question-circle" style="font-size:12px; color:var(--text-muted)"></i>
                                    </span>
                                </label>
                                <input type="number" step="0.0001" min="0" name="weight_grams" id="weight_grams"
                                    value="{{ old('weight_grams') }}"
                                    class="form-custom @error('weight_grams') is-invalid @enderror" placeholder="0.0000"
                                    oninput="calculateTotal()">
                                @error('weight_grams')
                                    <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="field-quantity" style="display:none">
                                <label class="form-label-custom d-flex align-items-center gap-1">
                                    <span>{{ __('asset.quantity') }}</span>
                                    <span style="cursor:help" title="{{ __('asset.quantity_help') }}">
                                        <i class="bi bi-question-circle" style="font-size:12px; color:var(--text-muted)"></i>
                                    </span>
                                </label>
                                <input type="number" step="0.0001" min="0" name="quantity" id="quantity"
                                    value="{{ old('quantity') }}"
                                    class="form-custom @error('quantity') is-invalid @enderror" placeholder="0"
                                    oninput="calculateTotal()">
                                @error('quantity')
                                    <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="field-unit_price" style="display:none">
                                <label class="form-label-custom d-flex align-items-center gap-1">
                                    <span>{{ __('asset.unit_price') }}</span>
                                    <span style="cursor:help" title="{{ __('asset.unit_price_help') }}">
                                        <i class="bi bi-question-circle" style="font-size:12px; color:var(--text-muted)"></i>
                                    </span>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="unit_price" id="unit_price"
                                        value="{{ old('unit_price') }}"
                                        class="form-custom @error('unit_price') is-invalid @enderror"
                                        placeholder="0.00" oninput="calculateTotal()">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                </div>
                                @error('unit_price')
                                    <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ═══ Total Value (auto-calculated) ═══ --}}
                            <div class="col-md-6" id="field-total_value">
                                <label class="form-label-custom d-flex align-items-center gap-1">
                                    <span>{{ __('asset.total_value') }}</span>
                                    <span style="cursor:help" title="{{ __('asset.total_value_help') }}">
                                        <i class="bi bi-question-circle" style="font-size:12px; color:var(--text-muted)"></i>
                                    </span>
                                    <span id="autoCalcBadge" style="display:none; font-size:11px; padding:1px 6px; border-radius:4px; background:var(--success-light); color:var(--success); font-weight:500">
                                        <i class="bi bi-calculator"></i> {{ __('asset.auto_calculated') }}
                                    </span>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="total_value"
                                        id="total_value" value="{{ old('total_value') }}"
                                        class="form-custom @error('total_value') is-invalid @enderror"
                                        placeholder="0.00">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                </div>
                                <div id="totalPreview" class="mt-1" style="font-size:12px; color:var(--text-muted); display:none"></div>
                                @error('total_value')
                                    <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ═══ Settings ═══ --}}
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <x-toggle-switch name="is_liquid" :checked="old('is_liquid', '1')"
                                                label="{{ __('asset.is_liquid') }}" hint="{{ __('asset.liquid_help') }}" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <x-toggle-switch name="is_zakatable" :checked="old('is_zakatable', '1')"
                                                label="{{ __('asset.is_zakatable') }}"
                                                hint="{{ __('asset.zakatable_help') }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ═══ Description ═══ --}}
                            <div class="col-12">
                                <label class="form-label-custom">{{ __('asset.description') }}</label>
                                <textarea name="description" class="form-custom" rows="2" maxlength="1000">{{ old('description') }}</textarea>
                            </div>

                            {{-- ═══ Notes ═══ --}}
                            <div class="col-12">
                                <label class="form-label-custom">{{ __('asset.notes') }}</label>
                                <textarea name="notes" class="form-custom" rows="2" maxlength="1000">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        {{-- ═══ Actions ═══ --}}
                        <div class="d-flex gap-3 mt-4 pt-3" style="border-top:1px solid var(--border)">
                            <x-button submit icon="bi bi-check-lg">{{ __('general.save') }}</x-button>
                            <x-button href="{{ route('asset.index') }}" icon="bi bi-x-lg" variant="outline">{{ __('general.cancel') }}</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function() {
        const karatPurity = @json($karatPurity);
        const currencySymbol = @json(config('finance.currency_symbol'));
        const assetTypes = @json($assetTypeMap);

        window.toggleAssetFields = toggleAssetFields;
        window.updateKaratBadge = updateKaratBadge;
        window.calculateTotal = calculateTotal;

        function toggleAssetFields() {
            var type = document.getElementById('assetType')?.value;
            var info = assetTypes[type] || null;

            document.getElementById('field-bank_name').style.display = (type === 'bank_account' || type === 'ccp') ? 'block' : 'none';
            document.getElementById('field-account_number').style.display = (type === 'bank_account' || type === 'ccp') ? 'block' : 'none';
            document.getElementById('field-karat').style.display = type === 'gold' ? 'block' : 'none';
            document.getElementById('field-weight_grams').style.display = (type === 'gold' || type === 'silver') ? 'block' : 'none';
            document.getElementById('field-quantity').style.display = (type === 'gold' || type === 'silver') ? 'block' : 'none';
            document.getElementById('field-unit_price').style.display = (type === 'gold' || type === 'silver') ? 'block' : 'none';

            var badge = document.getElementById('typeBadge');
            if (info && badge) {
                badge.innerHTML = '<span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;padding:3px 8px;border-radius:6px;background:' + info.color + '15;color:' + info.color + ';font-weight:500"><i class="bi bi-' + info.icon + '"></i>' +
                    info.label + (info.zakatable ? ' · <i class="bi bi-check-circle-fill" style="font-size:10px"></i>' : '') + '</span>';
            } else if (badge) {
                badge.innerHTML = '';
            }

            if (type !== 'gold' && type !== 'silver') {
                document.getElementById('autoCalcBadge').style.display = 'none';
            }

            updateKaratBadge();
            calculateTotal();
        }

        function updateKaratBadge() {
            var karat = document.getElementById('karat')?.value;
            var badge = document.getElementById('karatBadge');
            if (!karat || !karatPurity[karat] || !badge) {
                if (badge) badge.innerHTML = '';
                return;
            }
            var pct = (karatPurity[karat] * 100).toFixed(1);
            badge.innerHTML = '<span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;padding:3px 8px;border-radius:6px;background:var(--warning-light);color:var(--warning);font-weight:500"><i class="bi bi-gem"></i>' + karat + 'K — ' + pct + '% {{ __("asset.purity") }}</span>';
        }

        function calculateTotal() {
            var type = document.getElementById('assetType')?.value;
            var totalInput = document.getElementById('total_value');
            var autoBadge = document.getElementById('autoCalcBadge');
            var preview = document.getElementById('totalPreview');

            if (type !== 'gold' && type !== 'silver') {
                autoBadge.style.display = 'none';
                preview.style.display = 'none';
                return;
            }

            var quantity = parseFloat(document.getElementById('quantity')?.value) || 0;
            var unitPrice = parseFloat(document.getElementById('unit_price')?.value) || 0;
            var weight = parseFloat(document.getElementById('weight_grams')?.value) || 0;

            var calculated = 0;
            var formula = '';

            if (quantity > 0 && unitPrice > 0) {
                calculated = quantity * unitPrice;
                formula = quantity + ' × ' + unitPrice;
            } else if (weight > 0 && unitPrice > 0) {
                calculated = weight * unitPrice;
                formula = weight + 'g × ' + unitPrice;
            }

            if (calculated > 0) {
                totalInput.value = calculated.toFixed(2);
                autoBadge.style.display = 'inline';
                preview.style.display = 'block';
                preview.innerHTML = '<i class="bi bi-check-circle" style="color:var(--success)"></i> ' + formula + ' = <strong>' + currencySymbol + ' ' + calculated.toFixed(2) + '</strong>';
            } else {
                autoBadge.style.display = 'none';
                preview.style.display = 'none';
            }
        }

        toggleAssetFields();
        })();
    </script>
    @endpush
</x-app-layout>
