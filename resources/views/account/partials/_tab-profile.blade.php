{{-- Profile Information --}}
<div class="settings-card mb-4">
    <div class="d-flex align-items-center gap-3 mb-3">
        <div class="d-flex align-items-center justify-content-center rounded-circle"
            style="width:36px;height:36px;background:rgba(21,183,108,0.1);flex-shrink:0;">
            <i class="bi bi-person" style="color:var(--accent);font-size:16px;"></i>
        </div>
        <div>
            <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('profile.account_info') }}</h5>
            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('profile.account_info_help') }}</p>
        </div>
    </div>
    <livewire:profile.update-profile-information-form />
</div>

{{-- Preferences --}}
<div class="settings-card mt-4">
    <div class="d-flex align-items-center gap-3 mb-3">
        <div class="d-flex align-items-center justify-content-center rounded-circle"
            style="width:36px;height:36px;background:rgba(99,102,241,0.1);flex-shrink:0;">
            <i class="bi bi-sliders2" style="color:var(--accent);font-size:16px;"></i>
        </div>
        <div>
            <h5 class="mb-0" style="font-weight:600;font-size:15px;">{{ __('settings.preferences') }}</h5>
            <p class="mb-0" style="font-size:13px;color:var(--text-muted);">{{ __('settings.preferences_desc') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('settings.account.update') }}">
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
                    <x-button @click="$store.theme.set('light')" variant="{{ session('theme', 'light') === 'light' ? 'accent' : 'outline' }}" icon="bi bi-sun-fill" class="flex-fill">{{ __('settings.light') }}</x-button>
                    <x-button @click="$store.theme.set('dark')" variant="{{ session('theme', 'light') === 'dark' ? 'accent' : 'outline' }}" icon="bi bi-moon-fill" class="flex-fill">{{ __('settings.dark') }}</x-button>
                </div>
            </div>
        </div>

        <x-button submit icon="bi bi-check-lg">{{ __('general.save') }}</x-button>
    </form>
</div>

{{-- Zakat Haul Settings --}}
<div class="settings-card mt-4">
    <div class="d-flex align-items-center gap-3 mb-3">
        <div class="d-flex align-items-center justify-content-center rounded-circle"
            style="width:36px;height:36px;background:rgba(99,102,241,0.1);flex-shrink:0;">
            <i class="bi bi-calendar-event" style="color:var(--accent);font-size:16px;"></i>
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

        <x-button submit icon="bi bi-check-lg">{{ __('general.save') }}</x-button>
    </form>
</div>
