<x-app-layout>
    <x-slot:title>{{ __('general.account') }} {{ __('general.settings') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('general.account') }}</x-slot>
    <x-slot:page-description>{{ __('profile.page_description') }}</x-slot>

    @php
        $recentNotifications = \App\Models\Notification::where('user_id', $user->id)
            ->latest()->take(10)->get();
        $unreadCount = \App\Models\Notification::where('user_id', $user->id)
            ->where('is_read', false)->count();
    @endphp

    <div class="profile-grid" x-data="{ tab: 'profile' }">
        <div class="profile-sidebar">
            <div class="profile-card">
                <div class="profile-avatar">
                    <div class="avatar-circle">{{ $initials }}</div>
                </div>
                <h4 class="profile-name mt-3">{{ $user->name }}</h4>
                <p class="profile-email">{{ $user->email }}</p>
                <nav class="profile-nav mt-3">
                    <button @click="tab = 'profile'" :class="{ 'active': tab === 'profile' }" class="profile-nav-item" style="background:none;border:none;cursor:pointer;text-align:start;width:100%;">
                        <i class="bi bi-person"></i>
                        <span>{{ __('profile.tab_profile_info') }}</span>
                    </button>
                    <button @click="tab = 'preferences'" :class="{ 'active': tab === 'preferences' }" class="profile-nav-item" style="background:none;border:none;cursor:pointer;text-align:start;width:100%;">
                        <i class="bi bi-sliders2"></i>
                        <span>{{ __('settings.preferences') }}</span>
                    </button>
                    <button @click="tab = 'security'" :class="{ 'active': tab === 'security' }" class="profile-nav-item" style="background:none;border:none;cursor:pointer;text-align:start;width:100%;">
                        <i class="bi bi-shield-lock"></i>
                        <span>{{ __('settings.security') }}</span>
                    </button>
                    <button @click="tab = 'notifications'" :class="{ 'active': tab === 'notifications' }" class="profile-nav-item" style="background:none;border:none;cursor:pointer;text-align:start;width:100%;">
                        <i class="bi bi-bell"></i>
                        <span>{{ __('profile.notifications') }}</span>
                        @if ($unreadCount > 0)
                            <span class="badge bg-danger ms-auto" style="font-size:10px;">{{ $unreadCount }}</span>
                        @endif
                    </button>
                    <a href="{{ route('account.settings.developer') }}" wire:navigate class="profile-nav-item">
                        <i class="bi bi-code-slash"></i>
                        <span>{{ __('developer.api_tokens') }}</span>
                    </a>
                </nav>
            </div>
        </div>

        <div class="profile-main">
            {{-- Profile Information Tab --}}
            <div x-show="tab === 'profile'" x-transition:enter.duration.200ms>
                <div class="settings-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-person" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('profile.account_info') }}</h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('profile.account_info_help') }}</p>
                        </div>
                    </div>
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            {{-- Preferences Tab --}}
            <div x-show="tab === 'preferences'" x-cloak x-transition:enter.duration.200ms>
                <div class="settings-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-sliders2" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('settings.preferences') }}</h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('settings.preferences_desc') }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('account.settings.update') }}">
                        @csrf

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('settings.language') }}</label>
                                <select name="language" class="form-custom">
                                    <option value="ar" {{ $user->locale === 'ar' ? 'selected' : '' }}>{{ __('general.ar') }}</option>
                                    <option value="fr" {{ $user->locale === 'fr' ? 'selected' : '' }}>{{ __('general.fr') }}</option>
                                    <option value="en" {{ $user->locale === 'en' ? 'selected' : '' }}>{{ __('general.en') }}</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('settings.currency') }}</label>
                                <select name="currency" class="form-custom">
                                    @foreach (\App\Helpers\CurrencyHelper::availableCurrencies() ?: [['code' => 'DZD', 'name' => 'Algerian Dinar'], ['code' => 'USD', 'name' => 'US Dollar'], ['code' => 'EUR', 'name' => 'Euro']] as $cur)
                                        <option value="{{ $cur['code'] }}" {{ $user->currency === $cur['code'] ? 'selected' : '' }}>{{ $cur['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('settings.timezone') }}</label>
                                <select name="timezone" class="form-custom">
                                    @php
                                        $timezones = [
                                            'Africa/Algiers' => 'Africa/Algiers (UTC+1)',
                                            'Africa/Cairo' => 'Africa/Cairo (UTC+2)',
                                            'Asia/Dubai' => 'Asia/Dubai (UTC+4)',
                                            'Europe/Paris' => 'Europe/Paris (UTC+1)',
                                            'Europe/London' => 'Europe/London (UTC+0)',
                                            'America/New_York' => 'America/New_York (UTC-5)',
                                            'America/Chicago' => 'America/Chicago (UTC-6)',
                                            'America/Denver' => 'America/Denver (UTC-7)',
                                            'America/Los_Angeles' => 'America/Los_Angeles (UTC-8)',
                                        ];
                                    @endphp
                                    @foreach($timezones as $value => $label)
                                        <option value="{{ $value }}" {{ $user->timezone === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('settings.theme') }}</label>
                                <div class="d-flex gap-2">
                                    <button type="button" @click="$store.theme.set('light')" class="btn {{ session('theme', 'light') === 'light' ? 'btn-accent' : 'btn-outline-secondary' }} btn-custom flex-fill">
                                        <i class="bi bi-sun-fill me-1"></i>{{ __('settings.light') }}
                                    </button>
                                    <button type="button" @click="$store.theme.set('dark')" class="btn {{ session('theme', 'light') === 'dark' ? 'btn-accent' : 'btn-outline-secondary' }} btn-custom flex-fill">
                                        <i class="bi bi-moon-fill me-1"></i>{{ __('settings.dark') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-accent btn-custom">
                            <i class="bi bi-check-lg me-1"></i>{{ __('general.save') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Security Tab --}}
            <div x-show="tab === 'security'" x-cloak x-transition:enter.duration.200ms>
                <div class="settings-card mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-shield-lock" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('general.update_password') }}</h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('profile.password_help') }}</p>
                        </div>
                    </div>
                    <livewire:profile.update-password-form />
                </div>

                <div class="settings-card mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-shield-check" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('settings.two_factor') }}</h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('messages.add_2fa_security') }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-size:14px;font-weight:500;">{{ __('general.status') }}</span>
                            @if ($user->hasTwoFactorEnabled())
                                <span class="badge-success">{{ __('general.enabled') }}</span>
                            @else
                                <span class="badge-muted">{{ __('general.disabled') }}</span>
                            @endif
                        </div>
                        <a href="{{ route('two-factor.setup') }}" class="btn btn-accent btn-sm">
                            <i class="bi bi-shield-plus me-1"></i>
                            <span>{{ __('general.manage') }}</span>
                        </a>
                    </div>
                </div>

                <div class="settings-card" style="border-color:rgba(239,68,68,0.2);">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(239,68,68,0.1);flex-shrink:0;">
                            <i class="bi bi-exclamation-triangle" style="color:var(--danger);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;color:var(--danger);">{{ __('general.danger_zone') }}</h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('profile.delete_account_help') }}</p>
                        </div>
                    </div>
                    <livewire:profile.delete-user-form />
                </div>
            </div>

            {{-- Notifications Tab --}}
            <div x-show="tab === 'notifications'" x-cloak x-transition:enter.duration.200ms>
                <div class="settings-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-bell" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('profile.notifications') }}</h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('profile.notifications_help') }}</p>
                        </div>
                    </div>
