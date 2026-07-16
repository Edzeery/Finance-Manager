<x-app-layout>
    <x-slot:title>{{ __('asset.add') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('asset.add') }}</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-body p-4">
                    <form action="{{ route('asset.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('asset.type') }} <span
                                        class="text-danger">*</span></label>

                                <select name="type" id="asset"
                                    class="form-custom @error('type') is-invalid @enderror" required
                                    @change="toggleAssetFields()" aria-placeholder="{{ __('general.select') }}">
                                    <option value="">{{ __('general.select') }}</option>
                                    @foreach ($types as $t)
                                        <option value="{{ $t->value }}"
                                            {{ old('type') === $t->value ? 'selected' : '' }}>{{ $t->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('asset.name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="form-custom @error('name') is-invalid @enderror" required maxlength="255">
                                @error('name')
                                    <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="field-quantity">
                                <label class="form-label-custom">{{ __('asset.quantity') }}</label>
                                <input type="number" step="0.0001" min="0" name="quantity"
                                    value="{{ old('quantity') }}"
                                    class="form-custom @error('quantity') is-invalid @enderror" placeholder="0">
                                @error('quantity')
                                    <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="field-unit_price">
                                <label class="form-label-custom">{{ __('asset.unit_price') }}</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="unit_price"
                                        value="{{ old('unit_price') }}"
                                        class="form-custom @error('unit_price') is-invalid @enderror"
                                        placeholder="0.00">
                                    <span class="input-group-text"
                                        style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                </div>
                                @error('unit_price')
                                    <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="field-total_value">
                                <label class="form-label-custom">{{ __('asset.total_value') }}</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="total_value"
                                        id="total_value" value="{{ old('total_value') }}"
                                        class="form-custom @error('total_value') is-invalid @enderror"
                                        placeholder="0.00">
                                    <span class="input-group-text"
                                        style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px">{{ config('finance.currency_symbol') }}</span>
                                </div>
                                @error('total_value')
                                    <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="field-bank_name" style="display:none">
                                <label class="form-label-custom">{{ __('asset.bank_name') }}</label>
                                <input type="text" name="bank_name" value="{{ old('bank_name') }}"
                                    class="form-custom" maxlength="255">
                            </div>

                            <div class="col-md-6" id="field-account_number" style="display:none">
                                <label class="form-label-custom">{{ __('asset.account_number') }}</label>
                                <input type="text" name="account_number" value="{{ old('account_number') }}"
                                    class="form-custom" maxlength="255">
                            </div>

                            <div class="col-6">
                                <div class="mb-1">
                                    <x-toggle-switch name="is_liquid" :checked="old('is_liquid', '1')"
                                        label="{{ __('asset.is_liquid') }}" hint="{{ __('asset.liquid_help') }}" />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-1">
                                    <x-toggle-switch name="is_zakatable" :checked="old('is_zakatable', '1')"
                                        label="{{ __('asset.is_zakatable') }}"
                                        hint="{{ __('asset.zakatable_help') }}" />
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">{{ __('asset.description') }}</label>
                                <textarea name="description" class="form-custom" rows="3" maxlength="1000">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">{{ __('asset.notes') }}</label>
                                <textarea name="notes" class="form-custom" rows="3" maxlength="1000">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4 pt-3" style="border-top:1px solid var(--border)">
                            <button type="submit" class="btn btn-accent btn-custom">
                                <i class="bi bi-check-lg me-1"></i>{{ __('general.save') }}
                            </button>
                            <a href="{{ route('asset.index') }}" class="btn btn-outline-secondary btn-custom">
                                <i class="bi bi-x-lg me-1"></i>{{ __('general.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleAssetFields() {
                var type = document.getElementById('asset')?.value;
                var bankFields = ['bank_name', 'account_number'];
                var qtyFields = ['quantity', 'unit_price'];
                var isBank = type === 'bank_account' || type === 'ccp';

                bankFields.forEach(function(f) {
                    var el = document.getElementById('field-' + f);
                    if (el) el.style.display = isBank ? 'block' : 'none';
                });

                qtyFields.forEach(function(f) {
                    var el = document.getElementById('field-' + f);
                    if (el) el.style.display = type === 'gold' || type === 'silver' ? 'block' : 'none';
                });
            }
            toggleAssetFields();
        </script>
    @endpush
</x-app-layout>
