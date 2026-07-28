<x-app-layout>
    <x-slot:title>{{ __('asset.edit') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('asset.edit') }}</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-body p-4">
                    <form action="{{ route('asset.update', $asset) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('asset.type') }} <span class="text-danger">*</span></label>
                                <select name="type" id="asset_type" class="form-custom @error('type') is-invalid @enderror" required @change="toggleAssetFields()">
                                    @foreach($types as $t)
                                        <option value="{{ $t->value }}" {{ old('type', $asset->type?->value) === $t->value ? 'selected' : '' }}>{{ $t->label() }}</option>
                                    @endforeach
                                </select>
                                @error('type') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('asset.name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $asset->name) }}" class="form-custom @error('name') is-invalid @enderror" required maxlength="255">
                                @error('name') <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6" id="field-quantity">
                                <label class="form-label-custom">{{ __('asset.quantity') }}</label>
                                <input type="number" step="0.0001" min="0" name="quantity" value="{{ old('quantity', $asset->quantity) }}" class="form-custom" placeholder="0">
                            </div>

                            <div class="col-md-6" id="field-unit_price">
                                <label class="form-label-custom">{{ __('asset.unit_price') }}</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="unit_price" value="{{ old('unit_price', $asset->unit_price) }}" class="form-custom" placeholder="0.00">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                </div>
                            </div>

                            <div class="col-md-6" id="field-total_value">
                                <label class="form-label-custom">{{ __('asset.total_value') }}</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="total_value" value="{{ old('total_value', $asset->total_value) }}" class="form-custom" placeholder="0.00">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                </div>
                            </div>

                            <div class="col-md-6" id="field-bank_name" style="display:none">
                                <label class="form-label-custom">{{ __('asset.bank_name') }}</label>
                                <input type="text" name="bank_name" value="{{ old('bank_name', $asset->bank_name) }}" class="form-custom" maxlength="255">
                            </div>

                            <div class="col-md-6" id="field-account_number" style="display:none">
                                <label class="form-label-custom">{{ __('asset.account_number') }}</label>
                                <input type="text" name="account_number" value="{{ old('account_number', $asset->account_number) }}" class="form-custom" maxlength="255">
                            </div>

                            <div class="col-md-6" id="field-karat" style="display:none">
                                <label class="form-label-custom">{{ __('asset.karat') }}</label>
                                <select name="karat" id="karat" class="form-custom">
                                    <option value="">--</option>
                                    <option value="24" {{ old('karat', $asset->karat) == 24 ? 'selected' : '' }}>24 {{ __('zakat.karat_24') }}</option>
                                    <option value="22" {{ old('karat', $asset->karat) == 22 ? 'selected' : '' }}>22 {{ __('zakat.karat_22') }}</option>
                                    <option value="21" {{ old('karat', $asset->karat) == 21 ? 'selected' : '' }}>21 {{ __('zakat.karat_21') }}</option>
                                    <option value="18" {{ old('karat', $asset->karat) == 18 ? 'selected' : '' }}>18 {{ __('zakat.karat_18') }}</option>
                                    <option value="14" {{ old('karat', $asset->karat) == 14 ? 'selected' : '' }}>14 {{ __('zakat.karat_14') }}</option>
                                    <option value="10" {{ old('karat', $asset->karat) == 10 ? 'selected' : '' }}>10 {{ __('zakat.karat_10') }}</option>
                                </select>
                            </div>

                            <div class="col-md-6" id="field-weight_grams" style="display:none">
                                <label class="form-label-custom">{{ __('asset.weight_grams') }}</label>
                                <input type="number" step="0.0001" min="0" name="weight_grams" id="weight_grams"
                                    value="{{ old('weight_grams', $asset->weight_grams) }}"
                                    class="form-custom" placeholder="0.0000">
                            </div>

                            <div class="col-6">
                                <div class="mb-1">
                                    <x-toggle-switch name="is_liquid" :checked="old('is_liquid', $asset->is_liquid)" label="{{ __('asset.is_liquid') }}" hint="{{ __('asset.liquid_help') }}" />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-1">
                                    <x-toggle-switch name="is_zakatable" :checked="old('is_zakatable', $asset->is_zakatable ?? true)" label="{{ __('asset.is_zakatable') }}" hint="{{ __('asset.zakatable_help') }}" />
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">{{ __('asset.description') }}</label>
                                <textarea name="description" class="form-custom" rows="3" maxlength="1000">{{ old('description', $asset->description) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">{{ __('asset.notes') }}</label>
                                <textarea name="notes" class="form-custom" rows="3" maxlength="1000">{{ old('notes', $asset->notes) }}</textarea>
                            </div>
                        </div>

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
    function toggleAssetFields() {
        var type = document.getElementById('asset_type')?.value;
        var bankFields = ['bank_name', 'account_number'];
        var qtyFields = ['quantity', 'unit_price'];
        var isBank = type === 'bank_account' || type === 'ccp';
        var isGold = type === 'gold';
        var isMetal = type === 'gold' || type === 'silver';

        bankFields.forEach(function(f) {
            var el = document.getElementById('field-' + f);
            if (el) el.style.display = isBank ? 'block' : 'none';
        });
        qtyFields.forEach(function(f) {
            var el = document.getElementById('field-' + f);
            if (el) el.style.display = isMetal ? 'block' : 'none';
        });

        var karatEl = document.getElementById('field-karat');
        if (karatEl) karatEl.style.display = isGold ? 'block' : 'none';

        var weightEl = document.getElementById('field-weight_grams');
        if (weightEl) weightEl.style.display = isMetal ? 'block' : 'none';
    }
    toggleAssetFields();
    </script>
    @endpush
</x-app-layout>
