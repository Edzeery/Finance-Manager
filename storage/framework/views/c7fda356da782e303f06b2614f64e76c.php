
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
    
    <div class="container my-5 p-3 border">
        <h5>اختبار مكتبة mystatuskit</h5>
        <p>
            Color: <?php echo \Edzeery\MyStatusKit\Facades\Status::for('payment', 'paid')->color(); ?>
        </p>
        <p>
            Label: <?php echo e(\Edzeery\MyStatusKit\Facades\Status::for('payment', 'paid')->label()); ?>

        </p>
        <p>
            Icon (Bootstrap Icons): <?php echo \Edzeery\MyStatusKit\Facades\Status::for('payment', 'paid')->icon('bi'); ?>

        </p>
        <p>
            Badge كامل: <?php echo \Edzeery\MyStatusKit\Facades\Status::for('payment', 'paid')->badge('bi'); ?>

        </p>
        <p>
            <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'payment','status' => 'paid','set' => 'fa']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'payment','status' => 'paid','set' => 'fa']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
        </p>
        <p>
            <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'user','status' => 'banned','set' => 'bi','class' => 'text-lg ']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'user','status' => 'banned','set' => 'bi','class' => 'text-lg ']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
        </p>
    </div>
    
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
</body>

</html>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/mystatuskit.blade.php ENDPATH**/ ?>