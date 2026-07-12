<aside class="sidebar" id="mainSidebar">
    <div class="sidebar-header">
        <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-logo" wire:navigate>
            <div class="logo-icon">FM</div>
            <div class="logo-text-group" x-show="!collapsed" x-cloak>
                <span class="logo-text"><?php echo e(config('app.name')); ?></span>
                <span class="logo-badge"><?php echo e(__('general.enterprise')); ?></span>
            </div>
        </a>
    </div>
    <button class="sidebar-collapse-btn" @click="toggleSidebar()" title="<?php echo e(__('general.collapse_toggle')); ?>">
        <i class="bi bi-chevron-left" x-show="!collapsed" x-cloak></i>
        <i class="bi bi-chevron-right" x-show="collapsed" x-cloak></i>
    </button>

    <?php
        $user = auth()->user();
        $allWorkspaces = $user->workspaces;
        $currentWs = $user->currentWorkspace;
        $activeSub = $currentWs ? $currentWs->owner()?->first()?->activeSubscription() : null;
        $currentPlan = $activeSub?->plan;
        $planName = $currentPlan?->name;
        $planFeats = $currentPlan ? $currentPlan->featureSlugs() : [];

        $feat = fn($s) => in_array($s, $planFeats);
    ?>
    <div class="sidebar-workspace" x-data="{ wsOpen: false }">
        <button class="workspace-switcher" type="button" @click="wsOpen = !wsOpen" @click.away="wsOpen = false">
            <span class="workspace-avatar"><?php echo e(substr($currentWs?->name ?? 'W', 0, 1)); ?></span>
            <span class="workspace-info" x-show="!collapsed" x-cloak>
                <span class="workspace-name"><?php echo e($currentWs?->name ?? __('workspace.select')); ?></span>
                <span class="workspace-role"><?php echo e(__('workspace.owner')); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($planName): ?>
                    <span class="workspace-plan-badge" style="font-size:10px;color:var(--accent);font-weight:600;display:block;margin-top:1px"><?php echo e($planName); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>
            <i class="bi bi-chevron-down workspace-chevron" :class="wsOpen ? 'rotate-180' : ''" x-show="!collapsed" x-cloak></i>
        </button>
            <div class="workspace-dropdown" x-show="wsOpen" @click.away="wsOpen = false" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allWorkspaces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ws): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <form method="POST" action="<?php echo e(route('workspace.switch', $ws)); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="workspace-dropdown-item">
                            <span class="ws-dot <?php echo e($currentWs && $currentWs->id === $ws->id ? 'active-ws' : ''); ?>"><?php echo e(substr($ws->name, 0, 1)); ?></span>
                            <span><?php echo e($ws->name); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentWs && $currentWs->id === $ws->id): ?>
                                <i class="bi bi-check-lg ws-check"></i>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </button>
                    </form>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <hr class="workspace-dropdown-divider">
                <a href="<?php echo e(route('settings.workspace.create')); ?>" class="workspace-dropdown-create" wire:navigate>
                    <i class="bi bi-plus-lg" style="font-size:10px;opacity:0.6"></i>
                    <span><?php echo e(__('workspace.create_new')); ?></span>
                </a>
            </div>
    </div>

    <div class="sidebar-search" @click="window.dispatchEvent(new CustomEvent('toggle-cmd-palette'))" role="button" tabindex="0">
        <div class="sidebar-search-box">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="<?php echo e(__('general.search')); ?>..." readonly x-show="!collapsed" x-cloak>
            <span class="sidebar-search-hint" x-show="!collapsed" x-cloak>⌘K</span>
        </div>
    </div>

    <nav class="sidebar-body">
        
        <div class="sidebar-nav-section" x-data="{ open: true }">
            <button class="sidebar-section-toggle" @click="open = !open" type="button" data-label="<?php echo e(__('general.overview')); ?>">
                <i class="bi bi-compass section-toggle-icon"></i>
                <span x-show="!collapsed" x-cloak><?php echo e(__('general.overview')); ?></span>
                <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!collapsed" x-cloak></i>
            </button>
            <div x-show="open">
                <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-nav-item <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>" wire:navigate data-label="<?php echo e(__('general.dashboard')); ?>">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span x-show="!collapsed" x-cloak><?php echo e(__('general.dashboard')); ?></span>
                </a>
            </div>
        </div>

        <?php
            $cTxn = $user->workspaceHasPermission('transaction.view');
            $cInc = $user->workspaceHasPermission('income.view');
            $cExp = $user->workspaceHasPermission('expense.view');
            $hasFin = $cTxn || $cInc || $cExp;
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasFin): ?>
        <div class="sidebar-nav-section" x-data="{ open: <?php echo e(request()->routeIs('transactions.*') || request()->routeIs('income.*') || request()->routeIs('expense.*') ? 'true' : 'false'); ?> }">
            <button class="sidebar-section-toggle" @click="open = !open" type="button" data-label="<?php echo e(__('general.finances')); ?>">
                <i class="bi bi-wallet2 section-toggle-icon"></i>
                <span x-show="!collapsed" x-cloak><?php echo e(__('general.finances')); ?></span>
                <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!collapsed" x-cloak></i>
            </button>
            <div x-show="open" style="display:none">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cTxn): ?>
                <a href="<?php echo e(route('transactions.index')); ?>" class="sidebar-nav-item <?php echo e(request()->routeIs('transactions.*') ? 'active' : ''); ?>" wire:navigate data-label="<?php echo e(__('transactions.title')); ?>">
                    <i class="bi bi-arrow-left-right"></i>
                    <span x-show="!collapsed" x-cloak><?php echo e(__('transactions.title')); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$feat('income_expense')): ?> <span class="premium-badge"><?php echo e(__('settings.premium')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cInc): ?>
                <a href="<?php echo e(route('income.index')); ?>" class="sidebar-nav-item <?php echo e(request()->routeIs('income.*') ? 'active' : ''); ?>" wire:navigate data-label="<?php echo e(__('general.income')); ?>">
                    <i class="bi bi-cash-stack"></i>
                    <span x-show="!collapsed" x-cloak><?php echo e(__('general.income')); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$feat('income_expense')): ?> <span class="premium-badge"><?php echo e(__('settings.premium')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cExp): ?>
                <div x-data="{ expOpen: <?php echo e(request()->routeIs('expense.index') || request()->routeIs('expense.categories.*') ? 'true' : 'false'); ?> }">
                    <a href="<?php echo e(route('expense.index')); ?>" class="sidebar-nav-item sidebar-nav-parent <?php echo e(request()->routeIs('expense.index') ? 'active' : ''); ?>" wire:navigate data-label="<?php echo e(__('general.expense')); ?>">
                        <i class="bi bi-cart"></i>
                        <span x-show="!collapsed" x-cloak><?php echo e(__('general.expense')); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$feat('income_expense')): ?> <span class="premium-badge"><?php echo e(__('settings.premium')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                        <i class="bi bi-chevron-down nav-parent-chevron" :class="expOpen ? '' : 'collapsed'" x-show="!collapsed" x-cloak @click.prevent.stop="expOpen = !expOpen"></i>
                    </a>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php
            $cBud = $user->workspaceHasPermission('budget.view');
            $cGoal = $user->workspaceHasPermission('goal.view');
            $hasPlan = $cBud || $cGoal;
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasPlan): ?>
        <div class="sidebar-nav-section" x-data="{ open: <?php echo e(request()->routeIs('budget.*') || request()->routeIs('goal.*') ? 'true' : 'false'); ?> }">
            <button class="sidebar-section-toggle" @click="open = !open" type="button" data-label="<?php echo e(__('general.planning')); ?>">
                <i class="bi bi-clipboard-check section-toggle-icon"></i>
                <span x-show="!collapsed" x-cloak><?php echo e(__('general.planning')); ?></span>
                <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!collapsed" x-cloak></i>
            </button>
            <div x-show="open" style="display:none">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cBud): ?>
                <a href="<?php echo e(route('budget.index')); ?>" class="sidebar-nav-item <?php echo e(request()->routeIs('budget.*') ? 'active' : ''); ?>" wire:navigate data-label="<?php echo e(__('general.budget')); ?>">
                    <i class="bi bi-calculator-fill"></i>
                    <span x-show="!collapsed" x-cloak><?php echo e(__('general.budget')); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$feat('budget')): ?> <span class="premium-badge"><?php echo e(__('settings.premium')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cGoal): ?>
                <a href="<?php echo e(route('goal.index')); ?>" class="sidebar-nav-item <?php echo e(request()->routeIs('goal.*') ? 'active' : ''); ?>" wire:navigate data-label="<?php echo e(__('general.goal')); ?>">
                    <i class="bi bi-flag-fill"></i>
                    <span x-show="!collapsed" x-cloak><?php echo e(__('general.goal')); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$feat('goals')): ?> <span class="premium-badge"><?php echo e(__('settings.premium')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php
            $cAst = $user->workspaceHasPermission('asset.view');
            $cDebt = $user->workspaceHasPermission('debt.view');
            $hasAst = $cAst || $cDebt;
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasAst): ?>
        <div class="sidebar-nav-section" x-data="{ open: <?php echo e(request()->routeIs('asset.*') || request()->routeIs('debt.*') ? 'true' : 'false'); ?> }">
            <button class="sidebar-section-toggle" @click="open = !open" type="button" data-label="<?php echo e(__('general.assets_liabilities')); ?>">
                <i class="bi bi-diagram-3 section-toggle-icon"></i>
                <span x-show="!collapsed" x-cloak><?php echo e(__('general.assets_liabilities')); ?></span>
                <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!collapsed" x-cloak></i>
            </button>
            <div x-show="open" style="display:none">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cAst): ?>
                <a href="<?php echo e(route('asset.index')); ?>" class="sidebar-nav-item <?php echo e(request()->routeIs('asset.*') ? 'active' : ''); ?>" wire:navigate data-label="<?php echo e(__('general.asset')); ?>">
                    <i class="bi bi-pie-chart-fill"></i>
                    <span x-show="!collapsed" x-cloak><?php echo e(__('general.asset')); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$feat('debt')): ?> <span class="premium-badge"><?php echo e(__('settings.premium')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cDebt): ?>
                <a href="<?php echo e(route('debt.index')); ?>" class="sidebar-nav-item <?php echo e(request()->routeIs('debt.*') ? 'active' : ''); ?>" wire:navigate data-label="<?php echo e(__('general.debt')); ?>">
                    <i class="bi bi-credit-card-2-front"></i>
                    <span x-show="!collapsed" x-cloak><?php echo e(__('general.debt')); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$feat('debt')): ?> <span class="premium-badge"><?php echo e(__('settings.premium')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php
            $cZak = $user->workspaceHasPermission('zakat.view');
            $cRpt = $user->workspaceHasPermission('report.view');
            $cAct = $user->workspaceHasPermission('activity-log.view');
            $hasMore = $cZak || $cRpt || $cAct;
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasMore): ?>
        <div class="sidebar-nav-section" x-data="{ open: <?php echo e(request()->routeIs('zakat.*') || request()->routeIs('report.*') || request()->routeIs('activity.logs') ? 'true' : 'false'); ?> }">
            <button class="sidebar-section-toggle" @click="open = !open" type="button" data-label="<?php echo e(__('general.more')); ?>">
                <i class="bi bi-three-dots-vertical section-toggle-icon"></i>
                <span x-show="!collapsed" x-cloak><?php echo e(__('general.more')); ?></span>
                <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!collapsed" x-cloak></i>
            </button>
            <div x-show="open" style="display:none">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cZak): ?>
                <a href="<?php echo e(route('zakat.calculator')); ?>" class="sidebar-nav-item <?php echo e(request()->routeIs('zakat.*') ? 'active' : ''); ?>" wire:navigate data-label="<?php echo e(__('general.zakat')); ?>">
                    <i class="bi bi-heart-fill"></i>
                    <span x-show="!collapsed" x-cloak><?php echo e(__('general.zakat')); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$feat('zakat')): ?> <span class="premium-badge"><?php echo e(__('settings.premium')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cRpt): ?>
                <a href="<?php echo e(route('report.index')); ?>" class="sidebar-nav-item <?php echo e(request()->routeIs('report.*') ? 'active' : ''); ?>" wire:navigate data-label="<?php echo e(__('general.report')); ?>">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i>
                    <span x-show="!collapsed" x-cloak><?php echo e(__('general.report')); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$feat('reports')): ?> <span class="premium-badge"><?php echo e(__('settings.premium')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cAct): ?>
                <a href="<?php echo e(route('activity.logs')); ?>" class="sidebar-nav-item <?php echo e(request()->routeIs('activity.logs') ? 'active' : ''); ?>" wire:navigate data-label="<?php echo e(__('settings.activity_log')); ?>">
                    <i class="bi bi-clock-history"></i>
                    <span x-show="!collapsed" x-cloak><?php echo e(__('settings.activity_log')); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$feat('activity_logs')): ?> <span class="premium-badge"><?php echo e(__('settings.premium')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <hr class="sidebar-nav-divider" x-show="!collapsed" x-cloak>

        
        <div class="sidebar-nav-section" x-data="{ open: <?php echo e(request()->routeIs('account.*') || request()->routeIs('two-factor.*') ? 'true' : 'false'); ?> }">
            <button class="sidebar-section-toggle" @click="open = !open" type="button" data-label="<?php echo e(__('general.account')); ?>">
                <i class="bi bi-person-circle section-toggle-icon"></i>
                <span x-show="!collapsed" x-cloak><?php echo e(__('general.account')); ?></span>
                <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!collapsed" x-cloak></i>
            </button>
            <div x-show="open" style="display:none">
                <a href="<?php echo e(route('account.profile')); ?>" class="sidebar-nav-item <?php echo e(request()->routeIs('account.profile') ? 'active' : ''); ?>" wire:navigate data-label="<?php echo e(__('general.profile')); ?>">
                    <i class="bi bi-person-fill"></i>
                    <span x-show="!collapsed" x-cloak><?php echo e(__('general.profile')); ?></span>
                </a>
                <a href="<?php echo e(route('account.subscriptions')); ?>" class="sidebar-nav-item <?php echo e(request()->routeIs('account.subscriptions') ? 'active' : ''); ?>" wire:navigate data-label="<?php echo e(__('settings.subscriptions')); ?>">
                    <i class="bi bi-credit-card-fill"></i>
                    <span x-show="!collapsed" x-cloak><?php echo e(__('settings.subscriptions')); ?></span>
                </a>
                <a href="<?php echo e(route('account.invoices.index')); ?>" class="sidebar-nav-item <?php echo e(request()->routeIs('account.invoices.*') ? 'active' : ''); ?>" wire:navigate data-label="<?php echo e(__('settings.invoices')); ?>">
                    <i class="bi bi-receipt"></i>
                    <span x-show="!collapsed" x-cloak><?php echo e(__('settings.invoices')); ?></span>
                </a>
                <a href="<?php echo e(route('account.settings')); ?>" class="sidebar-nav-item <?php echo e(request()->routeIs('account.settings') ? 'active' : ''); ?>" wire:navigate data-label="<?php echo e(__('general.preferences')); ?>">
                    <i class="bi bi-sliders2"></i>
                    <span x-show="!collapsed" x-cloak><?php echo e(__('general.preferences')); ?></span>
                </a>
                <a href="<?php echo e(route('account.settings.developer')); ?>" class="sidebar-nav-item <?php echo e(request()->routeIs('account.settings.developer*') ? 'active' : ''); ?>" wire:navigate data-label="<?php echo e(__('general.developers')); ?>">
                    <i class="bi bi-code-slash"></i>
                    <span x-show="!collapsed" x-cloak><?php echo e(__('general.developers')); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$feat('api_access')): ?> <span class="premium-badge"><?php echo e(__('settings.premium')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                </a>
                <a href="<?php echo e(route('two-factor.setup')); ?>" class="sidebar-nav-item" wire:navigate data-label="<?php echo e(__('general.security')); ?>">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span x-show="!collapsed" x-cloak><?php echo e(__('general.security')); ?></span>
                </a>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->isWorkspaceAdmin()): ?>
        <hr class="sidebar-nav-divider" x-show="!collapsed" x-cloak>

        <div class="sidebar-nav-section" x-data="{ open: <?php echo e(request()->routeIs('settings.*') || request()->routeIs('workspace.*') ? 'true' : 'false'); ?> }">
            <button class="sidebar-section-toggle" @click="open = !open" type="button" data-label="<?php echo e(__('workspace.team')); ?>">
                <i class="bi bi-people section-toggle-icon"></i>
                <span x-show="!collapsed" x-cloak><?php echo e(__('workspace.team')); ?></span>
                <i class="bi bi-chevron-down section-chevron" :class="open ? '' : 'collapsed'" x-show="!collapsed" x-cloak></i>
            </button>
            <div x-show="open" style="display:none">
                <a href="<?php echo e(route('settings.index')); ?>" class="sidebar-nav-item <?php echo e(request()->routeIs('settings.index') ? 'active' : ''); ?>" wire:navigate data-label="<?php echo e(__('workspace.settings')); ?>">
                    <i class="bi bi-building"></i>
                    <span x-show="!collapsed" x-cloak><?php echo e(__('workspace.settings')); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$feat('team_management')): ?> <span class="premium-badge"><?php echo e(__('settings.premium')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->workspaceHasPermission('workspace-role.view')): ?>
                <a href="<?php echo e(route('settings.workspace.roles.index')); ?>" class="sidebar-nav-item <?php echo e(request()->routeIs('settings.workspace.roles*') ? 'active' : ''); ?>" wire:navigate data-label="<?php echo e(__('workspace.roles')); ?>">
                    <i class="bi bi-shield-check"></i>
                    <span x-show="!collapsed" x-cloak><?php echo e(__('workspace.roles')); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$feat('roles_permissions')): ?> <span class="premium-badge"><?php echo e(__('settings.premium')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </nav>

    <hr class="sidebar-nav-divider" x-show="!collapsed" x-cloak>

    <nav class="sidebar-nav" x-show="!collapsed" x-cloak>
        <a href="<?php echo e(route('api.documentation')); ?>" class="sidebar-nav-item" target="_blank" data-label="<?php echo e(__('general.api_documentation')); ?>">
            <i class="bi bi-file-earmark-text"></i>
            <span><?php echo e(__('general.api_documentation')); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$feat('api_access')): ?> <span class="premium-badge"><?php echo e(__('settings.premium')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
        </a>
    </nav>

    <div class="sidebar-footer" x-data="profileDropdown()" @profile-dropdown-close.window="open = false">
        <button class="sidebar-nav-item profile-item" @click="toggle()" data-label="<?php echo e(__('general.profile')); ?>">
            <div class="profile-avatar">
                <?php echo e(substr(auth()->user()->name, 0, 1)); ?>

            </div>
            <div class="profile-info" x-show="!collapsed" x-cloak>
                <span class="profile-name"><?php echo e(auth()->user()->name); ?></span>
                <span class="profile-email"><?php echo e(auth()->user()->email); ?></span>
            </div>
            <i class="bi bi-chevron-up profile-chevron" :class="open && !collapsed ? 'rotate-180' : ''" x-show="!collapsed" x-cloak></i>
        </button>

        <?php echo $__env->make('layouts.partials._profile-dropdown-menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</aside>

<div class="sidebar-overlay" @click="closeSidebarMobile()"></div>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/layouts/partials/_user-sidebar.blade.php ENDPATH**/ ?>