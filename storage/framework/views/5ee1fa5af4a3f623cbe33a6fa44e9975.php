<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>"
    dir="<?php echo e(in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr'); ?>" data-theme="<?php echo e(session('theme', 'light')); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description"
        content="<?php echo e($metaDescription ?? config('app.name') . ' - ' . __('general.app_description')); ?>">
    <meta name="theme-color" content="#15B76C">
    <meta name="theme-switch-url" content="<?php echo e(route('theme.switch')); ?>">
    <meta name="password-verify-url" content="<?php echo e(route('account.settings.developer.verify-password')); ?>">
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <title><?php echo e($title ?? config('app.name')); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Tajawal:wght@400;500;700&display=swap"
        rel="stylesheet">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
    <div class="app-layout" x-data="appLayout" :class="{ 'sidebar-collapsed': collapsed }">
        <?php echo $__env->make('layouts.partials._user-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="main-content"
            @click="if(window.innerWidth < 992 && event.target === event.currentTarget) closeSidebarMobile()">
            <?php echo $__env->make('layouts.partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                <?php
                    $hasActive = (bool) auth()->user()->activeSubscription();
                    $hasExpiredWithoutGrace =
                        !$hasActive &&
                        auth()
                            ->user()
                            ->subscriptions()
                            ->withoutGlobalScopes()
                            ->whereIn('status', ['expired', 'canceled'])
                            ->where(function ($q) {
                                $q->whereNull('grace_ends_at')->orWhere('grace_ends_at', '<', now());
                            })
                            ->exists();
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasExpiredWithoutGrace): ?>
                    <div x-data="{ show: true }" x-show="show" x-transition.duration.300
                        class="mx-3 mx-md-4 mt-2 mb-0 px-3 py-2 rounded-2 d-flex align-items-center justify-content-between"
                        style="background:#fff3cd;color:#856404;border:1px solid #ffc107;font-size:13px">
                        <div>
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <?php echo e(__('enums.subscription_status.expired') ?? 'اشتراكك منتهي. يرجى تجديد الاشتراك.'); ?>

                            <a href="<?php echo e(route('account.subscriptions')); ?>" class="ms-2 fw-bold text-decoration-underline"
                                style="color:#856404">
                                <?php echo e(__('subscription.renew') ?? 'تجديد'); ?>

                            </a>
                        </div>
                        <button @click="show = false" type="button"
                            style="background:none;border:none;color:#856404;cursor:pointer;padding:2px 6px">
                            <i class="bi bi-x-lg" style="font-size:11px"></i>
                        </button>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <main>
                <div class="page-header">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($breadcrumb)): ?>
                        <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb.index','data' => ['items' => $breadcrumb]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($breadcrumb)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $attributes = $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $component = $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <h1><?php echo e($pageTitle ?? ''); ?></h1>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($pageDescription)): ?>
                        <p><?php echo e($pageDescription); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="p-3 p-md-4">
                    <?php echo e($slot); ?>

                </div>
            </main>
        </div>

        <?php if (isset($component)) { $__componentOriginal635b39ef5be33bd3b6e46d3bb11dba21 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal635b39ef5be33bd3b6e46d3bb11dba21 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.command-palette','data' => ['items' => [
            [
                'title' => __('general.dashboard'),
                'description' => __('general.go_to_dashboard'),
                'icon' => 'bi bi-grid-1x2-fill',
                'url' => route('dashboard'),
            ],
            [
                'title' => __('general.profile'),
                'description' => __('general.manage_profile'),
                'icon' => 'bi bi-person',
                'url' => route('account.profile'),
            ],
            [
                'title' => __('general.settings'),
                'description' => __('general.manage_settings'),
                'icon' => 'bi bi-gear',
                'url' => route('settings.index'),
            ],
            [
                'title' => __('transactions.add_income'),
                'description' => __('transactions.record_new_income'),
                'icon' => 'bi bi-plus-circle',
                'url' => route('income.create'),
            ],
            [
                'title' => __('transactions.add_expense'),
                'description' => __('transactions.record_new_expense'),
                'icon' => 'bi bi-dash-circle',
                'url' => route('expense.create'),
            ],
            [
                'title' => __('transactions.title'),
                'description' => __('transactions.view_all'),
                'icon' => 'bi bi-arrow-left-right',
                'url' => route('transactions.index'),
            ],
            [
                'title' => __('budget.title'),
                'description' => __('budget.manage'),
                'icon' => 'bi bi-pie-chart',
                'url' => route('budget.index'),
            ],
            [
                'title' => __('goal.title'),
                'description' => __('goal.manage'),
                'icon' => 'bi bi-flag',
                'url' => route('goal.index'),
            ],
            [
                'title' => __('debt.title'),
                'description' => __('debt.manage'),
                'icon' => 'bi bi-credit-card-2-front',
                'url' => route('debt.index'),
            ],
            [
                'title' => __('general.notifications'),
                'description' => __('general.view_notifications'),
                'icon' => 'bi bi-bell',
                'url' => route('notifications.index'),
            ],
            [
                'title' => __('report.title'),
                'description' => __('report.generate'),
                'icon' => 'bi bi-file-earmark-bar-graph',
                'url' => route('report.index'),
            ],
            [
                'title' => __('invoices.title'),
                'description' => __('invoices.view_all'),
                'icon' => 'bi bi-receipt',
                'url' => route('account.invoices.index'),
            ],
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('command-palette'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            [
                'title' => __('general.dashboard'),
                'description' => __('general.go_to_dashboard'),
                'icon' => 'bi bi-grid-1x2-fill',
                'url' => route('dashboard'),
            ],
            [
                'title' => __('general.profile'),
                'description' => __('general.manage_profile'),
                'icon' => 'bi bi-person',
                'url' => route('account.profile'),
            ],
            [
                'title' => __('general.settings'),
                'description' => __('general.manage_settings'),
                'icon' => 'bi bi-gear',
                'url' => route('settings.index'),
            ],
            [
                'title' => __('transactions.add_income'),
                'description' => __('transactions.record_new_income'),
                'icon' => 'bi bi-plus-circle',
                'url' => route('income.create'),
            ],
            [
                'title' => __('transactions.add_expense'),
                'description' => __('transactions.record_new_expense'),
                'icon' => 'bi bi-dash-circle',
                'url' => route('expense.create'),
            ],
            [
                'title' => __('transactions.title'),
                'description' => __('transactions.view_all'),
                'icon' => 'bi bi-arrow-left-right',
                'url' => route('transactions.index'),
            ],
            [
                'title' => __('budget.title'),
                'description' => __('budget.manage'),
                'icon' => 'bi bi-pie-chart',
                'url' => route('budget.index'),
            ],
            [
                'title' => __('goal.title'),
                'description' => __('goal.manage'),
                'icon' => 'bi bi-flag',
                'url' => route('goal.index'),
            ],
            [
                'title' => __('debt.title'),
                'description' => __('debt.manage'),
                'icon' => 'bi bi-credit-card-2-front',
                'url' => route('debt.index'),
            ],
            [
                'title' => __('general.notifications'),
                'description' => __('general.view_notifications'),
                'icon' => 'bi bi-bell',
                'url' => route('notifications.index'),
            ],
            [
                'title' => __('report.title'),
                'description' => __('report.generate'),
                'icon' => 'bi bi-file-earmark-bar-graph',
                'url' => route('report.index'),
            ],
            [
                'title' => __('invoices.title'),
                'description' => __('invoices.view_all'),
                'icon' => 'bi bi-receipt',
                'url' => route('account.invoices.index'),
            ],
        ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal635b39ef5be33bd3b6e46d3bb11dba21)): ?>
