<x-super-admin-layout>
    <x-slot:title>{{ $taxRate ? __('super-admin.edit_tax_rate') : __('super-admin.create_tax_rate') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ $taxRate ? __('super-admin.edit_tax_rate') : __('super-admin.create_tax_rate') }}</x-slot>
    <x-slot:page-description>{{ $taxRate ? __('super-admin.edit_tax_rate_desc') : __('super-admin.create_tax_rate_desc') }}</x-slot>

    <form method="POST" action="{{ $taxRate ? route('super.admin.tax-rates.update', $taxRate) : route('super.admin.tax-rates.store') }}" style="max-width:600px">
        @csrf @if($taxRate) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label-custom">{{ __('super-admin.tax_rate_name') }} <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-custom @error('name') is-invalid @enderror"
                   value="{{ old('name', $taxRate->name ?? '') }}" maxlength="255" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label-custom">{{ __('super-admin.tax_rate_slug') }}</label>
                <input type="text" name="slug" class="form-custom @error('slug') is-invalid @enderror"
                       value="{{ old('slug', $taxRate->slug ?? '') }}" maxlength="100">
                @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label-custom">{{ __('super-admin.tax_rate_value') }} <span class="text-danger">*</span></label>
                <input type="number" name="rate" class="form-custom @error('rate') is-invalid @enderror"
                       value="{{ old('rate', $taxRate->rate ?? '') }}" step="0.01" min="0" max="100" required>
                @error('rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label-custom">{{ __('super-admin.tax_rate_type') }} <span class="text-danger">*</span></label>
                <select name="type" class="form-custom @error('type') is-invalid @enderror" required>
                    <option value="percentage" {{ old('type', $taxRate->type ?? 'percentage') === 'percentage' ? 'selected' : '' }}>{{ __('general.percentage') }} (%)</option>
                    <option value="fixed" {{ old('type', $taxRate->type ?? 'percentage') === 'fixed' ? 'selected' : '' }}>{{ __('general.fixed') }} ($)</option>
                </select>
                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label-custom">{{ __('super-admin.tax_rate_country') }}</label>
                <input type="text" name="country" class="form-custom @error('country') is-invalid @enderror"
                       value="{{ old('country', $taxRate->country ?? '') }}" maxlength="2" placeholder="DZ, US, FR...">
                @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label-custom">{{ __('super-admin.tax_rate_region') }}</label>
            <input type="text" name="region" class="form-custom @error('region') is-invalid @enderror"
                   value="{{ old('region', $taxRate->region ?? '') }}" maxlength="255">
            @error('region') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex align-items-center gap-2 mb-4">
            <x-toggle-switch name="is_active" :checked="$taxRate->is_active ?? true" description="{{ __('general.active') }}" />
        </div>

        {{-- ربط طرق الدفع --}}
        <div class="section-card mt-4">
            <div class="section-card-header">
                <h5><i class="bi bi-credit-card"></i> {{ __('super-admin.payment_method_restrictions') ?? 'ربط طرق الدفع' }}</h5>
            </div>
            <div class="section-card-body">
                <div class="settings-section-desc small mb-3">
                    {{ __('super-admin.payment_method_restrictions_hint_tax') ?? 'اختر طرق الدفع التي تطبق عليها هذه الضريبة/الرسوم وحدد نوع الرسوم.' }}
                </div>
                @if($paymentMethods->isNotEmpty())
                    @php
                        $_defaultPms = $linkedPaymentMethods ?? collect();
                        $_chargeTypes = ['gateway_fee', 'tax_added', 'tax_disclosed'];
                    @endphp
                    <div class="d-flex flex-wrap gap-3">
                        @foreach($paymentMethods as $pm)
                            @php
                                $pmChargeTypes = ($_defaultPms->get($pm->id) ?? collect())->toArray();
                            @endphp
                            <div style="min-width:170px;padding:12px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)">
                                <div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:6px">{{ $pm->name }}</div>
                                @foreach($_chargeTypes as $_ct)
                                    @php $_ctChecked = in_array($_ct, $pmChargeTypes, true); @endphp
                                    <label class="d-flex align-items-center gap-1" style="cursor:pointer;font-size:12px;color:var(--text-secondary);padding:2px 0">
                                        <input type="checkbox" name="links[{{ $pm->id }}][]" value="{{ $_ct }}"
                                            {{ $_ctChecked ? 'checked' : '' }}
                                            style="width:13px;height:13px;accent-color:var(--accent);cursor:pointer">
                                        <span>{{ __("super-admin.charge_type_{$_ct}") ?? $_ct }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0 py-2">{{ __('super-admin.no_payment_methods') ?? 'لا توجد طرق دفع نشطة.' }}</p>
                @endif
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-accent btn-custom">
                {{ $taxRate ? __('general.update') : __('general.create') }}
            </button>
            <a href="{{ route('super.admin.tax-rates.index') }}" class="btn btn-outline-secondary btn-custom">
                {{ __('general.cancel') }}
            </a>
        </div>
    </form>
</x-super-admin-layout>
