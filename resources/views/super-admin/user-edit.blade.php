<x-super-admin-layout>
    <x-slot:title>{{ __('admin.edit_user') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('admin.edit_user') }}</x-slot>
    <x-slot:page-description>{{ $user->name }} ({{ $user->email }})</x-slot>

    <div style="max-width:720px">
        <form action="{{ route('super.admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-person-gear"></i> {{ __('admin.user_details') }}</h5>
                </div>
                <div class="section-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="{{ __('general.name') }}" value="{{ old('name', $user->name) }}" required>
                                <label>{{ __('general.name') }} <span class="text-danger">*</span></label>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="{{ __('general.email') }}" value="{{ old('email', $user->email) }}" required>
                                <label>{{ __('general.email') }} <span class="text-danger">*</span></label>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label-custom mb-1">{{ __('general.new_password') }}</label>
                            <x-password-input name="password" id="password" placeholder="{{ __('admin.password_leave_blank') }}" error="password" />
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <select name="locale" class="form-control @error('locale') is-invalid @enderror">
                                    <option value="ar" {{ old('locale', $user->locale) === 'ar' ? 'selected' : '' }}>{{ __('general.ar') }}</option>
                                    <option value="fr" {{ old('locale', $user->locale) === 'fr' ? 'selected' : '' }}>{{ __('general.fr') }}</option>
                                    <option value="en" {{ old('locale', $user->locale) === 'en' ? 'selected' : '' }}>{{ __('general.en') }}</option>
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
                                        <option value="{{ $cur['code'] }}" {{ old('currency', $user->currency) === $cur['code'] ? 'selected' : '' }}>{{ $cur['code'] }}</option>
                                    @endforeach
                                </select>
                                <label>{{ __('settings.currency') }}</label>
                                @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom mb-1">{{ __('general.status') }}</label>
                            <x-status-select domain="user" name="status" :selected="old('status', $user->status)"
                                set="bi" size="lg" />
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating-group">
                                <textarea name="status_reason" class="form-control @error('status_reason') is-invalid @enderror" placeholder="{{ __('account.reason') }}" rows="2" style="min-height:60px">{{ old('status_reason', $user->statusRecord?->status_reason) }}</textarea>
                                <label>{{ __('account.reason') }} <span style="color:var(--text-muted);font-weight:400">({{ __('general.optional') }})</span></label>
                                @error('status_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <select name="theme" class="form-control @error('theme') is-invalid @enderror">
                                    <option value="light" {{ old('theme', $user->theme) === 'light' ? 'selected' : '' }}>Light</option>
                                    <option value="dark" {{ old('theme', $user->theme) === 'dark' ? 'selected' : '' }}>Dark</option>
                                </select>
                                <label>{{ __('settings.theme') }}</label>
                                @error('theme') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating-group">
                                <select name="timezone" class="form-control @error('timezone') is-invalid @enderror">
                                    @php $selectedTz = old('timezone', $user->timezone); @endphp
                                    @foreach (timezone_identifiers_list() as $tz)
                                        <option value="{{ $tz }}" {{ $selectedTz === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                    @endforeach
                                </select>
                                <label>{{ __('super-admin.timezone') }}</label>
                                @error('timezone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center gap-3">
                            <span style="font-size:13px;font-weight:500;color:var(--text-muted)">{{ __('general.email_verification') }}:</span>
                            @if ($user->email_verified_at)
                                <x-status-badge domain="general" status="yes" set="bi" />
                                <span style="font-size:12px;color:var(--text-muted)">{{ $user->email_verified_at->format('Y/m/d H:i') }}</span>
                            @else
                                <x-status-badge domain="general" status="no" set="bi" />
                            @endif
                        </div>
                    </div>

                    <div class="mt-3">
                        <label style="font-size:13px;font-weight:500;color:var(--text-muted);display:block;margin-bottom:8px">{{ __('admin.roles') }}</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($roles as $role)
                                <label class="d-flex align-items-center gap-2" style="cursor:pointer;padding:8px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)" @click="$el.querySelector('input').click()">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ in_array($role->id, old('roles', $userRoles)) ? 'checked' : '' }} style="accent-color:var(--accent);width:16px;height:16px">
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
                    <i class="bi bi-check-lg"></i>{{ __('admin.save_user') }}
                </button>
                <a href="{{ route('super.admin.users.index') }}" class="btn" style="padding:9px 22px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text);font-weight:500;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
                    {{ __('general.cancel') }}
                </a>
            </div>
        </form>
    </div>
</x-super-admin-layout>
