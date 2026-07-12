<x-super-admin-layout>
    <x-slot:title>{{ $coupon ? __('super-admin.edit_coupon') : __('super-admin.create_coupon') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ $coupon ? __('super-admin.edit_coupon') : __('super-admin.create_coupon') }}</x-slot>
    <x-slot:page-description>{{ $coupon ? __('super-admin.edit_coupon_desc') : __('super-admin.create_coupon_desc') }}</x-slot>

    <div style="max-width:640px">
        <form method="POST" action="{{ $coupon ? route('super.admin.coupons.update', $coupon) : route('super.admin.coupons.store') }}">
            @csrf @if($coupon) @method('PUT') @endif

            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-tag"></i>{{ __('super-admin.coupon_details') }}</h5>
                </div>
                <div class="section-card-body">
                    <div class="form-floating-group">
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="{{ __('super-admin.coupon_code') }}" value="{{ old('code', $coupon->code ?? '') }}" maxlength="50" required>
                        <label>{{ __('super-admin.coupon_code') }} <span class="text-danger">*</span></label>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                    <option value="fixed" {{ old('type', $coupon->type ?? '') === 'fixed' ? 'selected' : '' }}>{{ __('general.fixed') }} ($)</option>
                                    <option value="percentage" {{ old('type', $coupon->type ?? '') === 'percentage' ? 'selected' : '' }}>{{ __('general.percentage') }} (%)</option>
                                </select>
                                <label>{{ __('super-admin.coupon_type') }} <span class="text-danger">*</span></label>
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <input type="number" name="value" class="form-control @error('value') is-invalid @enderror" placeholder="{{ __('super-admin.coupon_value') }}" value="{{ old('value', $coupon->value ?? '') }}" step="0.01" min="0" required>
                                <label>{{ __('super-admin.coupon_value') }} <span class="text-danger">*</span></label>
                                @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <input type="number" name="min_amount" class="form-control @error('min_amount') is-invalid @enderror" placeholder="{{ __('super-admin.coupon_min_amount') }}" value="{{ old('min_amount', $coupon->min_amount ?? '') }}" step="0.01" min="0">
                                <label>{{ __('super-admin.coupon_min_amount') }}</label>
                                @error('min_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <input type="number" name="max_uses" class="form-control @error('max_uses') is-invalid @enderror" placeholder="{{ __('super-admin.coupon_max_uses') }}" value="{{ old('max_uses', $coupon->max_uses ?? '') }}" min="1">
                                <label>{{ __('super-admin.coupon_max_uses') }}</label>
                                @error('max_uses') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <input type="datetime-local" name="starts_at" class="form-control @error('starts_at') is-invalid @enderror" placeholder="{{ __('super-admin.coupon_starts') }}" value="{{ old('starts_at', $coupon->starts_at ?? '') }}">
                                <label>{{ __('super-admin.coupon_starts') }}</label>
                                @error('starts_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <input type="datetime-local" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" placeholder="{{ __('super-admin.coupon_expires') }}" value="{{ old('expires_at', $coupon->expires_at ?? '') }}">
                                <label>{{ __('super-admin.coupon_expires') }}</label>
                                @error('expires_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <label class="d-flex align-items-center gap-2" style="cursor:pointer;padding:8px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)" @click="event.preventDefault();this.querySelector('.toggle-switch-btn').click()">
                            <x-toggle-switch name="is_active" :checked="$coupon->is_active ?? true" />
                            <span style="font-size:13px;font-weight:500;color:var(--text)">{{ __('general.active') }}</span>
                        </label>
                    </div>
                </div>
            </div>

        {{-- ربط طرق الدفع --}}
        <div class="section-card mt-4">
            <div class="section-card-header">
                <h5><i class="bi bi-credit-card"></i> {{ __('super-admin.payment_method_restrictions') ?? 'ربط طرق الدفع' }}</h5>
            </div>
            <div class="section-card-body">
                <div class="settings-section-desc small mb-3">
                    {{ __('super-admin.payment_method_restrictions_hint') ?? 'اختر طرق الدفع التي يعمل معها هذا الكوبون. اترك فارغاً ليعمل مع جميع الطرق.' }}
                </div>
                @if($paymentMethods->isNotEmpty())
                    <div class="d-flex flex-wrap gap-3">
                        @foreach($paymentMethods as $pm)
                            @php $checked = in_array($pm->id, old('payment_methods', $linkedPaymentMethods->toArray())); @endphp
                            <label class="d-flex align-items-center gap-2" style="cursor:pointer;padding:8px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);user-select:none">
                                <input type="checkbox" name="payment_methods[]" value="{{ $pm->id }}" {{ $checked ? 'checked' : '' }}
                                    style="width:16px;height:16px;accent-color:var(--accent);cursor:pointer">
                                <span style="font-size:13px;font-weight:500;color:var(--text)">{{ $pm->name }}</span>
                            </label>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0 py-2">{{ __('super-admin.no_payment_methods') ?? 'لا توجد طرق دفع نشطة.' }}</p>
                @endif
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn" style="padding:9px 22px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
                {{ $coupon ? __('general.update') : __('general.create') }}
            </button>
            <a href="{{ route('super.admin.coupons.index') }}" class="btn" style="padding:9px 22px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text);font-weight:500;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
                {{ __('general.cancel') }}
            </a>
        </div>
        </form>
    </div>
</x-super-admin-layout>
