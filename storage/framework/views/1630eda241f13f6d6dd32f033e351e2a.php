<aside class="sidebar" id="mainSidebar">
    <div class="sidebar-header">
        <a href="<?php echo e(route('super.admin.dashboard')); ?>" class="sidebar-logo">
            <div class="logo-icon">
                <i class="bi bi-shield-shaded"></i>
            </div>
            <div class="logo-text-group" x-show="!sidebarCollapsed" x-cloak>
                <span class="logo-text"><?php echo e(config('app.name', 'Finance Manager')); ?></span>
                <span class="logo-badge"><?php echo e(__('super-admin.admin_panel')); ?></span>
            </div>
        </a>
    </div>
    <button class="sidebar-collapse-btn" @click="toggleSidebar()" title="<?php echo e(__('general.collapse_toggle')); ?>">
        <i class="bi bi-chevron-left" x-show="!sidebarCollapsed" x-cloak></i>
        <i class="bi bi-chevron-right" x-show="sidebarCollapsed" x-cloak></i>
    </button>

    <div class="sidebar-search" @click="window.dispatchEvent(new CustomEvent('toggle-cmd-palette'))" role="button" tabindex="0">
        <div class="sidebar-search-box">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="<?php echo e(__('general.search')); ?>..." readonly x-show="!sidebarCollapsed" x-cloak>
            <span class="sidebar-search-hint" x-show="!sidebarCollapsed" x-cloak>⌘K</span>
        </div>
    </div>

    <nav class="sidebar-body">
        
        <div class="sidebar-nav-section" x-data="{ open: true }">
            <button class="sidebar-section-toggle" @click="open = !open" type="button" data-label="<?php echo e(__('super-admin.overview')); ?>">
                <i class="bi bi-compass section-toggle-icon"></i>
                <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.overview')); ?></span>
                <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!sidebarCollapsed" x-cloak></i>
            </button>
            <div x-show="open">
                <a href="<?php echo e(route('super.admin.dashboard')); ?>"
                   class="sidebar-nav-item <?php echo e(request()->routeIs('super.admin.dashboard') ? 'active' : ''); ?>"
                   wire:navigate data-label="<?php echo e(__('super-admin.dashboard')); ?>">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.dashboard')); ?></span>
                </a>
            </div>
        </div>

        
        <div class="sidebar-nav-section" x-data="{ open: <?php echo e(request()->routeIs('super.admin.users*') || request()->routeIs('super.admin.workspaces*') ? 'true' : 'false'); ?> }">
            <button class="sidebar-section-toggle" @click="open = !open" type="button" data-label="<?php echo e(__('super-admin.management')); ?>">
                <i class="bi bi-building-gear section-toggle-icon"></i>
                <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.management')); ?></span>
                <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!sidebarCollapsed" x-cloak></i>
            </button>
            <div x-show="open" style="display:none">
                <a href="<?php echo e(route('super.admin.users.index')); ?>"
                   class="sidebar-nav-item <?php echo e(request()->routeIs('super.admin.users*') ? 'active' : ''); ?>"
                   wire:navigate data-label="<?php echo e(__('super-admin.users')); ?>">
                    <i class="bi bi-people-fill"></i>
                    <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.users')); ?></span>
                </a>
                <a href="<?php echo e(route('super.admin.workspaces.index')); ?>"
                   class="sidebar-nav-item <?php echo e(request()->routeIs('super.admin.workspaces*') ? 'active' : ''); ?>"
                   wire:navigate data-label="<?php echo e(__('super-admin.workspaces')); ?>">
                    <i class="bi bi-building"></i>
                    <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.workspaces')); ?></span>
                </a>
            </div>
        </div>

        
        <div class="sidebar-nav-section" x-data="{ open: <?php echo e(request()->routeIs('super.admin.plans*') || request()->routeIs('super.admin.subscriptions*') || request()->routeIs('super.admin.invoices*') || request()->routeIs('super.admin.payments*') || request()->routeIs('super.admin.payment-methods*') || request()->routeIs('super.admin.coupons*') || request()->routeIs('super.admin.noest-orders*') ? 'true' : 'false'); ?> }">
            <button class="sidebar-section-toggle" @click="open = !open" type="button" data-label="<?php echo e(__('super-admin.billing')); ?>">
                <i class="bi bi-credit-card section-toggle-icon"></i>
                <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.billing')); ?></span>
                <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!sidebarCollapsed" x-cloak></i>
            </button>
            <div x-show="open" style="display:none">
                <a href="<?php echo e(route('super.admin.plans.index')); ?>"
                   class="sidebar-nav-item <?php echo e(request()->routeIs('super.admin.plans*') ? 'active' : ''); ?>"
                   wire:navigate data-label="<?php echo e(__('super-admin.plans')); ?>">
                    <i class="bi bi-box"></i>
                    <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.plans')); ?></span>
                </a>
                <a href="<?php echo e(route('super.admin.subscriptions.index')); ?>"
                   class="sidebar-nav-item <?php echo e(request()->routeIs('super.admin.subscriptions*') ? 'active' : ''); ?>"
                   wire:navigate data-label="<?php echo e(__('super-admin.subscriptions')); ?>">
                    <i class="bi bi-credit-card"></i>
                    <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.subscriptions')); ?></span>
                </a>
                <a href="<?php echo e(route('super.admin.invoices.index')); ?>"
                   class="sidebar-nav-item <?php echo e(request()->routeIs('super.admin.invoices*') ? 'active' : ''); ?>"
                   wire:navigate data-label="<?php echo e(__('super-admin.invoices')); ?>">
                    <i class="bi bi-receipt"></i>
                    <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.invoices')); ?></span>
                </a>
                <a href="<?php echo e(route('super.admin.payments.index')); ?>"
                   class="sidebar-nav-item <?php echo e(request()->routeIs('super.admin.payments.*') ? 'active' : ''); ?>"
                   wire:navigate data-label="<?php echo e(__('super-admin.payments')); ?>">
                    <i class="bi bi-cash-coin"></i>
                    <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.payments')); ?></span>
                </a>
                <a href="<?php echo e(route('super.admin.payment-methods.index')); ?>"
                   class="sidebar-nav-item <?php echo e(request()->routeIs('super.admin.payment-methods*') ? 'active' : ''); ?>"
                   wire:navigate data-label="<?php echo e(__('super-admin.payment_methods')); ?>">
                    <i class="bi bi-credit-card-2-front"></i>
                    <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.payment_methods')); ?></span>
                </a>

                <a href="<?php echo e(route('super.admin.coupons-tax-rates.index')); ?>"
                   class="sidebar-nav-item <?php echo e(request()->routeIs('super.admin.coupons-tax-rates*') || request()->routeIs('super.admin.coupons.*') || request()->routeIs('super.admin.tax-rates.*') ? 'active' : ''); ?>"
                   wire:navigate data-label="<?php echo e(__('super-admin.coupons')); ?>">
                    <i class="bi bi-tags"></i>
                    <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.coupons')); ?> &amp; <?php echo e(__('super-admin.tax_rates')); ?></span>
                </a>
                <a href="<?php echo e(route('super.admin.noest-orders.index')); ?>"
                   class="sidebar-nav-item <?php echo e(request()->routeIs('super.admin.noest-orders*') ? 'active' : ''); ?>"
                   wire:navigate data-label="<?php echo e(__('super-admin.noest_orders')); ?>">
                    <i class="bi bi-truck"></i>
                    <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.noest_orders')); ?></span>
                </a>
            </div>
        </div>

        
        <div class="sidebar-nav-section" x-data="{ open: <?php echo e(request()->routeIs('super.admin.roles*') || request()->routeIs('super.admin.workspace-roles*') ? 'true' : 'false'); ?> }">
            <button class="sidebar-section-toggle" @click="open = !open" type="button" data-label="<?php echo e(__('super-admin.access_control')); ?>">
                <i class="bi bi-shield-lock section-toggle-icon"></i>
                <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.access_control')); ?></span>
                <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!sidebarCollapsed" x-cloak></i>
            </button>
            <div x-show="open" style="display:none">
                <a href="<?php echo e(route('super.admin.roles.index')); ?>"
                   class="sidebar-nav-item <?php echo e(request()->routeIs('super.admin.roles*') && !request()->routeIs('super.admin.workspace-roles*') ? 'active' : ''); ?>"
                   wire:navigate data-label="<?php echo e(__('super-admin.platform_roles')); ?>">
                    <i class="bi bi-shield-lock"></i>
                    <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.platform_roles')); ?></span>
                </a>
                <a href="<?php echo e(route('super.admin.workspace-roles.index')); ?>"
                   class="sidebar-nav-item sidebar-sub-item <?php echo e(request()->routeIs('super.admin.workspace-roles*') ? 'active' : ''); ?>"
                   wire:navigate data-label="<?php echo e(__('super-admin.workspace_roles')); ?>">
                    <i class="bi bi-people"></i>
                    <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.workspace_roles')); ?></span>
                </a>
            </div>
        </div>

        
        <div class="sidebar-nav-section" x-data="{ open: <?php echo e(request()->routeIs('super.admin.notifications*') || request()->routeIs('super.admin.backups*') || request()->routeIs('super.admin.activity-log') || request()->routeIs('super.admin.settings*') || request()->routeIs('super.admin.test-checklist*') ? 'true' : 'false'); ?> }">
            <button class="sidebar-section-toggle" @click="open = !open" type="button" data-label="<?php echo e(__('super-admin.system')); ?>">
                <i class="bi bi-cpu section-toggle-icon"></i>
                <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.system')); ?></span>
                <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!sidebarCollapsed" x-cloak></i>
            </button>
            <div x-show="open" style="display:none">
                <a href="<?php echo e(route('super.admin.notifications.index')); ?>"
                   class="sidebar-nav-item <?php echo e(request()->routeIs('super.admin.notifications*') ? 'active' : ''); ?>"
                   wire:navigate data-label="<?php echo e(__('admin.notifications')); ?>">
                    <i class="bi bi-bell"></i>
                    <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('admin.notifications')); ?></span>
                </a>
                <a href="<?php echo e(route('super.admin.backups.index')); ?>"
                   class="sidebar-nav-item <?php echo e(request()->routeIs('super.admin.backups*') ? 'active' : ''); ?>"
                   wire:navigate data-label="<?php echo e(__('super-admin.backups')); ?>">
                    <i class="bi bi-cloud-arrow-up"></i>
                    <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.backups')); ?></span>
                </a>
                <a href="<?php echo e(route('super.admin.test-checklist.index')); ?>"
                   class="sidebar-nav-item <?php echo e(request()->routeIs('super.admin.test-checklist*') ? 'active' : ''); ?>"
                   wire:navigate data-label="<?php echo e(__('super-admin.test_checklist')); ?>">
                    <i class="bi bi-check2-square"></i>
                    <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.test_checklist')); ?></span>
                </a>
                <a href="<?php echo e(route('super.admin.activity-log')); ?>"
                   class="sidebar-nav-item <?php echo e(request()->routeIs('super.admin.activity-log') ? 'active' : ''); ?>"
                   wire:navigate data-label="<?php echo e(__('super-admin.activity_log')); ?>">
                    <i class="bi bi-clock-history"></i>
                    <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.activity_log')); ?></span>
                </a>
                <a href="<?php echo e(route('super.admin.settings.index')); ?>"
                   class="sidebar-nav-item <?php echo e(request()->routeIs('super.admin.settings*') ? 'active' : ''); ?>"
                   wire:navigate data-label="<?php echo e(__('super-admin.settings')); ?>">
                    <i class="bi bi-gear-fill"></i>
                    <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.settings')); ?></span>
                </a>
            </div>
        </div>

        <hr class="sidebar-nav-divider" x-show="!sidebarCollapsed" x-cloak>

        <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-nav-item" wire:navigate data-label="<?php echo e(__('super-admin.back_to_app')); ?>">
            <i class="bi bi-arrow-left"></i>
            <span x-show="!sidebarCollapsed" x-cloak><?php echo e(__('super-admin.back_to_app')); ?></span>
        </a>
    </nav>

    <div class="sidebar-footer" x-data="profileDropdown()" @profile-dropdown-close.window="open = false">
        <button class="sidebar-nav-item profile-item" @click="toggle()" data-label="<?php echo e(__('general.profile')); ?>">
            <div class="profile-avatar">
                <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

            </div>
            <div class="profile-info" x-show="!sidebarCollapsed" x-cloak>
                <span class="profile-name"><?php echo e(auth()->user()->name); ?></span>
                <span class="profile-email"><?php echo e(__('super-admin.super_admin')); ?></span>
            </div>
            <i class="bi bi-chevron-up profile-chevron" :class="open && !sidebarCollapsed ? 'rotate-180' : ''" x-show="!sidebarCollapsed" x-cloak></i>
        </button>

        <?php echo $__env->make('layouts.partials._profile-dropdown-menu-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</aside>

<div class="sidebar-overlay" @click="closeSidebarMobile()"></div>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\layouts\super-admin\partials\sidebar.blade.php ENDPATH**/ ?>