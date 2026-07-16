{{-- resources\views\landing.blade.php --}}
@php
    $landingCurrency = session(
        'currency',
        auth()->check() ? auth()->user()->currency : config('finance.currency', 'USD'),
    );
    $displayLandingPrice = function (float $usdAmount) use ($landingCurrency) {
        if ($usdAmount <= 0) {
            return __('welcome.pricing_free');
        }
        $converted = \App\Services\CurrencyHelper::fromUsd($usdAmount, $landingCurrency);
        return number_format($converted, 2) . ' ' . \App\Services\CurrencyHelper::symbol($landingCurrency);
    };
    $landingFeatureName = function ($feature) {
        return $feature->{'name_' . app()->getLocale()} ?? $feature->name_en;
    };
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}" data-theme="{{ session('theme', 'light') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-switch-url" content="{{ route('theme.switch') }}">
    <title>{{ config('app.name', 'Finance Manager') }} — {{ __('welcome.hero_title') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap"
        rel="stylesheet" media="print" onload="this.media='all'">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="landing-page" x-data="">
    <nav class="landing-nav">
        <a href="/" class="nav-logo">
            <div class="logo-icon">FM</div>
            <span class="logo-text">{{ config('app.name') }}</span>
        </a>

        <div class="nav-links">
            <a href="#features" class="nav-link">{{ __('welcome.section_title') }}</a>
            <a href="#pricing" class="nav-link">{{ __('welcome.pricing_title') }}</a>
            <a href="#workspaces" class="nav-link">{{ __('workspace.manage') }}</a>
            <a href="#api" class="nav-link">API</a>
            <a href="#faq" class="nav-link">{{ __('welcome.faq_title') }}</a>

            <x-language-switcher variant="dropdown-bs" triggerClass="topbar-btn dropdown-toggle" />
            <x-currency-switcher variant="dropdown-bs" triggerClass="topbar-btn" />

            <button class="topbar-btn" data-theme-toggle onclick="toggleTheme()" type="button">
                <i class="bi {{ session('theme', 'light') === 'dark' ? 'bi-sun-fill' : 'bi-moon-fill' }}"></i>
            </button>

            @auth

                <x-button variant="accent" :href="route('dashboard')" icon="bi bi-grid-1x2-fill"
                    icon-position="left">{{ __('general.dashboard') }}</x-button>
            @else
                <x-button variant="outline" :href="route('login')" icon="bi bi-box-arrow-in-right"
                    icon-position="left">{{ __('general.login') }}</x-button>

                <x-button variant="accent" :href="route('register')" icon="bi bi-person-plus"
                    icon-position="left">{{ __('general.register') }}</x-button>

            @endauth
        </div>
    </nav>
    {{-- TEST: mystatuskit --}}
    <div class="container my-5 p-3 border">
        <h5>اختبار مكتبة mystatuskit</h5>
        <p>
            Color: <?php echo \Edzeery\MyStatusKit\Facades\Status::for('payment', 'paid')->color(); ?>
        </p>
        <p>
            Label: {{ \Edzeery\MyStatusKit\Facades\Status::for('payment', 'paid')->label() }}
        </p>
        <p>
            Icon (Bootstrap Icons): {!! \Edzeery\MyStatusKit\Facades\Status::for('payment', 'paid')->icon('bi') !!}
        </p>
        <p>
            Badge كامل: {!! \Edzeery\MyStatusKit\Facades\Status::for('payment', 'paid')->badge('bi') !!}
        </p>
        <p>
            <x-status-badge domain="payment" status="paid" set="fa" />
        </p>
        <p>
            <x-status-badge domain="user" status="banned" set="bi" class="text-lg " />
        </p>
        <p>
            <x-status-badge domain="general" status="featured" set="fa" />
        </p>
    </div>
    {{-- Footer --}}
    <footer class="landing-footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <div class="footer-logo">
                    <div class="logo-icon">FM</div>
                    <span class="logo-text">{{ config('app.name') }}</span>
                </div>
                <p>{{ __('welcome.hero_description') }}</p>
            </div>
            <div class="footer-col">
                <h4>{{ __('welcome.footer_product') }}</h4>
                <a href="#features">{{ __('income.title') }}</a>
                <a href="#features">{{ __('expense.title') }}</a>
                <a href="#features">{{ __('debt.title') }}</a>
                <a href="#features">{{ __('asset.title') }}</a>
                <a href="#features">{{ __('budget.title') }}</a>
            </div>
            <div class="footer-col">
                <h4>{{ __('welcome.footer_company') }}</h4>
                <a href="#">{{ __('welcome.footer_about') }}</a>
                <a href="#">{{ __('welcome.footer_blog') }}</a>
                <a href="#">{{ __('welcome.footer_contact') }}</a>
                <a href="#faq">{{ __('welcome.footer_faq') }}</a>
            </div>
            <div class="footer-col">
                <h4>{{ __('welcome.footer_support') }}</h4>
                <a href="{{ route('api.documentation') }}">{{ __('welcome.footer_documentation') }}</a>
                <a href="{{ route('api.documentation') }}">{{ __('welcome.footer_api') }}</a>
                <a href="#">{{ __('welcome.footer_privacy') }}</a>
                <a href="#">{{ __('welcome.footer_terms') }}</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('welcome.all_rights') }}</span>
            <div class="footer-lang">
                <x-language-switcher variant="inline" itemClass="footer-lang-btn" />
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>

</html>
