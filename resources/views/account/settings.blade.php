<x-app-layout>
    <x-slot:title>{{ __('general.account') }} {{ __('general.settings') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('general.account') }}</x-slot>
    <x-slot:page-description>{{ __('profile.page_description') }}</x-slot>

    @php
        $recentNotifications = \App\Models\Notification::where('user_id', $user->id)->latest()->take(10)->get();
        $unreadCount = \App\Models\Notification::where('user_id', $user->id)->where('is_read', false)->count();
    @endphp

    <div class="profile-grid" x-data="{ tab: 'profile' }">
        <div class="profile-sidebar">
            <div class="profile-card">
                <div class="profile-avatar mb-3">
                    <div class="avatar-circle">{{ $initials }}</div>
                    <div class="avatar-online"></div>
                </div>
                <h4 class="profile-name mt-3">{{ $user->name }}</h4>
                <p class="profile-email">{{ $user->email }}</p>
                <span class="profile-joined">{{ __('profile.member_since') }} {{ $user->created_at->translatedFormat('F Y') }}</span>
                <nav class="profile-nav mt-3">
                    <button @click="tab = 'profile'" :class="{ 'active': tab === 'profile' }" class="profile-tab-btn">
                        <i class="bi bi-person ms-1 "></i>
                        <span>{{ __('profile.tab_profile_info') }}</span>
                    </button>
                    <button @click="tab = 'preferences'" :class="{ 'active': tab === 'preferences' }" class="profile-tab-btn">
                        <i class="bi bi-sliders2 ms-1 "></i>
                        <span>{{ __('settings.preferences') }}</span>
                    </button>
                    <button @click="tab = 'security'" :class="{ 'active': tab === 'security' }" class="profile-tab-btn">
                        <i class="bi bi-shield-lock ms-1 "></i>
                        <span>{{ __('settings.security') }}</span>
                    </button>
                    <button @click="tab = 'notifications'" :class="{ 'active': tab === 'notifications' }" class="profile-tab-btn">
                        <i class="bi bi-bell  ms-1 "></i>
                        <span>{{ __('profile.notifications') }}</span>
                        @if ($unreadCount > 0)
                            <x-status-badge domain="general" status="pending" set="bi" format="dot" size="xs" class="ms-auto" />
                        @endif
                    </button>
                    <a href="{{ route('account.settings.developer') }}" wire:navigate class="profile-tab-btn">
                        <i class="bi bi-code-slash ms-1 "></i>
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
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-person" style="color:var(--accent);font-size:16px; ms-1 "></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('profile.account_info') }}
                            </h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                                {{ __('profile.account_info_help') }}</p>
                        </div>
                    </div>
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            {{-- Preferences Tab --}}
            <div x-show="tab === 'preferences'" x-cloak x-transition:enter.duration.200ms>
                <div class="settings-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-sliders2" style="color:var(--accent);font-size:16px; ms-1 "></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('settings.preferences') }}
                            </h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                                {{ __('settings.preferences_desc') }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('account.settings.update') }}">
                        @csrf

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('settings.language') }}</label>
                                <select name="language" class="form-custom">
                                    <option value="ar" {{ $user->locale === 'ar' ? 'selected' : '' }}>
                                        {{ __('general.ar') }}</option>
                                    <option value="fr" {{ $user->locale === 'fr' ? 'selected' : '' }}>
                                        {{ __('general.fr') }}</option>
                                    <option value="en" {{ $user->locale === 'en' ? 'selected' : '' }}>
                                        {{ __('general.en') }}</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('settings.currency') }}</label>
                                <select name="currency" class="form-custom">
                                    @foreach (\App\Helpers\CurrencyHelper::availableCurrencies() ?: [['code' => 'DZD', 'name' => 'Algerian Dinar'], ['code' => 'USD', 'name' => 'US Dollar'], ['code' => 'EUR', 'name' => 'Euro']] as $cur)
                                        <option value="{{ $cur['code'] }}"
                                            {{ $user->currency === $cur['code'] ? 'selected' : '' }}>
                                            {{ $cur['name'] }}</option>
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
                                    @foreach ($timezones as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ $user->timezone === $value ? 'selected' : '' }}>{{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('settings.theme') }}</label>
                                <div class="d-flex gap-2">
                                    <button type="button" @click="$store.theme.set('light')"
                                        class="btn {{ session('theme', 'light') === 'light' ? 'btn-accent' : 'btn-outline-secondary' }} btn-custom flex-fill">
                                        <i class="bi bi-sun-fillms-1 ms-1 "></i>{{ __('settings.light') }}
                                    </button>
                                    <button type="button" @click="$store.theme.set('dark')"
                                        class="btn {{ session('theme', 'light') === 'dark' ? 'btn-accent' : 'btn-outline-secondary' }} btn-custom flex-fill">
                                        <i class="bi bi-moon-fillms-1 ms-1 "></i>{{ __('settings.dark') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-accent btn-custom">
                            <i class="bi bi-check-lg ms-1 ms-1 "></i>{{ __('general.save') }}
                        </button>
                    </form>
                </div>

                {{-- Zakat Haul Settings --}}
                <div class="settings-card mt-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width:36px;height:36px;background:rgba(99,102,241,0.1);flex-shrink:0;">
                            <i class="bi bi-calendar-event" style="color:#6366F1;font-size:16px; ms-1 "></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('zakat.haul_settings') }}</h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('zakat.zakat_haul') }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('zakat.haul-settings') }}"
                        x-data="hijriDatePicker({
                            calendarType: '{{ ($user->calendar_type ?? 'hijri') }}',
                            gregorianDate: '{{ $user->zakat_start_date?->format('Y-m-d') ?? '' }}',
                            hijriYear: '{{ $user->getZakatStartDateHijri()['year'] ?? '' }}',
                            hijriMonth: '{{ $user->getZakatStartDateHijri()['month'] ?? '' }}',
                            hijriDay: '{{ $user->getZakatStartDateHijri()['day'] ?? '' }}',
                            locale: '{{ app()->getLocale() }}'
                        })">
                        @csrf
                        @method('PUT')
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('zakat.calendar_type') }} <span class="text-danger">*</span></label>
                                <select name="calendar_type" class="form-custom" required x-model="calendarType" @change="$dispatch('calendar-type-changed', calendarType)">
                                    <option value="hijri">{{ __('zakat.hijri') }} (354 {{ __('zakat.days_per_year') }})</option>
                                    <option value="gregorian">{{ __('zakat.gregorian') }} (365 {{ __('zakat.days_per_year') }})</option>
                                </select>
                            </div>
                        </div>

                        {{-- Hijri Date Input --}}
                        <div class="mb-3" x-show="calendarType === 'hijri'" x-transition>
                            <label class="form-label-custom">{{ __('zakat.zakat_start_date') }} <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <label class="form-label-custom" style="font-size:11px">{{ __('zakat.hijri_year') }}</label>
                                    <input type="text" inputmode="numeric" pattern="\d{4}" class="form-custom" placeholder="1446"
                                        :value="hijriYear" @input="onYearInput($event.target.value)" list="hijri-years-list-settings"
                                        maxlength="4" required>
                                    <datalist id="hijri-years-list-settings">
                                        @for($y = 1500; $y >= 1300; $y--)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </datalist>
                                    <input type="hidden" name="zakat_start_date_hijri_year" :value="hijriYear">
                                </div>
                                <div class="col-4">
                                    <label class="form-label-custom" style="font-size:11px">{{ __('zakat.hijri_month') }}</label>
                                    <select class="form-custom" x-model="hijriMonth" @change="updateGregorian()" required>
                                        <option value="">{{ __('zakat.hijri_month') }}</option>
                                        @php
                                            $arMonths = ['محرم','صفر','ربيع الأول','ربيع الثاني','جمادى الأولى','جمادى الثانية','رجب','شعبان','رمضان','شوال','ذو القعدة','ذو الحجة'];
                                            $frMonths = ['Mouharram','Safar','Rabia al-Aoual','Rabia al-Thani','Joumada al-Oula','Joumada al-Thani','Rajab','Cha\'ban','Ramadan','Chawwal','Dhou al-Qa\'da','Dhou al-Hijja'];
                                            $enMonths = ['Muharram','Safar','Rabi al-Awwal','Rabi al-Thani','Jumada al-Ula','Jumada al-Thani','Rajab','Shaban','Ramadan','Shawwal','Dhul Qadah','Dhul Hijjah'];
                                            $displayMonths = app()->getLocale() === 'ar' ? $arMonths : (app()->getLocale() === 'fr' ? $frMonths : $enMonths);
                                        @endphp
                                        @foreach($displayMonths as $i => $name)
                                            <option value="{{ $i + 1 }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="zakat_start_date_hijri_month" :value="hijriMonth">
                                </div>
                                <div class="col-4">
                                    <label class="form-label-custom" style="font-size:11px">{{ __('zakat.hijri_day') }}</label>
                                    <input type="text" inputmode="numeric" pattern="\d{1,2}" class="form-custom" placeholder="15"
                                        :value="hijriDay" @input="onDayInput($event.target.value)" list="hijri-days-list-settings"
                                        maxlength="2" required>
                                    <datalist id="hijri-days-list-settings">
                                        @for($d = 1; $d <= 30; $d++)
                                            <option value="{{ $d }}">{{ $d }}</option>
                                        @endfor
                                    </datalist>
                                    <input type="hidden" name="zakat_start_date_hijri_day" :value="hijriDay">
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size:11px" x-show="gregorianDate">
                                <i class="bi bi-arrow-left-right"></i>
                                {{ __('zakat.gregorian_equivalent') }}: <span x-text="gregorianDate"></span>
                            </small>
                        </div>

                        {{-- Gregorian Date Input --}}
                        <div class="mb-3" x-show="calendarType === 'gregorian'" x-transition>
                            <label class="form-label-custom">{{ __('zakat.zakat_start_date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="zakat_start_date" class="form-custom"
                                x-model="gregorianDate"
                                @change="onGregorianChange()"
                                max="{{ date('Y-m-d') }}" required>
                        </div>

                        @if($user->hasZakatHaulStarted())
                            <div class="mb-3 p-2" style="border-radius:6px; background:rgba(99,102,241,0.06); font-size:12px">
                                <span style="color:var(--text-muted)">{{ __('zakat.next_zakat_date') }}:</span>
                                <span class="fw-bold">{{ $user->nextZakatDate()?->format('Y/m/d') ?? '-' }}</span>
                                @if(! $user->isZakatDue())
                                    <span style="color:var(--text-muted)"> — {{ __('zakat.days_left', ['days' => $user->daysUntilNextZakat()]) }}</span>
                                @else
                                    <span style="color:var(--success)"> — {{ __('zakat.haul_complete') }}</span>
                                @endif
                            </div>
                        @endif

                        <button type="submit" class="btn btn-accent btn-custom">
                            <i class="bi bi-check-lg ms-1 ms-1 "></i>{{ __('general.save') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Security Tab --}}
            <div x-show="tab === 'security'" x-cloak x-transition:enter.duration.200ms>
                <div class="settings-card mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-shield-lock" style="color:var(--accent);font-size:16px; ms-1 "></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">
                                {{ __('general.update_password') }}</h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                                {{ __('profile.password_help') }}</p>
                        </div>
                    </div>
                    <livewire:profile.update-password-form />
                </div>

                <div class="settings-card mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-shield-check" style="color:var(--accent);font-size:16px; ms-1 "></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">
                                {{ __('settings.two_factor') }}</h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                                {{ __('messages.add_2fa_security') }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-size:14px;font-weight:500;">{{ __('general.status') }}</span>
                            @if ($user->hasTwoFactorEnabled())
                                <x-status-badge domain="general" status="yes" set="bi" />
                            @else
                                <x-status-badge domain="general" status="no" set="bi" />
                            @endif
                        </div>
                        <a href="{{ route('two-factor.setup') }}" class="btn btn-accent btn-sm">
                            <i class="bi bi-shield-plusms-1 ms-1 "></i>
                            <span>{{ __('general.manage') }}</span>
                        </a>
                    </div>
                </div>

                {{-- Active Sessions --}}
                <div class="settings-card mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle"
                                style="width:36px;height:36px;background:rgba(59,130,246,0.1);flex-shrink:0;">
                                <i class="bi bi-pc-display" style="color:#3b82f6;font-size:16px; ms-1 "></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-weight:600;font-size:15px;">
                                    {{ __('settings.active_sessions') }}</h5>
                                <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                                    {{ __('settings.active_sessions_help') }}</p>
                            </div>
                        </div>
                        @if ($sessions->count() > 1)
                            <form method="POST" action="{{ route('account.settings.sessions.revoke-all') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm"
                                    style="background:rgba(239,68,68,0.1);color:var(--danger);border:1px solid rgba(239,68,68,0.2);"
                                    onclick="return confirm('{{ __('settings.confirm_revoke_all') }}')">
                                    <i class="bi bi-box-arrow-rightms-1 ms-1 "></i>{{ __('settings.revoke_all_others') }}
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="sessions-list">
                        @forelse ($sessions as $session)
                            <div
                                class="session-item d-flex align-items-center justify-content-between py-3 {{ $loop->first ? '' : 'border-top' }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle"
                                        style="width:40px;height:40px;background:{{ $session->is_current ? 'rgba(21,183,108,0.1)' : 'var(--bg-secondary)' }};flex-shrink:0;">
                                        <i class="bi {{ $session->device === 'phone' ? 'bi-phone' : ($session->device === 'tablet' ? 'bi-tablet' : 'bi-pc-display') }}"
                                            style="color:{{ $session->is_current ? 'var(--accent)' : 'var(--text-muted)' }};font-size:18px; ms-1 "></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span style="font-weight:500;font-size:14px;">{{ $session->browser }} on
                                                {{ $session->os }}</span>
                                            @if ($session->is_current)
                                                <x-status-badge domain="general" status="active" set="bi" size="xs" />
                                            @endif
                                        </div>
                                        <div style="font-size:12px;color:var(--text-muted);">
                                            <i class="bi bi-globems-1 ms-1 "></i>{{ $session->ip_address }}
                                            &middot;
                                            <i
                                                class="bi bi-clockms-1 ms-1 "></i>{{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                                            @if ($session->login_at)
                                                &middot;
                                                <i
                                                    class="bi bi-box-arrow-in-rightms-1 ms-1 "></i>{{ $session->login_at->diffForHumans() }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if (!$session->is_current)
                                    <form method="POST"
                                        action="{{ route('account.settings.sessions.revoke', $session->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm"
                                            style="background:transparent;color:var(--text-muted);border:1px solid var(--border-color);"
                                            title="{{ __('settings.revoke_session') }}">
                                            <i class="bi bi-x-lg ms-1 "></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-3">
                                <x-empty-state icon="bi bi-pc-display" :title="__('settings.no_sessions')" />
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Login History --}}
                <div class="settings-card mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width:36px;height:36px;background:rgba(168,85,247,0.1);flex-shrink:0;">
                            <i class="bi bi-clock-history" style="color:#a855f7;font-size:16px; ms-1 "></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">
                                {{ __('settings.login_history') }}</h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                                {{ __('settings.login_history_help') }}</p>
                        </div>
                    </div>

                    @if ($loginHistory->count())
                        <div class="table-responsive">
                            <table class="data-table" style="margin:0;">
                                <thead>
                                    <tr>
                                        <th>{{ __('general.status') }}</th>
                                        <th>{{ __('general.ip_address') }}</th>
                                        <th>{{ __('general.device') }}</th>
                                        <th>{{ __('general.user_agent') }}</th>
                                        <th>{{ __('general.os') }}</th>
                                        <th>{{ __('general.date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($loginHistory as $attempt)
                                        <tr>
                                            <td>
                                                @if ($attempt->status === 'success')
                                                    <x-status-icon domain="general" status="success" set="bi" class="ms-1 text-success" />
                                                    <span style="color:var(--accent);font-weight:500;">{{ __('general.success') }}</span>
                                                @else
                                                    <x-status-icon domain="general" status="failed" set="bi" class="ms-1 text-danger" />
                                                    <span style="color:var(--danger);font-weight:500;">{{ __('general.failed') }}</span>
                                                @endif
                                                @if ($attempt->suspicious)
                                                    <x-status-badge domain="general" status="suspended" set="bi" size="xs" class="ms-1" />
                                                @endif
                                            </td>
                                            <td style="font-family:monospace;font-size:13px;">
                                                {{ $attempt->ip_address }}</td>
                                            <td>

                                                <x-status-badge domain="general" status="{{ $attempt->device === 'phone' ? 'phone'
                                                    : ($attempt->device === 'tablet' ? 'tablet' : 'pc') }}" set="bi" size="xs" class="ms-1" />
                                                <i
                                                    class="bi bi-{{ $attempt->device === 'phone' ? 'phone' : ($attempt->device === 'tablet' ? 'tablet' : 'pc-display') }}
                                                     ms-1 ms-1 "></i>{{ __('general.' . $attempt->device) }}
                                            </td>
                                            <td>{{ $attempt->browser }}</td>
                                            <td>{{ $attempt->os }}</td>
                                            <td style="font-size:13px;color:var(--text-muted);">
                                                {{ $attempt->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <x-empty-state icon="bi bi-clock-history" :title="__('settings.no_login_history')" />
                        </div>
                    @endif
                </div>

                <div class="settings-card" style="border-color:rgba(239,68,68,0.2);">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width:36px;height:36px;background:rgba(239,68,68,0.1);flex-shrink:0;">
                            <i class="bi bi-exclamation-triangle" style="color:var(--danger);font-size:16px; ms-1 "></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;color:var(--danger);">
                                {{ __('general.danger_zone') }}</h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                                {{ __('profile.delete_account_help') }}</p>
                        </div>
                    </div>
                    <livewire:profile.delete-user-form />
                </div>
            </div>

            {{-- Notifications Tab --}}
            <div x-show="tab === 'notifications'" x-cloak x-transition:enter.duration.200ms>
                <div class="settings-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
                            <i class="bi bi-bell" style="color:var(--accent);font-size:16px; ms-1 "></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-weight:600;font-size:15px;">
                                {{ __('profile.notifications') }}</h5>
                            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">
                                {{ __('profile.notifications_help') }}</p>
                        </div>
                    </div>
                    @if ($recentNotifications->count())
                        <div class="notifications-list">
                            @foreach ($recentNotifications as $notification)
                                @php
                                    $notifIcon = match ($notification->type) {
                                        'budget_exceeded',
                                        'budget_nearing_limit'
                                            => 'bi-exclamation-triangle text-danger',
                                        'debt_reminder' => 'bi-credit-card-2-front text-warning',
                                        'goal_achieved', 'goal_milestone' => 'bi-flag text-success',
                                        'goal_deadline' => 'bi-clock text-info',
                                        'zakat_reminder' => 'bi-heart text-primary',
                                        'zakat_approaching' => 'bi-hourglass-split text-primary',
                                        'role_changed' => 'bi-shield-check text-warning',
                                        default => 'bi-info-circle text-info',
                                    };
                                @endphp
                                <div
                                    class="notification-item {{ $notification->is_read ? '' : 'notification-unread' }}">
                                    <div class="notification-icon">
                                        <i class="bi {{ $notifIcon }} ms-1 "></i>
                                    </div>
                                    <div class="notification-body">
                                        <p class="notification-title">
                                            {{ $notification->{'title_' . app()->getLocale()} }}</p>
                                        <p class="notification-message">
                                            {{ $notification->{'message_' . app()->getLocale()} }}</p>
                                        <span
                                            class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if (!$notification->is_read)
                                        <span class="notification-dot"></span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <x-empty-state icon="bi bi-bell-slash" :title="__('profile.no_notifications')" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
