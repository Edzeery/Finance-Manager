@props([
    'variant' => 'guest',
])

@php
    $isGuest = $variant === 'guest';
    $isSetup = $variant === 'setup';
    $isProfile = $variant === 'profile';
@endphp

<nav class="{{ $isGuest ? 'guest-navbar' : 'setup-navbar' }}">
    <div class="{{ $isGuest ? 'guest-navbar-inner' : 'setup-navbar-inner' }}">
        @if ($isGuest)
            <a href="/" class="guest-navbar-brand">
                <div class="logo-icon" style="width:28px;height:28px;font-size:11px">FM</div>
                <span class="logo-text" style="font-size:15px">{{ config('app.name') }}</span>
            </a>
            <div class="guest-navbar-controls">
                <x-language-switcher variant="dropdown-bs" triggerClass="topbar-btn dropdown-toggle" />
                @if (Route::has('onboarding.plan') || Route::has('verification.notice'))
                    <x-currency-switcher variant="dropdown-bs" triggerClass="topbar-btn" />
                @endif


                <button class="topbar-btn" @click="toggleTheme()" type="button">
                    <i class="bi {{ session('theme', 'light') === 'dark' ? 'bi-sun-fill' : 'bi-moon-fill' }}"></i>
                </button>
                @auth

                    <x-button href="{{ route('dashboard') }}" icon="bi bi-grid-1x2-fillms-1" class="mb-0 d-flex align-items-center gap-2">{{ __('general.dashboard') }}</x-button>
                @endauth
            </div>
        @elseif ($isSetup)
            <div class="setup-navbar-left">
                <a href="{{ app(\App\Services\RedirectService::class)->getHomeUrl(auth()->user()) }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i>
                    <span>{{ __('general.back_to_dashboard') }}</span>
                </a>
            </div>
            <div class="setup-navbar-center">
                <span class="setup-brand">{{ config('app.name') }}</span>
            </div>
            <div class="setup-navbar-right"></div>
        @elseif ($isProfile)
            <div class="setup-navbar-left">
                <a href="{{ app(\App\Services\RedirectService::class)->getHomeUrl(auth()->user()) }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i>
                    <span>{{ __('general.back_to_dashboard') }}</span>
                </a>
            </div>
            <div class="setup-navbar-center">
                <span class="setup-brand">{{ __('profile.title') }}</span>
            </div>
            <div class="setup-navbar-right">
                <div class="d-flex align-items-center gap-2">
                    <form method="POST"
                        action="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : (app()->getLocale() === 'fr' ? 'ar' : 'fr')) }}"
                        class="d-inline">
                        @csrf
                        <button type="submit" class="btn-lang" aria-label="{{ __('settings.language') }}">
                            <i class="bi bi-globe2"></i>
                        </button>
                    </form>
                    <button class="btn-theme" data-theme-toggle @click="toggleTheme()"
                        aria-label="{{ __('settings.theme') }}">
                        <i class="bi {{ session('theme', 'light') === 'dark' ? 'bi-sun-fill' : 'bi-moon-fill' }}"></i>
                    </button>
                </div>
            </div>
        @endif
    </div>
</nav>
