<x-super-admin-layout>
    <x-slot:title>{{ __('admin.add_user') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('admin.add_user') }}</x-slot>
    <x-slot:page-description>{{ __('admin.add_user_desc') }}</x-slot>

    <div style="max-width:720px">
        <form action="{{ route('super.admin.users.store') }}" method="POST">
            @csrf

            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-person-plus"></i>{{ __('admin.user_details') }}</h5>
                </div>
                <div class="section-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="{{ __('general.name') }}" value="{{ old('name') }}" required>
                                <label>{{ __('general.name') }} <span class="text-danger">*</span></label>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="{{ __('general.email') }}" value="{{ old('email') }}" required>
                                <label>{{ __('general.email') }} <span class="text-danger">*</span></label>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label-custom mb-1">{{ __('general.password') }} <span class="text-danger">*</span></label>
                            <x-password-input name="password" id="password" required error="password" />
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <select name="locale" class="form-control @error('locale') is-invalid @enderror">
                                    <option value="ar" {{ old('locale') === 'ar' ? 'selected' : '' }}>{{ __('general.ar') }}</option>
                                    <option value="fr" {{ old('locale') === 'fr' ? 'selected' : '' }}>{{ __('general.fr') }}</option>
                                    <option value="en" {{ old('locale') === 'en' || !old('locale') ? 'selected' : '' }}>{{ __('general.en') }}</option>
                                </select>
                                <label>{{ __('settings.language') }}</label>
                                @error('locale') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <select name="currency" class="form-control @error('currency') is-invalid @enderror">
                                    @php $currencies = \App\Helpers\CurrencyHelper::availableCurrencies() ?: [['code' => 'DZD', 'name' => 'DZD'], ['code' => 'USD', 'name' => 'USD'], ['code' => 'EUR', 'name' => 'EUR']]; @endphp
                                    @foreach ($currencies as $cur)
                                        <option value="{{ $cur['code'] }}" {{ old('currency') === $cur['code'] || (!old('currency') && $loop->first) ? 'selected' : '' }}>{{ $cur['code'] }}</option>
                                    @endforeach
                                </select>
                                <label>{{ __('settings.currency') }}</label>
                                @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom mb-1">{{ __('general.status') }}</label>
                            <x-status-select domain="user" name="status" :selected="old('status', 'active')"
                                set="bi" size="lg" />
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating-group">
                                <textarea name="status_reason" class="form-control @error('status_reason') is-invalid @enderror" placeholder="{{ __('account.reason') }}" rows="2" style="min-height:60px">{{ old('status_reason') }}</textarea>
                                <label>{{ __('account.reason') }} <span style="color:var(--text-muted);font-weight:400">({{ __('general.optional') }})</span></label>
                                @error('status_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label style="font-size:13px;font-weight:500;color:var(--text-muted);display:block;margin-bottom:8px">{{ __('admin.roles') }}</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($roles as $role)
                                <label class="d-flex align-items-center gap-2" style="cursor:pointer;padding:8px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)" @click="$el.querySelector('input').click()">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ collect(old('roles'))->contains($role->id) ? 'checked' : '' }} style="accent-color:var(--accent);width:16px;height:16px">
                                    <span style="font-size:13px;font-weight:500;color:var(--text)">{{ $role->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('roles') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn" style="padding:9px 22px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:var(--primary);font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
                    <i class="bi bi-check-lg"></i>{{ __('admin.create_user') }}
                </button>
                <a href="{{ route('super.admin.users.index') }}" class="btn" style="padding:9px 22px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text);font-weight:500;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
                    {{ __('general.cancel') }}
                </a>
            </div>
        </form>
    </div>
</x-super-admin-layout>