@if ($recentNotifications->count())
    <div class="notifications-list">
        @foreach ($recentNotifications as $notification)
            @php
                $notifIcon = match($notification->type) {
                    'budget_exceeded', 'budget_nearing_limit' => 'bi-exclamation-triangle text-danger',
                    'debt_reminder' => 'bi-credit-card-2-front text-warning',
                    'goal_achieved', 'goal_milestone' => 'bi-flag text-success',
                    'goal_deadline' => 'bi-clock text-info',
                    'zakat_reminder' => 'bi-heart text-primary',
                    'role_changed' => 'bi-shield-check text-warning',
                    default => 'bi-info-circle text-info',
                };
            @endphp
            <div class="notification-item {{ $notification->is_read ? '' : 'notification-unread' }}">
                <div class="notification-icon">
                    <i class="bi {{ $notifIcon }}"></i>
                </div>
                <div class="notification-body">
                    <p class="notification-title">{{ $notification->{'title_' . app()->getLocale()} }}</p>
                    <p class="notification-message">{{ $notification->{'message_' . app()->getLocale()} }}</p>
                    <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                </div>
                @if (!$notification->is_read)
                    <span class="notification-dot"></span>
                @endif
            </div>
        @endforeach
    </div>
@else
                        <div class="text-center py-4">
                            <i class="bi bi-bell-slash" style="font-size:2rem;color:var(--text-muted);"></i>
                            <p class="text-muted mt-2 mb-0">{{ __('profile.no_notifications') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
