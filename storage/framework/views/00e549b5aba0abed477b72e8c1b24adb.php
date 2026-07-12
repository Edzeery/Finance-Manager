<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'guest',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'variant' => 'guest',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $isGuest = $variant === 'guest';
    $isSetup = $variant === 'setup';
    $isProfile = $variant === 'profile';
?>

<nav class="<?php echo e($isGuest ? 'guest-navbar' : 'setup-navbar'); ?>">
    <div class="<?php echo e($isGuest ? 'guest-navbar-inner' : 'setup-navbar-inner'); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isGuest): ?>
            <a href="/" class="guest-navbar-brand">
                <div class="logo-icon" style="width:28px;height:28px;font-size:11px">FM</div>
                <span class="logo-text" style="font-size:15px"><?php echo e(config('app.name')); ?></span>
            </a>
            <div class="guest-navbar-controls">
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((Route::has('onboarding.plan') && Route::has('onboarding.payment'))  || Route::has(!'verification.notice') ): ?>
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
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


                <button class="topbar-btn" @click="toggleTheme()" type="button">
                    <i class="bi <?php echo e(session('theme', 'light') === 'dark' ? 'bi-sun-fill' : 'bi-moon-fill'); ?>"></i>
                </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>

                    <a href="<?php echo e(route('dashboard')); ?>"
                        class="btn btn-accent btn-custom mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-grid-1x2-fill me-1"></i><?php echo e(__('general.dashboard')); ?>

                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php elseif($isSetup): ?>
            <div class="setup-navbar-left">
                <a href="<?php echo e(app(\App\Services\RedirectService::class)->getHomeUrl(auth()->user())); ?>" class="btn-back">
                    <i class="bi bi-arrow-left"></i>
                    <span><?php echo e(__('general.back_to_dashboard')); ?></span>
                </a>
            </div>
            <div class="setup-navbar-center">
                <span class="setup-brand"><?php echo e(config('app.name')); ?></span>
            </div>
            <div class="setup-navbar-right"></div>
        <?php elseif($isProfile): ?>
            <div class="setup-navbar-left">
                <a href="<?php echo e(app(\App\Services\RedirectService::class)->getHomeUrl(auth()->user())); ?>" class="btn-back">
                    <i class="bi bi-arrow-left"></i>
                    <span><?php echo e(__('general.back_to_dashboard')); ?></span>
                </a>
            </div>
            <div class="setup-navbar-center">
                <span class="setup-brand"><?php echo e(__('profile.title')); ?></span>
            </div>
            <div class="setup-navbar-right">
                <div class="d-flex align-items-center gap-2">
                    <form method="POST"
                        action="<?php echo e(route('locale.switch', app()->getLocale() === 'ar' ? 'en' : (app()->getLocale() === 'fr' ? 'ar' : 'fr'))); ?>"
                        class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn-lang" aria-label="<?php echo e(__('settings.language')); ?>">
                            <i class="bi bi-globe2"></i>
                        </button>
                    </form>
                    <button class="btn-theme" data-theme-toggle @click="toggleTheme()"
                        aria-label="<?php echo e(__('settings.theme')); ?>">
                        <i class="bi <?php echo e(session('theme', 'light') === 'dark' ? 'bi-sun-fill' : 'bi-moon-fill'); ?>"></i>
                    </button>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</nav>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/components/navbar.blade.php ENDPATH**/ ?>