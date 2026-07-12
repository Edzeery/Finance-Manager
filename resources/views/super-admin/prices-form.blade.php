<x-super-admin-layout>
    <x-slot:title>{{ $price ? __('super-admin.edit_price') : __('super-admin.create_price') }} - {{ $plan->name }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ $price ? __('super-admin.edit_price') : __('super-admin.create_price') }}</x-slot>
    <x-slot:page-description>{{ $plan->name }} — {{ $price ? __('super-admin.edit_price_desc') : __('super-admin.create_price_desc') }}</x-slot>

    <div style="max-width:640px">
        <form method="POST" action="{{ $price ? route('super.admin.plans.prices.update', [$plan, $price]) : route('super.admin.plans.prices.store', $plan) }}">
            @csrf @if($price) @method('PUT') @endif

            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-currency-dollar"></i>{{ __('super-admin.price_details') }}</h5>
                </div>
                <div class="section-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <select name="billing_period" class="form-control @error('billing_period') is-invalid @enderror" required>
                                    <option value="monthly" {{ old('billing_period', $price->billing_period ?? '') === 'monthly' ? 'selected' : '' }}>{{ __('super-admin.monthly') }}</option>
                                    <option value="yearly" {{ old('billing_period', $price->billing_period ?? '') === 'yearly' ? 'selected' : '' }}>{{ __('super-admin.yearly') }}</option>
                                </select>
                                <label>{{ __('super-admin.price_period') }} <span class="text-danger">*</span></label>
                                @error('billing_period') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" placeholder="{{ __('super-admin.price_currency') }}" value="{{ old('currency', $price->currency ?? 'USD') }}" maxlength="10" required>
                                <label>{{ __('super-admin.price_currency') }} <span class="text-danger">*</span></label>
                                @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-floating-group">
                        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" placeholder="{{ __('super-admin.price_amount') }}" value="{{ old('price', $price->price ?? '') }}" step="0.01" min="0" required>
                        <label>{{ __('super-admin.price_amount') }} <span class="text-danger">*</span></label>
                        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <label class="d-flex align-items-center gap-2" style="cursor:pointer;padding:8px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)" @click="event.preventDefault();this.querySelector('.toggle-switch-btn').click()">
                            <x-toggle-switch name="is_active" :checked="$price->is_active ?? true" />
                            <span style="font-size:13px;font-weight:500;color:var(--text)">{{ __('general.active') }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn" style="padding:9px 22px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
                    {{ $price ? __('general.update') : __('general.create') }}
                </button>
                <a href="{{ route('super.admin.plans.prices.index', $plan) }}" class="btn" style="padding:9px 22px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text);font-weight:500;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
                    {{ __('general.cancel') }}
                </a>
            </div>
        </form>
    </div>
</x-super-admin-layout>