<?php $attributes = $__attributesOriginal635b39ef5be33bd3b6e46d3bb11dba21; ?>
<?php unset($__attributesOriginal635b39ef5be33bd3b6e46d3bb11dba21); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal635b39ef5be33bd3b6e46d3bb11dba21)): ?>
<?php $component = $__componentOriginal635b39ef5be33bd3b6e46d3bb11dba21; ?>
<?php unset($__componentOriginal635b39ef5be33bd3b6e46d3bb11dba21); ?>
<?php endif; ?>
    </div>

    <?php if (isset($component)) { $__componentOriginal7cfab914afdd05940201ca0b2cbc009b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7cfab914afdd05940201ca0b2cbc009b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toast','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toast'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7cfab914afdd05940201ca0b2cbc009b)): ?>
<?php $attributes = $__attributesOriginal7cfab914afdd05940201ca0b2cbc009b; ?>
<?php unset($__attributesOriginal7cfab914afdd05940201ca0b2cbc009b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7cfab914afdd05940201ca0b2cbc009b)): ?>
<?php $component = $__componentOriginal7cfab914afdd05940201ca0b2cbc009b; ?>
<?php unset($__componentOriginal7cfab914afdd05940201ca0b2cbc009b); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal2cfaf2d8c559a20e3495c081df2d0b10 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2cfaf2d8c559a20e3495c081df2d0b10 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirm-modal.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirm-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2cfaf2d8c559a20e3495c081df2d0b10)): ?>
<?php $attributes = $__attributesOriginal2cfaf2d8c559a20e3495c081df2d0b10; ?>
<?php unset($__attributesOriginal2cfaf2d8c559a20e3495c081df2d0b10); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2cfaf2d8c559a20e3495c081df2d0b10)): ?>
<?php $component = $__componentOriginal2cfaf2d8c559a20e3495c081df2d0b10; ?>
<?php unset($__componentOriginal2cfaf2d8c559a20e3495c081df2d0b10); ?>
<?php endif; ?>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
    <script type="module" src="https://esm.sh/ionicons@latest/loader"></script>
    <script nomodule src="https://esm.sh/ionicons@latest/loader"></script>
    <?php echo $__env->make('layouts.partials._alpine-components', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>

</html>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/layouts/app.blade.php ENDPATH**/ ?>