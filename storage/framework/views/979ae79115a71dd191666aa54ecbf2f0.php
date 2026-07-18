<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>"
    dir="<?php echo e(in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr'); ?>" data-theme="<?php echo e(session('theme', 'light')); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description" content="<?php echo e(config('app.name') . ' - ' . __('super-admin.super_dashboard')); ?>">
    <meta name="theme-color" content="#6366F1">
    <meta name="theme-switch-url" content="<?php echo e(route('theme.switch')); ?>">
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <title><?php echo e($title ?? __('super-admin.super_dashboard')); ?> - <?php echo e(config('app.name', 'Finance Manager')); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700&display=swap"
        rel="stylesheet">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="super-admin-layout" x-data="superAdminLayout" :class="{ 'sidebar-collapsed': sidebarCollapsed }">
    <?php if (isset($component)) { $__componentOriginal4d6bed2ebceb29e0a9932fbda627422a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4d6bed2ebceb29e0a9932fbda627422a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-loader','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4d6bed2ebceb29e0a9932fbda627422a)): ?>
<?php $attributes = $__attributesOriginal4d6bed2ebceb29e0a9932fbda627422a; ?>
<?php unset($__attributesOriginal4d6bed2ebceb29e0a9932fbda627422a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4d6bed2ebceb29e0a9932fbda627422a)): ?>
<?php $component = $__componentOriginal4d6bed2ebceb29e0a9932fbda627422a; ?>
<?php unset($__componentOriginal4d6bed2ebceb29e0a9932fbda627422a); ?>
<?php endif; ?>
    <?php echo $__env->make('layouts.super-admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content">
        <?php echo $__env->make('layouts.super-admin.partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <main x-data="{ loaded: true }">
            <div class="page-header">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($breadcrumb)): ?>
                    <div class="mb-2">
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
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <h1><?php echo e($pageTitle ?? ''); ?></h1>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($pageDescription)): ?>
                    <p><?php echo e($pageDescription); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="page-content">
                <?php echo e($slot); ?>

            </div>
        </main>
    </div>

    <?php if (isset($component)) { $__componentOriginal635b39ef5be33bd3b6e46d3bb11dba21 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal635b39ef5be33bd3b6e46d3bb11dba21 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.command-palette','data' => ['items' => [
        [
            'title' => __('super-admin.dashboard'),
            'description' => __('super-admin.overview'),
            'icon' => 'bi bi-grid-1x2-fill',
            'url' => route('super.admin.dashboard'),
        ],
        [
            'title' => __('super-admin.users'),
            'description' => __('super-admin.manage_users'),
            'icon' => 'bi bi-people',
            'url' => route('super.admin.users.index'),
        ],
        [
            'title' => __('super-admin.workspaces'),
            'description' => __('super-admin.manage_workspaces'),
            'icon' => 'bi bi-layers',
            'url' => route('super.admin.workspaces.index'),
        ],
        [
            'title' => __('super-admin.subscriptions'),
            'description' => __('super-admin.manage_subscriptions'),
            'icon' => 'bi bi-credit-card',
            'url' => route('super.admin.subscriptions.index'),
        ],
        [
            'title' => __('super-admin.plans'),
            'description' => __('super-admin.manage_plans'),
            'icon' => 'bi bi-box',
            'url' => route('super.admin.plans.index'),
        ],
        [
            'title' => __('super-admin.invoices'),
            'description' => __('super-admin.view_invoices'),
            'icon' => 'bi bi-receipt',
            'url' => route('super.admin.invoices.index'),
        ],
        [
            'title' => __('super-admin.payments'),
            'description' => __('super-admin.view_payments'),
            'icon' => 'bi bi-currency-dollar',
            'url' => route('super.admin.payments.index'),
        ],
        [
            'title' => __('super-admin.payment_methods'),
            'description' => __('super-admin.payment_methods_desc'),
            'icon' => 'bi bi-credit-card-2-front',
            'url' => route('super.admin.payment-methods.index'),
        ],
        [
            'title' => __('super-admin.coupons'),
            'description' => __('super-admin.manage_coupons'),
            'icon' => 'bi bi-percent',
            'url' => route('super.admin.coupons-tax-rates.index'),
        ],
        [
            'title' => __('super-admin.roles'),
            'description' => __('super-admin.manage_roles'),
            'icon' => 'bi bi-shield',
            'url' => route('super.admin.roles.index'),
        ],
        [
            'title' => __('super-admin.test_checklist'),
            'description' => __('super-admin.test_checklist_cmd'),
            'icon' => 'bi bi-check2-square',
            'url' => route('super.admin.test-checklist.index'),
        ],
        [
            'title' => __('super-admin.activity_log'),
            'description' => __('super-admin.view_activity'),
            'icon' => 'bi bi-activity',
            'url' => route('super.admin.activity-log'),
        ],
        [
            'title' => __('super-admin.settings'),
            'description' => __('super.admin.system_settings'),
            'icon' => 'bi bi-gear',
            'url' => route('super.admin.settings.index'),
        ],
        [
            'title' => __('general.dashboard'),
            'description' => __('general.go_to_user_dashboard'),
            'icon' => 'bi bi-person',
            'url' => route('dashboard'),
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
            'title' => __('super-admin.dashboard'),
            'description' => __('super-admin.overview'),
            'icon' => 'bi bi-grid-1x2-fill',
            'url' => route('super.admin.dashboard'),
        ],
        [
            'title' => __('super-admin.users'),
            'description' => __('super-admin.manage_users'),
            'icon' => 'bi bi-people',
            'url' => route('super.admin.users.index'),
        ],
        [
            'title' => __('super-admin.workspaces'),
            'description' => __('super-admin.manage_workspaces'),
            'icon' => 'bi bi-layers',
            'url' => route('super.admin.workspaces.index'),
        ],
        [
            'title' => __('super-admin.subscriptions'),
            'description' => __('super-admin.manage_subscriptions'),
            'icon' => 'bi bi-credit-card',
            'url' => route('super.admin.subscriptions.index'),
        ],
        [
            'title' => __('super-admin.plans'),
            'description' => __('super-admin.manage_plans'),
            'icon' => 'bi bi-box',
            'url' => route('super.admin.plans.index'),
        ],
        [
            'title' => __('super-admin.invoices'),
            'description' => __('super-admin.view_invoices'),
            'icon' => 'bi bi-receipt',
            'url' => route('super.admin.invoices.index'),
        ],
        [
            'title' => __('super-admin.payments'),
            'description' => __('super-admin.view_payments'),
            'icon' => 'bi bi-currency-dollar',
            'url' => route('super.admin.payments.index'),
        ],
        [
            'title' => __('super-admin.payment_methods'),
            'description' => __('super-admin.payment_methods_desc'),
            'icon' => 'bi bi-credit-card-2-front',
            'url' => route('super.admin.payment-methods.index'),
        ],
        [
            'title' => __('super-admin.coupons'),
            'description' => __('super-admin.manage_coupons'),
            'icon' => 'bi bi-percent',
            'url' => route('super.admin.coupons-tax-rates.index'),
        ],
        [
            'title' => __('super-admin.roles'),
            'description' => __('super-admin.manage_roles'),
            'icon' => 'bi bi-shield',
            'url' => route('super.admin.roles.index'),
        ],
        [
            'title' => __('super-admin.test_checklist'),
            'description' => __('super-admin.test_checklist_cmd'),
            'icon' => 'bi bi-check2-square',
            'url' => route('super.admin.test-checklist.index'),
        ],
        [
            'title' => __('super-admin.activity_log'),
            'description' => __('super-admin.view_activity'),
            'icon' => 'bi bi-activity',
            'url' => route('super.admin.activity-log'),
        ],
        [
            'title' => __('super-admin.settings'),
            'description' => __('super.admin.system_settings'),
            'icon' => 'bi bi-gear',
            'url' => route('super.admin.settings.index'),
        ],
        [
            'title' => __('general.dashboard'),
            'description' => __('general.go_to_user_dashboard'),
            'icon' => 'bi bi-person',
            'url' => route('dashboard'),
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

    <?php echo $__env->make('layouts.partials._alpine-components', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
        function adminNotificationDropdown() {
            return {
                open: false,
                notifications: [],
                unreadCount: 0,
                pollInterval: null,

                init() {
                    this.fetchNotifications();
                    this.pollInterval = setInterval(() => this.fetchNotifications(), 30000);
                },

                destroy() {
                    if (this.pollInterval) clearInterval(this.pollInterval);
                },

                toggle() {
                    this.open = !this.open;
                    if (this.open) this.fetchNotifications();
                },

                fetchNotifications() {
                    const locale = document.documentElement.lang.substring(0, 2) || 'en';
                    fetch('<?php echo e(route('super.admin.notifications.index')); ?>', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            this.notifications = (data.notifications || []).map(n => ({
                                ...n,
                                _title: n['title_' + locale] || n.title_en || n.title_ar || n.title_fr || '',
                                _message: n['message_' + locale] || n.message_en || n.message_ar || n.message_fr || '',
                            }));
                            this.unreadCount = data.unread_count || 0;
                        })
                        .catch(() => {});
                },

                markRead(id) {
                    fetch(`/super-admin/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).then(() => this.fetchNotifications());
                },

                markAllRead() {
                    fetch('<?php echo e(route('super.admin.notifications.mark-all-read')); ?>', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).then(() => this.fetchNotifications());
                },

                iconClass(type) {
                    const map = {
                        new_user: 'bi-person-plus',
                        new_payment: 'bi-cash-stack',
                        subscription_activated: 'bi-stars',
                        backup_completed: 'bi-cloud-check',
                        system_alert: 'bi-exclamation-triangle',
                    };
                    return map[type] || 'bi-bell';
                },

                iconColor(type) {
                    const map = {
                        new_user: 'primary',
                        new_payment: 'success',
                        subscription_activated: 'info',
                        backup_completed: 'secondary',
                        system_alert: 'warning',
                    };
                    return map[type] || 'secondary';
                },

                timeAgo(dateStr) {
                    const now = new Date();
                    const date = new Date(dateStr);
                    const diff = Math.floor((now - date) / 1000);
                    if (diff < 60) return '<?php echo e(__('general.just_now')); ?>';
                    if (diff < 3600) return Math.floor(diff / 60) + 'm';
                    if (diff < 86400) return Math.floor(diff / 3600) + 'h';
                    return Math.floor(diff / 86400) + 'd';
                },
            };
        }
    </script>
    <script type="module" src="https://esm.sh/ionicons@latest/loader"></script>
    <script nomodule src="https://esm.sh/ionicons@latest/loader"></script>
</body>

</html>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\layouts\super-admin\app.blade.php ENDPATH**/ ?>