
<?php
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
?>

<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>"
    dir="<?php echo e(in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr'); ?>" data-theme="<?php echo e(session('theme', 'light')); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="theme-switch-url" content="<?php echo e(route('theme.switch')); ?>">
    <title><?php echo e(config('app.name', 'Finance Manager')); ?> — <?php echo e(__('welcome.hero_title')); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap"
        rel="stylesheet" media="print" onload="this.media='all'">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>

<body class="landing-page" x-data="">
    <nav class="landing-nav">
        <a href="/" class="nav-logo">
            <div class="logo-icon">FM</div>
            <span class="logo-text"><?php echo e(config('app.name')); ?></span>
        </a>

        <div class="nav-links">
            <a href="#features" class="nav-link"><?php echo e(__('welcome.section_title')); ?></a>
            <a href="#pricing" class="nav-link"><?php echo e(__('welcome.pricing_title')); ?></a>
            <a href="#workspaces" class="nav-link"><?php echo e(__('workspace.manage')); ?></a>
            <a href="#api" class="nav-link">API</a>
            <a href="#faq" class="nav-link"><?php echo e(__('welcome.faq_title')); ?></a>

            <?php if (isset($component)) { $__componentOriginal8d3bff7d7383a45350f7495fc470d934 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d3bff7d7383a45350f7495fc470d934 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.language-switcher','data' => ['variant' => 'dropdown-bs','triggerClass' => 'topbar-btn dropdown-toggle']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('language-switcher'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'dropdown-bs','triggerClass' => 'topbar-btn dropdown-toggle']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d3bff7d7383a45350f7495fc470d934)): ?>
<?php $attributes = $__attributesOriginal8d3bff7d7383a45350f7495fc470d934; ?>
<?php unset($__attributesOriginal8d3bff7d7383a45350f7495fc470d934); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d3bff7d7383a45350f7495fc470d934)): ?>
<?php $component = $__componentOriginal8d3bff7d7383a45350f7495fc470d934; ?>
<?php unset($__componentOriginal8d3bff7d7383a45350f7495fc470d934); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalca58501fa868702c8dca665d81ebadbe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalca58501fa868702c8dca665d81ebadbe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency-switcher','data' => ['variant' => 'dropdown-bs','triggerClass' => 'topbar-btn']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency-switcher'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'dropdown-bs','triggerClass' => 'topbar-btn']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalca58501fa868702c8dca665d81ebadbe)): ?>
<?php $attributes = $__attributesOriginalca58501fa868702c8dca665d81ebadbe; ?>
<?php unset($__attributesOriginalca58501fa868702c8dca665d81ebadbe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalca58501fa868702c8dca665d81ebadbe)): ?>
<?php $component = $__componentOriginalca58501fa868702c8dca665d81ebadbe; ?>
<?php unset($__componentOriginalca58501fa868702c8dca665d81ebadbe); ?>
<?php endif; ?>

            <button class="topbar-btn" data-theme-toggle onclick="toggleTheme()" type="button">
                <i class="bi <?php echo e(session('theme', 'light') === 'dark' ? 'bi-sun-fill' : 'bi-moon-fill'); ?>"></i>
            </button>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>

                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'accent','href' => route('dashboard'),'icon' => 'bi bi-grid-1x2-fill','iconPosition' => 'left']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'accent','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('dashboard')),'icon' => 'bi bi-grid-1x2-fill','icon-position' => 'left']); ?><?php echo e(__('general.dashboard')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            <?php else: ?>
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'outline','href' => route('login'),'icon' => 'bi bi-box-arrow-in-right','iconPosition' => 'left']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('login')),'icon' => 'bi bi-box-arrow-in-right','icon-position' => 'left']); ?><?php echo e(__('general.login')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'accent','href' => route('register'),'icon' => 'bi bi-person-plus','iconPosition' => 'left']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'accent','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('register')),'icon' => 'bi bi-person-plus','icon-position' => 'left']); ?><?php echo e(__('general.register')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </nav>

    
    <section class="landing-hero">
        <div class="hero-content">
            <span class="hero-badge"><?php echo e(__('welcome.hero_stat_2_label')); ?> 10,000+</span>
            <h1><?php echo e(__('welcome.hero_title')); ?></h1>
            <p><?php echo e(__('welcome.hero_description')); ?></p>
            <div class="hero-actions">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'accent','href' => route('dashboard'),'icon' => 'bi bi-grid-1x2-fill','iconPosition' => 'left']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'accent','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('dashboard')),'icon' => 'bi bi-grid-1x2-fill','icon-position' => 'left']); ?><?php echo e(__('general.dashboard')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                    <a href="<?php echo e(route('api.documentation')); ?>"
                        class="btn btn-outline-secondary btn-custom btn-lg mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-book me-2"></i>API
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('register')); ?>"
                        class="btn btn-accent btn-custom btn-lg mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-person-plus me-2"></i><?php echo e(__('welcome.hero_cta_started')); ?>

                    </a>
                    <a href="#features"
                        class="btn btn-outline-secondary btn-custom btn-lg mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-play-circle me-2"></i><?php echo e(__('welcome.hero_cta_demo')); ?>

                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-card hero-card-main">
                <div class="hero-card-header">
                    <div class="hero-card-dots"><span></span><span></span><span></span></div>
                </div>
                <div class="hero-card-body">
                    <div class="hc-row"><span class="hc-label"><?php echo e(__('general.monthly')); ?>

                            <?php echo e(__('general.income')); ?></span><span class="hc-value hc-up">+$4,250</span></div>
                    <div class="hc-row"><span class="hc-label"><?php echo e(__('general.monthly')); ?>

                            <?php echo e(__('general.expense')); ?></span><span class="hc-value hc-down">-$3,180</span></div>
                    <div class="hc-bar">
                        <div class="hc-bar-fill" style="width:75%"></div>
                    </div>
                    <div class="hc-row"><span class="hc-label"><?php echo e(__('general.budget')); ?></span><span
                            class="hc-value">75%</span></div>
                </div>
            </div>
            <div class="hero-card hero-card-float hero-card-1">
                <i class="bi bi-wallet2"></i>
                <div><strong>$12,400</strong><span><?php echo e(__('dashboard.total_balance')); ?></span></div>
            </div>
            <div class="hero-card hero-card-float hero-card-2">
                <i class="bi bi-trophy"></i>
                <div><strong>87%</strong><span><?php echo e(__('goal.title')); ?></span></div>
            </div>
        </div>
    </section>

    
    <section class="landing-stats">
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number">$10M+</span>
                <span class="stat-label"><?php echo e(__('welcome.hero_stat_1_label')); ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-number">10,000+</span>
                <span class="stat-label"><?php echo e(__('welcome.hero_stat_2_label')); ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-number">50,000+</span>
                <span class="stat-label"><?php echo e(__('welcome.hero_stat_3_label')); ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-number">100,000+</span>
                <span class="stat-label"><?php echo e(__('welcome.hero_stat_4_label')); ?></span>
            </div>
        </div>
    </section>

    
    <section class="landing-features" id="features">
        <div class="section-header">
            <span class="section-badge"><?php echo e(__('welcome.section_title')); ?></span>
            <h2 class="section-title"><?php echo e(__('welcome.section_heading')); ?></h2>
            <p class="section-subtitle"><?php echo e(__('welcome.section_subtitle')); ?></p>
        </div>

        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon bg-success-subtle text-success">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <h3><?php echo e(__('income.title')); ?></h3>
                <p><?php echo e(__('welcome.feature_income')); ?></p>
            </div>

            <div class="feature-card">
                <div class="feature-icon bg-danger-subtle text-danger">
                    <i class="bi bi-cart"></i>
                </div>
                <h3><?php echo e(__('expense.title')); ?></h3>
                <p><?php echo e(__('welcome.feature_expense')); ?></p>
            </div>

            <div class="feature-card">
                <div class="feature-icon bg-warning-subtle text-warning">
                    <i class="bi bi-credit-card-2-front"></i>
                </div>
                <h3><?php echo e(__('debt.title')); ?></h3>
                <p><?php echo e(__('welcome.feature_debt')); ?></p>
            </div>

            <div class="feature-card">
                <div class="feature-icon bg-info-subtle text-info">
                    <i class="bi bi-pie-chart-fill"></i>
                </div>
                <h3><?php echo e(__('asset.title')); ?></h3>
                <p><?php echo e(__('welcome.feature_asset')); ?></p>
            </div>

            <div class="feature-card">
                <div class="feature-icon bg-purple-subtle text-purple">
                    <i class="bi bi-calculator-fill"></i>
                </div>
                <h3><?php echo e(__('budget.title')); ?></h3>
                <p><?php echo e(__('welcome.feature_budget')); ?></p>
            </div>

            <div class="feature-card">
                <div class="feature-icon bg-accent-subtle text-accent">
                    <i class="bi bi-heart-fill"></i>
                </div>
                <h3><?php echo e(__('zakat.title')); ?></h3>
                <p><?php echo e(__('welcome.feature_zakat')); ?></p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(99,102,241,0.1);color:#6366f1">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <h3><?php echo e(__('general.transactions')); ?></h3>
                <p><?php echo e(__('welcome.feature_transactions')); ?></p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(236,72,153,0.1);color:#ec4899">
                    <i class="bi bi-bullseye"></i>
                </div>
                <h3><?php echo e(__('goal.title')); ?></h3>
                <p><?php echo e(__('welcome.feature_goals')); ?></p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(34,197,94,0.1);color:#22c55e">
                    <i class="bi bi-bell-fill"></i>
                </div>
                <h3><?php echo e(__('general.notifications')); ?></h3>
                <p><?php echo e(__('welcome.feature_notifications')); ?></p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(251,146,60,0.1);color:#fb923c">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                </div>
                <h3><?php echo e(__('report.title')); ?></h3>
                <p><?php echo e(__('welcome.feature_reports')); ?></p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(168,85,247,0.1);color:#a855f7">
                    <i class="bi bi-receipt"></i>
                </div>
                <h3><?php echo e(__('settings.invoices')); ?></h3>
                <p><?php echo e(__('welcome.feature_invoices')); ?></p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(6,182,212,0.1);color:#06b6d4">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h3><?php echo e(__('workspace.members')); ?></h3>
                <p><?php echo e(__('welcome.feature_team')); ?></p>
            </div>
        </div>
    </section>

    
    <section class="landing-how" id="workspaces">
        <div class="section-header">
            <span class="section-badge"><?php echo e(__('welcome.saas_title')); ?></span>
            <h2 class="section-title"><?php echo e(__('welcome.saas_heading')); ?></h2>
            <p class="section-subtitle"><?php echo e(__('welcome.saas_subtitle')); ?></p>
        </div>

        <div class="how-grid">
            <div class="how-step">
                <div class="how-number">1</div>
                <div class="how-icon"><i class="bi bi-building"></i></div>
                <h3><?php echo e(__('welcome.saas_step1_title')); ?></h3>
                <p><?php echo e(__('welcome.saas_step1_desc')); ?></p>
            </div>
            <div class="how-connector">
                <i class="bi bi-arrow-<?php echo e(in_array(app()->getLocale(), ['ar']) ? 'left' : 'right'); ?>"></i>
            </div>
            <div class="how-step">
                <div class="how-number">2</div>
                <div class="how-icon"><i class="bi bi-people-fill"></i></div>
                <h3><?php echo e(__('welcome.saas_step2_title')); ?></h3>
                <p><?php echo e(__('welcome.saas_step2_desc')); ?></p>
            </div>
            <div class="how-connector">
                <i class="bi bi-arrow-<?php echo e(in_array(app()->getLocale(), ['ar']) ? 'left' : 'right'); ?>"></i>
            </div>
            <div class="how-step">
                <div class="how-number">3</div>
                <div class="how-icon"><i class="bi bi-shield-lock"></i></div>
                <h3><?php echo e(__('welcome.saas_step3_title')); ?></h3>
                <p><?php echo e(__('welcome.saas_step3_desc')); ?></p>
            </div>
        </div>
    </section>

    
    <section class="landing-testimonials">
        <div class="section-header">
            <span class="section-badge"><?php echo e(__('welcome.team_title')); ?></span>
            <h2 class="section-title"><?php echo e(__('welcome.team_heading')); ?></h2>
            <p class="section-subtitle"><?php echo e(__('welcome.team_subtitle')); ?></p>
        </div>

        <div class="feature-grid" style="max-width:900px;margin:0 auto">
            <div class="feature-card" style="text-align:center">
                <div class="feature-icon" style="background:rgba(99,102,241,0.1);color:#6366f1;margin:0 auto 16px">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h3><?php echo e(__('workspace.invite_member')); ?></h3>
                <p><?php echo e(__('welcome.team_feature_invite')); ?></p>
            </div>
            <div class="feature-card" style="text-align:center">
                <div class="feature-icon" style="background:rgba(34,197,94,0.1);color:#22c55e;margin:0 auto 16px">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h3><?php echo e(__('general.roles')); ?></h3>
                <p><?php echo e(__('welcome.team_feature_roles')); ?></p>
            </div>
            <div class="feature-card" style="text-align:center">
                <div class="feature-icon" style="background:rgba(251,146,60,0.1);color:#fb923c;margin:0 auto 16px">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <h3><?php echo e(__('workspace.transfer_ownership')); ?></h3>
                <p><?php echo e(__('welcome.team_feature_transfer')); ?></p>
            </div>
        </div>
    </section>

    
    <section class="landing-how" style="padding:60px 0" id="api">
        <div class="section-header">
            <span class="section-badge">API</span>
            <h2 class="section-title"><?php echo e(__('welcome.api_heading')); ?></h2>
            <p class="section-subtitle"><?php echo e(__('welcome.api_subtitle')); ?></p>
        </div>
        <div style="text-align:center;max-width:600px;margin:0 auto">
            <div style="font-size:48px;margin-bottom:24px;color:var(--accent)">⚡</div>
            <p style="font-size:16px;color:var(--text-muted);margin-bottom:24px"><?php echo e(__('welcome.api_description')); ?>

            </p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="<?php echo e(route('api.documentation')); ?>"
                    class="btn btn-accent btn-custom d-inline-flex align-items-center gap-2">
                    <i class="bi bi-book me-1"></i><?php echo e(__('developer.api_documentation_link')); ?>

                </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('account.settings.developer')); ?>"
                        class="btn btn-outline-secondary btn-custom d-inline-flex align-items-center gap-2">
                        <i class="bi bi-key me-1"></i><?php echo e(__('developer.api_tokens')); ?>

                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    
    <section class="landing-pricing" id="pricing">
        <div class="section-header">
            <span class="section-badge"><?php echo e(__('welcome.pricing_title')); ?></span>
            <h2 class="section-title"><?php echo e(__('welcome.pricing_heading')); ?></h2>
            <p class="section-subtitle"><?php echo e(__('welcome.pricing_subtitle')); ?></p>
        </div>

        <div class="pricing-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="pricing-card <?php if($plan->slug === 'business'): ?> pricing-featured <?php endif; ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->slug === 'business'): ?>
                        <div class="pricing-badge"><?php echo e(__('general.popular')); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <h3><?php echo e($plan->name); ?></h3>
                    <p class="pricing-desc">
                        <?php echo e($plan->description ?? __('subscription.' . $plan->slug . '_plan_description')); ?></p>
                    <div class="pricing-amount">
                        <span class="pricing-price">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->is_free): ?>
                                <?php echo e(__('welcome.pricing_free')); ?>

                            <?php elseif($plan->monthly_price > 0): ?>
                                <?php echo e($displayLandingPrice($plan->monthly_price)); ?>

                            <?php else: ?>
                                <?php echo e(__('welcome.pricing_custom')); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->monthly_price > 0): ?>
                            <span class="pricing-period">/<?php echo e(__('general.month')); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <ul class="pricing-features" x-data="{ showAll: false }">
                        <?php $allFeatures = $plan->planFeatures; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allFeatures->isNotEmpty()): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allFeatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li x-show="showAll || <?php echo e($index < 3 ? 'true' : 'false'); ?>"
                                    x-transition:enter.duration.200ms>
                                    <i
                                        class="bi bi-check-lg me-1"></i><?php echo e($landingFeatureName($feature)); ?><?php echo e($feature->pivot->value ? ': ' . $feature->pivot->value : ''); ?>

                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allFeatures->count() > 3): ?>
                            <li>
                                <button @click="showAll = !showAll" type="button" class="btn btn-link p-0"
                                    style="font-size:13px;color:var(--accent);text-decoration:none">
                                    <span x-show="!showAll"><?php echo e(__('general.show_more')); ?>

                                        (<?php echo e($allFeatures->count() - 3); ?>)</span>
                                    <span x-show="showAll"><?php echo e(__('general.show_less')); ?></span>
                                </button>
                            </li>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('dashboard')); ?>"
                            class="btn btn-accent btn-custom w-100"><?php echo e(__('general.dashboard')); ?></a>
                    <?php else: ?>
                        <a href="<?php echo e(route('register')); ?>"
                            class="btn btn-accent btn-custom w-100"><?php echo e($plan->button_text ?? __('welcome.pricing_cta')); ?></a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    
    <section class="landing-faq" id="faq">
        <div class="section-header">
            <span class="section-badge"><?php echo e(__('welcome.faq_title')); ?></span>
            <h2 class="section-title"><?php echo e(__('welcome.faq_heading')); ?></h2>
            <p class="section-subtitle"><?php echo e(__('welcome.faq_subtitle')); ?></p>
        </div>

        <div class="faq-list">
            <div class="faq-item">
                <button class="faq-question">
                    <span><?php echo e(__('welcome.faq_q1')); ?></span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="faq-answer"><?php echo e(__('welcome.faq_a1')); ?></div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    <span><?php echo e(__('welcome.faq_q2')); ?></span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="faq-answer"><?php echo e(__('welcome.faq_a2')); ?></div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    <span><?php echo e(__('welcome.faq_q3')); ?></span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="faq-answer"><?php echo e(__('welcome.faq_a3')); ?></div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    <span><?php echo e(__('welcome.faq_q4')); ?></span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="faq-answer"><?php echo e(__('welcome.faq_a4')); ?></div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    <span><?php echo e(__('welcome.faq_q5')); ?></span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="faq-answer"><?php echo e(__('welcome.faq_a5')); ?></div>
            </div>
            <div class="faq-item">
                <button class="faq-question">
                    <span><?php echo e(__('welcome.faq_q6')); ?></span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="faq-answer"><?php echo e(__('welcome.faq_a6')); ?></div>
            </div>
        </div>
    </section>

    
    <section class="landing-cta">
        <div class="cta-card">
            <h2><?php echo e(__('welcome.cta_title')); ?></h2>
            <p><?php echo e(__('welcome.cta_description')); ?></p>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>"
                    class="btn btn-accent btn-custom btn-lg d-flex align-items-center gap-2 mx-auto" style="width:220px">
                    <i class="bi bi-grid-1x2-fill me-2"></i><?php echo e(__('general.dashboard')); ?>

                </a>
            <?php else: ?>
                <a href="<?php echo e(route('register')); ?>"
                    class="btn btn-accent btn-custom btn-lg d-flex align-items-center gap-2 mx-auto" style="width:220px">
                    <i class="bi bi-person-plus me-2"></i><?php echo e(__('welcome.hero_cta_started')); ?>

                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    
    <footer class="landing-footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <div class="footer-logo">
                    <div class="logo-icon">FM</div>
                    <span class="logo-text"><?php echo e(config('app.name')); ?></span>
                </div>
                <p><?php echo e(__('welcome.hero_description')); ?></p>
            </div>
            <div class="footer-col">
                <h4><?php echo e(__('welcome.footer_product')); ?></h4>
                <a href="#features"><?php echo e(__('income.title')); ?></a>
                <a href="#features"><?php echo e(__('expense.title')); ?></a>
                <a href="#features"><?php echo e(__('debt.title')); ?></a>
                <a href="#features"><?php echo e(__('asset.title')); ?></a>
                <a href="#features"><?php echo e(__('budget.title')); ?></a>
            </div>
            <div class="footer-col">
                <h4><?php echo e(__('welcome.footer_company')); ?></h4>
                <a href="#"><?php echo e(__('welcome.footer_about')); ?></a>
                <a href="#"><?php echo e(__('welcome.footer_blog')); ?></a>
                <a href="#"><?php echo e(__('welcome.footer_contact')); ?></a>
                <a href="#faq"><?php echo e(__('welcome.footer_faq')); ?></a>
            </div>
            <div class="footer-col">
                <h4><?php echo e(__('welcome.footer_support')); ?></h4>
                <a href="<?php echo e(route('api.documentation')); ?>"><?php echo e(__('welcome.footer_documentation')); ?></a>
                <a href="<?php echo e(route('api.documentation')); ?>"><?php echo e(__('welcome.footer_api')); ?></a>
                <a href="#"><?php echo e(__('welcome.footer_privacy')); ?></a>
                <a href="#"><?php echo e(__('welcome.footer_terms')); ?></a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. <?php echo e(__('welcome.all_rights')); ?></span>
            <div class="footer-lang">
                <?php if (isset($component)) { $__componentOriginal8d3bff7d7383a45350f7495fc470d934 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d3bff7d7383a45350f7495fc470d934 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.language-switcher','data' => ['variant' => 'inline','itemClass' => 'footer-lang-btn']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('language-switcher'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'inline','itemClass' => 'footer-lang-btn']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d3bff7d7383a45350f7495fc470d934)): ?>
<?php $attributes = $__attributesOriginal8d3bff7d7383a45350f7495fc470d934; ?>
<?php unset($__attributesOriginal8d3bff7d7383a45350f7495fc470d934); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d3bff7d7383a45350f7495fc470d934)): ?>
<?php $component = $__componentOriginal8d3bff7d7383a45350f7495fc470d934; ?>
<?php unset($__componentOriginal8d3bff7d7383a45350f7495fc470d934); ?>
<?php endif; ?>
            </div>
        </div>
    </footer>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
    <script type="module" src="https://esm.sh/ionicons@latest/loader"></script>
    <script nomodule src="https://esm.sh/ionicons@latest/loader"></script>
</body>

</html>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/landing.blade.php ENDPATH**/ ?>