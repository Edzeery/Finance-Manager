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
                <a href="{{ route('dashboard') }}" class="btn btn-accent btn-custom mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-grid-1x2-fill me-1"></i>{{ __('general.dashboard') }}
                </a>
            @else
                <a href="{{ route('login') }}"
                    class="btn btn-outline-secondary btn-custom mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-in-right me-1"></i>{{ __('general.login') }}
                </a>
                <a href="{{ route('register') }}"
                    class="btn btn-accent btn-custom d-none d-sm-inline-flex mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-person-plus me-1"></i>{{ __('general.register') }}
                </a>
            @endauth
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="landing-hero">
        <div class="hero-content">
            <span class="hero-badge">{{ __('welcome.hero_stat_2_label') }} 10,000+</span>
            <h1>{{ __('welcome.hero_title') }}</h1>
            <p>{{ __('welcome.hero_description') }}</p>
            <div class="hero-actions">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="btn btn-accent btn-custom btn-lg mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-grid-1x2-fill me-2"></i>{{ __('general.dashboard') }}
                    </a>
                    <a href="{{ route('api.documentation') }}"
                        class="btn btn-outline-secondary btn-custom btn-lg mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-book me-2"></i>API
                    </a>
                @else
                    <a href="{{ route('register') }}"
                        class="btn btn-accent btn-custom btn-lg mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-person-plus me-2"></i>{{ __('welcome.hero_cta_started') }}
                    </a>
                    <a href="#features"
                        class="btn btn-outline-secondary btn-custom btn-lg mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-play-circle me-2"></i>{{ __('welcome.hero_cta_demo') }}
                    </a>
                @endauth
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-card hero-card-main">
                <div class="hero-card-header">
                    <div class="hero-card-dots"><span></span><span></span><span></span></div>
                </div>
                <div class="hero-card-body">
                    <div class="hc-row"><span class="hc-label">{{ __('general.monthly') }}
                            {{ __('general.income') }}</span><span class="hc-value hc-up">+$4,250</span></div>
                    <div class="hc-row"><span class="hc-label">{{ __('general.monthly') }}
                            {{ __('general.expense') }}</span><span class="hc-value hc-down">-$3,180</span></div>
                    <div class="hc-bar">
                        <div class="hc-bar-fill" style="width:75%"></div>
                    </div>
                    <div class="hc-row"><span class="hc-label">{{ __('general.budget') }}</span><span
                            class="hc-value">75%</span></div>
                </div>
            </div>
            <div class="hero-card hero-card-float hero-card-1">
                <i class="bi bi-wallet2"></i>
                <div><strong>$12,400</strong><span>{{ __('dashboard.total_balance') }}</span></div>
            </div>
            <div class="hero-card hero-card-float hero-card-2">
                <i class="bi bi-trophy"></i>
                <div><strong>87%</strong><span>{{ __('goal.title') }}</span></div>
            </div>
        </div>
    </section>

    {{-- Stats Section --}}
    <section class="landing-stats">
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number">$10M+</span>
                <span class="stat-label">{{ __('welcome.hero_stat_1_label') }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">10,000+</span>
                <span class="stat-label">{{ __('welcome.hero_stat_2_label') }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">50,000+</span>
                <span class="stat-label">{{ __('welcome.hero_stat_3_label') }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">100,000+</span>
                <span class="stat-label">{{ __('welcome.hero_stat_4_label') }}</span>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="landing-features" id="features">
        <div class="section-header">
            <span class="section-badge">{{ __('welcome.section_title') }}</span>
            <h2 class="section-title">{{ __('welcome.section_heading') }}</h2>
            <p class="section-subtitle">{{ __('welcome.section_subtitle') }}</p>
        </div>

        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon bg-success-subtle text-success">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <h3>{{ __('income.title') }}</h3>
                <p>{{ __('welcome.feature_income') }}</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon bg-danger-subtle text-danger">
                    <i class="bi bi-cart"></i>
                </div>
                <h3>{{ __('expense.title') }}</h3>
                <p>{{ __('welcome.feature_expense') }}</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon bg-warning-subtle text-warning">
                    <i class="bi bi-credit-card-2-front"></i>
                </div>
                <h3>{{ __('debt.title') }}</h3>
                <p>{{ __('welcome.feature_debt') }}</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon bg-info-subtle text-info">
                    <i class="bi bi-pie-chart-fill"></i>
                </div>
                <h3>{{ __('asset.title') }}</h3>
                <p>{{ __('welcome.feature_asset') }}</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon bg-purple-subtle text-purple">
                    <i class="bi bi-calculator-fill"></i>
                </div>
                <h3>{{ __('budget.title') }}</h3>
                <p>{{ __('welcome.feature_budget') }}</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon bg-accent-subtle text-accent">
                    <i class="bi bi-heart-fill"></i>
                </div>
                <h3>{{ __('zakat.title') }}</h3>
                <p>{{ __('welcome.feature_zakat') }}</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(99,102,241,0.1);color:#6366f1">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <h3>{{ __('general.transactions') }}</h3>
                <p>{{ __('welcome.feature_transactions') }}</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(236,72,153,0.1);color:#ec4899">
                    <i class="bi bi-bullseye"></i>
                </div>
                <h3>{{ __('goal.title') }}</h3>
                <p>{{ __('welcome.feature_goals') }}</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(34,197,94,0.1);color:#22c55e">
                    <i class="bi bi-bell-fill"></i>
                </div>
                <h3>{{ __('general.notifications') }}</h3>
                <p>{{ __('welcome.feature_notifications') }}</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(251,146,60,0.1);color:#fb923c">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                </div>
                <h3>{{ __('report.title') }}</h3>
                <p>{{ __('welcome.feature_reports') }}</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(168,85,247,0.1);color:#a855f7">
                    <i class="bi bi-receipt"></i>
                </div>
                <h3>{{ __('settings.invoices') }}</h3>
                <p>{{ __('welcome.feature_invoices') }}</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(6,182,212,0.1);color:#06b6d4">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h3>{{ __('workspace.members') }}</h3>
                <p>{{ __('welcome.feature_team') }}</p>
            </div>
        </div>
    </section>

    {{-- Workspaces / SaaS Section --}}
    <section class="landing-how" id="workspaces">
        <div class="section-header">
            <span class="section-badge">{{ __('welcome.saas_title') }}</span>
            <h2 class="section-title">{{ __('welcome.saas_heading') }}</h2>
            <p class="section-subtitle">{{ __('welcome.saas_subtitle') }}</p>
        </div>

        <div class="how-grid">
            <div class="how-step">
                <div class="how-number">1</div>
                <div class="how-icon"><i class="bi bi-building"></i></div>
                <h3>{{ __('welcome.saas_step1_title') }}</h3>
                <p>{{ __('welcome.saas_step1_desc') }}</p>
            </div>
            <div class="how-connector">
                <i class="bi bi-arrow-{{ in_array(app()->getLocale(), ['ar']) ? 'left' : 'right' }}"></i>
            </div>
            <div class="how-step">
                <div class="how-number">2</div>
                <div class="how-icon"><i class="bi bi-people-fill"></i></div>
                <h3>{{ __('welcome.saas_step2_title') }}</h3>
                <p>{{ __('welcome.saas_step2_desc') }}</p>
            </div>
            <div class="how-connector">
                <i class="bi bi-arrow-{{ in_array(app()->getLocale(), ['ar']) ? 'left' : 'right' }}"></i>
            </div>
            <div class="how-step">
                <div class="how-number">3</div>
                <div class="how-icon"><i class="bi bi-shield-lock"></i></div>
                <h3>{{ __('welcome.saas_step3_title') }}</h3>
                <p>{{ __('welcome.saas_step3_desc') }}</p>
            </div>
        </div>
    </section>

    {{-- Team Management Section --}}
    <section class="landing-testimonials">
        <div class="section-header">
            <span class="section-badge">{{ __('welcome.team_title') }}</span>
            <h2 class="section-title">{{ __('welcome.team_heading') }}</h2>
            <p class="section-subtitle">{{ __('welcome.team_subtitle') }}</p>
        </div>

        <div class="feature-grid" style="max-width:900px;margin:0 auto">
            <div class="feature-card" style="text-align:center">
                <div class="feature-icon" style="background:rgba(99,102,241,0.1);color:#6366f1;margin:0 auto 16px">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h3>{{ __('workspace.invite_member') }}</h3>
                <p>{{ __('welcome.team_feature_invite') }}</p>
            </div>
            <div class="feature-card" style="text-align:center">
                <div class="feature-icon" style="background:rgba(34,197,94,0.1);color:#22c55e;margin:0 auto 16px">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h3>{{ __('general.roles') }}</h3>
                <p>{{ __('welcome.team_feature_roles') }}</p>
            </div>
            <div class="feature-card" style="text-align:center">
                <div class="feature-icon" style="background:rgba(251,146,60,0.1);color:#fb923c;margin:0 auto 16px">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <h3>{{ __('workspace.transfer_ownership') }}</h3>
                <p>{{ __('welcome.team_feature_transfer') }}</p>
            </div>
        </div>
    </section>

    {{-- API Section --}}
    <section class="landing-how" style="padding:60px 0" id="api">
        <div class="section-header">
            <span class="section-badge">API</span>
            <h2 class="section-title">{{ __('welcome.api_heading') }}</h2>
            <p class="section-subtitle">{{ __('welcome.api_subtitle') }}</p>
        </div>
        <div style="text-align:center;max-width:600px;margin:0 auto">
            <div style="font-size:48px;margin-bottom:24px;color:var(--accent)">⚡</div>
            <p style="font-size:16px;color:var(--text-muted);margin-bottom:24px">{{ __('welcome.api_description') }}
            </p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('api.documentation') }}"
                    class="btn btn-accent btn-custom d-inline-flex align-items-center gap-2">
                    <i class="bi bi-book me-1"></i>{{ __('developer.api_documentation_link') }}
                </a>
                @auth
                    <a href="{{ route('account.settings.developer') }}"
                        class="btn btn-outline-secondary btn-custom d-inline-flex align-items-center gap-2">
                        <i class="bi bi-key me-1"></i>{{ __('developer.api_tokens') }}
                    </a>
                @endauth
            </div>
        </div>
    </section>

    {{-- Pricing Section --}}
    <section class="landing-pricing" id="pricing">
        <div class="section-header">
            <span class="section-badge">{{ __('welcome.pricing_title') }}</span>
            <h2 class="section-title">{{ __('welcome.pricing_heading') }}</h2>
            <p class="section-subtitle">{{ __('welcome.pricing_subtitle') }}</p>
        </div>

        <div class="pricing-grid">
            @foreach ($plans as $plan)
                <div class="pricing-card @if ($plan->slug === 'business') pricing-featured @endif">
                    @if ($plan->slug === 'business')
                        <div class="pricing-badge">{{ __('general.popular') }}</div>
                    @endif
                    <h3>{{ $plan->name }}</h3>
                    <p class="pricing-desc">
                        {{ $plan->description  ??  __('subscription.' . $plan->slug . '_plan_description') }}</p>
                    <div class="pricing-amount">
                        <span class="pricing-price">
                            @if ($plan->is_free)
                                {{ __('welcome.pricing_free') }}
                            @elseif($plan->monthly_price > 0)
                                {{ $displayLandingPrice($plan->monthly_price) }}
                            @else
                                {{ __('welcome.pricing_custom') }}
                            @endif
                        </span>
                        @if ($plan->monthly_price > 0)
                            <span class="pricing-period">/{{ __('general.month') }}</span>
                        @endif
                    </div>
                    <ul class="pricing-features" x-data="{ showAll: false }">
                        @php $allFeatures = $plan->planFeatures; @endphp
                        @if ($allFeatures->isNotEmpty())
                            @foreach ($allFeatures as $index => $feature)
                                <li x-show="showAll || {{ $index < 3 ? 'true' : 'false' }}"
                                    x-transition:enter.duration.200ms>
                                    <i
                                        class="bi bi-check-lg me-1"></i>{{ $landingFeatureName($feature) }}{{ $feature->pivot->value ? ': ' . $feature->pivot->value : '' }}
                                </li>
                            @endforeach
                        @endif
                        @if ($allFeatures->count() > 3)
                            <li>
                                <button @click="showAll = !showAll" type="button" class="btn btn-link p-0"
                                    style="font-size:13px;color:var(--accent);text-decoration:none">
                                    <span x-show="!showAll">{{ __('general.show_more') }}
                                        ({{ $allFeatures->count() - 3 }})</span>
                                    <span x-show="showAll">{{ __('general.show_less') }}</span>
                                </button>
                            </li>
                        @endif
                    </ul>
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="btn btn-accent btn-custom w-100">{{ __('general.dashboard') }}</a>
                    @else
                        <a href="{{ route('register') }}"
                            class="btn btn-accent btn-custom w-100">{{ $plan->button_text ?? __('welcome.pricing_cta') }}</a>
                    @endauth
                </div>
            @endforeach
        </div>
    </section>

    {{-- FAQ Section --}}
    <section class="landing-faq" id="faq">
        <div class="section-header">
            <span class="section-badge">{{ __('welcome.faq_title') }}</span>
            <h2 class="section-title">{{ __('welcome.faq_heading') }}</h2>
            <p class="section-subtitle">{{ __('welcome.faq_subtitle') }}</p>
        </div>

        <div class="faq-list">
            <div class="faq-item">
                <button class="faq-question">
                    <span>{{ __('welcome.faq_q1') }}</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="faq-answer">{{ __('welcome.faq_a1') }}</div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    <span>{{ __('welcome.faq_q2') }}</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="faq-answer">{{ __('welcome.faq_a2') }}</div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    <span>{{ __('welcome.faq_q3') }}</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="faq-answer">{{ __('welcome.faq_a3') }}</div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    <span>{{ __('welcome.faq_q4') }}</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="faq-answer">{{ __('welcome.faq_a4') }}</div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    <span>{{ __('welcome.faq_q5') }}</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="faq-answer">{{ __('welcome.faq_a5') }}</div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    <span>{{ __('welcome.faq_q6') }}</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="faq-answer">{{ __('welcome.faq_a6') }}</div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="landing-cta">
        <div class="cta-card">
            <h2>{{ __('welcome.cta_title') }}</h2>
            <p>{{ __('welcome.cta_description') }}</p>
            @auth
                <a href="{{ route('dashboard') }}"
                    class="btn btn-accent btn-custom btn-lg d-flex align-items-center gap-2 mx-auto" style="width:220px">
                    <i class="bi bi-grid-1x2-fill me-2"></i>{{ __('general.dashboard') }}
                </a>
            @else
                <a href="{{ route('register') }}"
                    class="btn btn-accent btn-custom btn-lg d-flex align-items-center gap-2 mx-auto" style="width:220px">
                    <i class="bi bi-person-plus me-2"></i>{{ __('welcome.hero_cta_started') }}
                </a>
            @endauth
        </div>
    </section>

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
