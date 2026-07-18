<?php if (isset($component)) { $__componentOriginal11b520df80702cb1ab8718e178b6ffa6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal11b520df80702cb1ab8718e178b6ffa6 = $attributes; } ?>
<?php $component = App\View\Components\SuperAdminLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('super-admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SuperAdminLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php
        $baseCur = $data['base_currency'] ?? \App\Services\CurrencyHelper::baseCurrency();
        $baseSym = \App\Services\CurrencyHelper::symbol($baseCur);
        $periods = [
            'all_time' => ['label_key' => 'filters.all_time'],
            'this_month' => ['label_key' => 'filters.this_month'],
            'last_month' => ['label_key' => 'filters.last_month'],
            'last_7_days' => ['label_key' => 'filters.last_7_days'],
            'custom' => ['label_key' => 'filters.custom'],
        ];
    ?>
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('super-admin.super_dashboard')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('super-admin.super_dashboard')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('super-admin.dashboard_desc')); ?> <?php $__env->endSlot(); ?>

    <?php if (isset($component)) { $__componentOriginal526982350b860bbb0ef3834fb35dd9e5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal526982350b860bbb0ef3834fb35dd9e5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-tabs','data' => ['tabs' => [
        'overview' => ['label' => __('super-admin.overview'), 'icon' => 'bi-grid-1x2-fill'],
        'revenue' => ['label' => __('super-admin.revenue'), 'icon' => 'bi-currency-dollar'],
        'subscriptions' => ['label' => __('super-admin.subscriptions'), 'icon' => 'bi-credit-card'],
        'team' => ['label' => __('super-admin.team_performance'), 'icon' => 'bi-people-fill'],
    ],'current' => ''.e($data['current_tab']).'','keyParam' => 'tab','preserve' => ['period','start_date','end_date','gateway','plan_id','member_id']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-tabs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tabs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        'overview' => ['label' => __('super-admin.overview'), 'icon' => 'bi-grid-1x2-fill'],
        'revenue' => ['label' => __('super-admin.revenue'), 'icon' => 'bi-currency-dollar'],
        'subscriptions' => ['label' => __('super-admin.subscriptions'), 'icon' => 'bi-credit-card'],
        'team' => ['label' => __('super-admin.team_performance'), 'icon' => 'bi-people-fill'],
    ]),'current' => ''.e($data['current_tab']).'','keyParam' => 'tab','preserve' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['period','start_date','end_date','gateway','plan_id','member_id'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal526982350b860bbb0ef3834fb35dd9e5)): ?>
<?php $attributes = $__attributesOriginal526982350b860bbb0ef3834fb35dd9e5; ?>
<?php unset($__attributesOriginal526982350b860bbb0ef3834fb35dd9e5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal526982350b860bbb0ef3834fb35dd9e5)): ?>
<?php $component = $__componentOriginal526982350b860bbb0ef3834fb35dd9e5; ?>
<?php unset($__componentOriginal526982350b860bbb0ef3834fb35dd9e5); ?>
<?php endif; ?>

    <div class="mb-4">
        <?php if (isset($component)) { $__componentOriginal45820a29a8741c05f6b6338dfa1de322 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45820a29a8741c05f6b6338dfa1de322 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.date-filter-bar','data' => ['periods' => $periods,'currentPeriod' => ''.e($data['period']).'','startDate' => ''.e($data['start_date']).'','endDate' => ''.e($data['end_date']).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('date-filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['periods' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($periods),'currentPeriod' => ''.e($data['period']).'','startDate' => ''.e($data['start_date']).'','endDate' => ''.e($data['end_date']).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal45820a29a8741c05f6b6338dfa1de322)): ?>
<?php $attributes = $__attributesOriginal45820a29a8741c05f6b6338dfa1de322; ?>
<?php unset($__attributesOriginal45820a29a8741c05f6b6338dfa1de322); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal45820a29a8741c05f6b6338dfa1de322)): ?>
<?php $component = $__componentOriginal45820a29a8741c05f6b6338dfa1de322; ?>
<?php unset($__componentOriginal45820a29a8741c05f6b6338dfa1de322); ?>
<?php endif; ?>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($data['current_tab'] === 'overview'): ?>
    <div class="kpi-grid stagger-fade-in mb-4">
        <div class="kpi-card kpi-green">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-green"><i class="bi bi-currency-dollar"></i></div>
                <span class="kpi-card-trend up">
                    <i class="bi bi-arrow-up"></i><?php echo e(number_format(($data['total_revenue'] ? $data['revenue_this_month'] / max($data['total_revenue'], 1) * 100 : 0), 1)); ?>%
                </span>
            </div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.total_revenue')); ?></div>
            <div class="kpi-card-value"><?php echo e(number_format($data['total_revenue'], 0)); ?> <?php echo e($baseSym); ?></div>
            <div class="kpi-card-compare">
                <i class="bi bi-cash-coin"></i> <?php echo e(__('super-admin.net_revenue')); ?>: <?php echo e(number_format($data['net_revenue'], 0)); ?> <?php echo e($baseSym); ?>

            </div>
        </div>

        <div class="kpi-card kpi-indigo">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-indigo"><i class="bi bi-people-fill"></i></div>
                <span class="kpi-card-trend up"><i class="bi bi-person-check"></i><?php echo e($data['active_users']); ?> <?php echo e(__('general.active')); ?></span>
            </div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.users')); ?></div>
            <div class="kpi-card-value"><?php echo e($data['total_users']); ?></div>
            <div class="kpi-card-compare">
                <i class="bi bi-circle-fill text-success" style="font-size:0.5rem"></i> <?php echo e($data['online_users']); ?> <?php echo e(__('general.online')); ?>

                &nbsp;&middot;&nbsp;
                <i class="bi bi-shield-shaded"></i> <?php echo e($data['super_admins'] ?? '—'); ?> <?php echo e(__('super-admin.super_admins')); ?>

            </div>
        </div>

        <div class="kpi-card kpi-blue">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-blue"><i class="bi bi-building"></i></div>
                    <span class="kpi-card-trend up"><?php if (isset($component)) { $__componentOriginal916418750eca0f0299436c8f1a00baec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal916418750eca0f0299436c8f1a00baec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-icon','data' => ['domain' => 'general','status' => 'success','set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'success','set' => 'bi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $attributes = $__attributesOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__attributesOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $component = $__componentOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__componentOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?> <?php echo e($data['active_workspaces']); ?> <?php echo e(__('general.active')); ?></span>
            </div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.workspaces')); ?></div>
            <div class="kpi-card-value"><?php echo e($data['total_workspaces']); ?></div>
            <div class="kpi-card-compare">
                <i class="bi bi-building"></i> <?php echo e(__('super-admin.total')); ?>

            </div>
        </div>

        <div class="kpi-card kpi-green">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-green"><i class="bi bi-credit-card"></i></div>
                    <span class="kpi-card-trend up"><?php if (isset($component)) { $__componentOriginal916418750eca0f0299436c8f1a00baec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal916418750eca0f0299436c8f1a00baec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-icon','data' => ['domain' => 'general','status' => 'success','set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'success','set' => 'bi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $attributes = $__attributesOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__attributesOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $component = $__componentOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__componentOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?> <?php echo e($data['active_subscriptions']); ?> <?php echo e(__('super-admin.active')); ?></span>
            </div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.active_subscriptions')); ?></div>
            <div class="kpi-card-value"><?php echo e($data['active_subscriptions']); ?></div>
            <div class="kpi-card-compare">
                <?php if (isset($component)) { $__componentOriginal916418750eca0f0299436c8f1a00baec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal916418750eca0f0299436c8f1a00baec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-icon','data' => ['domain' => 'general','status' => 'danger','set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'danger','set' => 'bi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $attributes = $__attributesOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__attributesOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $component = $__componentOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__componentOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?> <?php echo e($data['canceled_subscriptions']); ?> <?php echo e(__('super-admin.canceled')); ?>

            </div>
        </div>

        <div class="kpi-card kpi-amber">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-amber"><i class="bi bi-hourglass-split"></i></div>
                <span class="kpi-card-trend <?php echo e($data['pending_amount'] > 0 ? 'down' : 'up'); ?>">
                    <i class="bi bi-cash"></i><?php echo e(number_format($data['pending_amount'], 0)); ?> <?php echo e($baseSym); ?>

                </span>
            </div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.pending_payments')); ?></div>
            <div class="kpi-card-value"><?php echo e($data['pending_payments']); ?></div>
            <div class="kpi-card-compare">
                <?php if (isset($component)) { $__componentOriginal916418750eca0f0299436c8f1a00baec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal916418750eca0f0299436c8f1a00baec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-icon','data' => ['domain' => 'general','status' => 'success','set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'success','set' => 'bi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $attributes = $__attributesOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__attributesOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $component = $__componentOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__componentOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?> <?php echo e($data['completed_payments']); ?> <?php echo e(__('super-admin.completed')); ?>

            </div>
        </div>

        <div class="kpi-card kpi-purple">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-purple"><i class="bi bi-tags-fill"></i></div>
                <span class="kpi-card-trend up"><?php echo e($data['total_coupon_uses'] ?? '—'); ?> <?php echo e(__('super-admin.total_uses')); ?></span>
            </div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.coupon_stats')); ?></div>
            <div class="kpi-card-value"><?php echo e($data['active_coupons'] ?? '—'); ?> / <?php echo e($data['total_coupons'] ?? '—'); ?></div>
            <div class="kpi-card-compare">
                <?php if (isset($component)) { $__componentOriginal916418750eca0f0299436c8f1a00baec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal916418750eca0f0299436c8f1a00baec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-icon','data' => ['domain' => 'general','status' => 'expired','set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'expired','set' => 'bi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $attributes = $__attributesOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__attributesOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $component = $__componentOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__componentOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?> <?php echo e($data['expired_coupons'] ?? '—'); ?> <?php echo e(__('super-admin.expired')); ?>

            </div>
        </div>
    </div>

    <div class="analytics-grid mb-4">
        <div class="section-card">
            <div class="section-card-header">
                <h5 class="d-flex align-items-center gap-2"><i class="bi bi-pie-chart-fill"></i><span><?php echo e(__('super-admin.subscriptions_by_plan')); ?></span></h5>
            </div>
            <div class="section-card-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = ($data['subscriptions_by_plan'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slug => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $total = max(array_sum($data['subscriptions_by_plan'] ?? []), 1);
                        $colors = ['#15b76c', '#6366F1', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'];
                    ?>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:10px;height:10px;border-radius:50%;background:<?php echo e($colors[$loop->index % count($colors)]); ?>;flex-shrink:0"></div>
                        <div style="flex:1;min-width:0">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size:13px;font-weight:500;color:var(--text)"><?php echo e(ucfirst($slug)); ?></span>
                                <span style="font-size:12px;font-weight:600;color:var(--text-muted)"><?php echo e($count); ?> (<?php echo e(round($count / $total * 100, 1)); ?>%)</span>
                            </div>
                            <div style="height:6px;background:var(--border);border-radius:3px;overflow:hidden">
                                <div style="height:100%;width:<?php echo e($count / $total * 100); ?>%;background:<?php echo e($colors[$loop->index % count($colors)]); ?>;border-radius:3px"></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'bi bi-pie-chart','title' => __('general.no_data')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi bi-pie-chart','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('general.no_data'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="section-card">
            <div class="section-card-header">
                <h5 class="d-flex align-items-center gap-2"><i class="bi bi-bar-chart-fill"></i><span><?php echo e(__('super-admin.revenue_by_gateway')); ?></span></h5>
            </div>
            <div class="section-card-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = ($data['revenue_by_gateway'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway => $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $grandTotal = max(array_sum($data['revenue_by_gateway'] ?? []), 1);
                        $colors = ['#15b76c', '#6366F1', '#3B82F6', '#F59E0B', '#8B5CF6', '#EF4444'];
                        $icons = ['chargily' => 'bi-credit-card-2-front', 'baridimob' => 'bi-phone', 'redotpay' => 'bi-currency-bitcoin', 'wise_manual' => 'bi-bank', 'cash' => 'bi-cash', 'paypal' => 'bi-paypal'];
                    ?>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:32px;height:32px;border-radius:8px;background:var(--bg);display:flex;align-items:center;justify-content:center;font-size:14px;color:<?php echo e($colors[$loop->index % count($colors)]); ?>;flex-shrink:0">
                            <i class="bi <?php echo e($icons[$gateway] ?? 'bi-credit-card'); ?>"></i>
                        </div>
                        <div style="flex:1;min-width:0">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size:13px;font-weight:500;color:var(--text)"><?php echo e(__("super-admin.{$gateway}")); ?></span>
                                <span style="font-size:12px;font-weight:600;color:var(--text-muted)"><?php echo e(number_format($total, 0)); ?> <?php echo e($baseSym); ?></span>
                            </div>
                            <div style="height:5px;background:var(--border);border-radius:3px;overflow:hidden">
                                <div style="height:100%;width:<?php echo e($total / $grandTotal * 100); ?>%;background:<?php echo e($colors[$loop->index % count($colors)]); ?>;border-radius:3px"></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'bi bi-bar-chart','title' => __('general.no_data')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi bi-bar-chart','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('general.no_data'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="section-card">
                <div class="section-card-header">
                    <h5 class="d-flex align-items-center gap-2"><i class="bi bi-clock-history"></i><span><?php echo e(__('super-admin.recent_payments')); ?></span></h5>
                </div>
                <div class="section-card-body p-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($data['recent_payments'] ?? collect())->count()): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $data['recent_payments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="activity-item px-4">
                                <div class="activity-icon" style="background:var(--accent-light);color:var(--accent)"><i class="bi bi-arrow-down-left"></i></div>
                                <div class="activity-content">
                                    <div class="activity-text">
                                        <strong><?php echo e($payment->workspace?->name ?? '—'); ?></strong>
                                        <?php echo e(__('general.paid')); ?>

                                        <strong><?php echo e(number_format($payment->amount, 2)); ?> <?php echo e($payment->currency); ?></strong>
                                    </div>
                                    <div class="activity-meta">
                                        <span><?php echo e($payment->subscription?->plan?->name ?? '—'); ?></span>
                                        <span class="activity-dot"></span>
                                        <span><?php echo e($payment->paid_at?->diffForHumans() ?? $payment->created_at->diffForHumans()); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'bi bi-clock-history','title' => __('general.no_data')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi bi-clock-history','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('general.no_data'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="section-card">
                <div class="section-card-header">
                    <h5 class="d-flex align-items-center gap-2"><i class="bi bi-box-arrow-in-right"></i><span><?php echo e(__('super-admin.quick_actions')); ?></span></h5>
                </div>
                <div class="section-card-body">
                    <div class="quick-actions-grid">
                        <a href="<?php echo e(route('super.admin.users.index')); ?>" class="quick-action-card"><i class="bi bi-people-fill"></i><span><?php echo e(__('super-admin.users')); ?></span></a>
                        <a href="<?php echo e(route('super.admin.workspaces.index')); ?>" class="quick-action-card"><i class="bi bi-building"></i><span><?php echo e(__('super-admin.workspaces')); ?></span></a>
                        <a href="<?php echo e(route('super.admin.subscriptions.index')); ?>" class="quick-action-card"><i class="bi bi-credit-card"></i><span><?php echo e(__('super-admin.subscriptions')); ?></span></a>
                        <a href="<?php echo e(route('super.admin.payments.index')); ?>" class="quick-action-card"><i class="bi bi-cash-coin"></i><span><?php echo e(__('super-admin.payments')); ?></span></a>
                        <a href="<?php echo e(route('super.admin.plans.index')); ?>" class="quick-action-card"><i class="bi bi-box"></i><span><?php echo e(__('super-admin.plans')); ?></span></a>
                        <a href="<?php echo e(route('super.admin.settings.index')); ?>" class="quick-action-card"><i class="bi bi-gear-fill"></i><span><?php echo e(__('super-admin.settings')); ?></span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php elseif($data['current_tab'] === 'revenue'): ?>
    <?php
        $revGW = $data['gateway_keys'] ?? [];
        $revPlans = $data['plan_options'] ?? [];
    ?>
    <div class="kpi-grid stagger-fade-in mb-4">
        <div class="kpi-card kpi-green">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-green"><i class="bi bi-currency-dollar"></i></div>
            </div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.total_revenue')); ?></div>
            <div class="kpi-card-value"><?php echo e(number_format($data['gross'], 0)); ?> <?php echo e($baseSym); ?></div>
        </div>
        <div class="kpi-card kpi-blue">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-blue"><i class="bi bi-cash-coin"></i></div>
            </div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.net_revenue')); ?></div>
            <div class="kpi-card-value"><?php echo e(number_format($data['net'], 0)); ?> <?php echo e($baseSym); ?></div>
            <div class="kpi-card-compare"><?php echo e(__('super-admin.total_fees')); ?>: <?php echo e(number_format($data['fees'], 0)); ?> <?php echo e($baseSym); ?></div>
        </div>
        <div class="kpi-card kpi-amber">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-amber"><i class="bi bi-arrow-uturn-left"></i></div>
            </div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.refunded_amount')); ?></div>
            <div class="kpi-card-value"><?php echo e(number_format($data['refunded'], 0)); ?> <?php echo e($baseSym); ?></div>
            <div class="kpi-card-compare"><?php echo e(__('super-admin.refund_rate')); ?>: <?php echo e($data['refund_rate']); ?>%</div>
        </div>
        <div class="kpi-card kpi-purple">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-purple"><i class="bi bi-graph-up-arrow"></i></div>
            </div>
            <div class="kpi-card-label">MRR</div>
            <div class="kpi-card-value"><?php echo e(number_format($data['mrr'], 0)); ?> <?php echo e($baseSym); ?></div>
            <div class="kpi-card-compare">ARR: <?php echo e(number_format($data['arr'], 0)); ?> <?php echo e($baseSym); ?></div>
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
        <form method="GET" action="<?php echo e(route('super.admin.dashboard')); ?>" class="d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="tab" value="revenue">
            <input type="hidden" name="period" value="<?php echo e($data['period']); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($revGW)): ?>
            <select name="gateway" class="form-control" style="width:auto;min-width:130px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                <option value=""><?php echo e(__('general.all_methods')); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $revGW; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($gw); ?>" <?php echo e(request('gateway') === $gw ? 'selected' : ''); ?>><?php echo e(__("super-admin.{$gw}")); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($revPlans)): ?>
            <select name="plan_id" class="form-control" style="width:auto;min-width:130px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                <option value=""><?php echo e(__('general.all_plans')); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $revPlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($id); ?>" <?php echo e(request('plan_id') == $id ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer"><?php echo e(__('general.filter')); ?></button>
        </form>
    </div>

    <div class="analytics-grid mb-4">
        <div class="section-card">
            <div class="section-card-header">
                <h5 class="d-flex align-items-center gap-2"><i class="bi bi-bar-chart-fill"></i><span><?php echo e(__('super-admin.monthly_revenue')); ?></span></h5>
            </div>
            <div class="section-card-body">
                <div x-data="superAdminChart('revenueChart', 'line', {
                    series: [
                        { name: '<?php echo e(__("super-admin.total_revenue")); ?>', data: <?php echo e(json_encode($data['monthly_gross'] ?? [])); ?> },
                        { name: '<?php echo e(__("super-admin.net_revenue")); ?>', data: <?php echo e(json_encode($data['monthly_net'] ?? [])); ?> },
                    ],
                    xaxis: { categories: <?php echo e(json_encode($data['monthly_labels'] ?? [])); ?> },
                    colors: ['#15b76c', '#3B82F6'],
                })">
                    <div style="min-height:300px"></div>
                </div>
            </div>
        </div>

        <div class="section-card">
            <div class="section-card-header">
                <h5 class="d-flex align-items-center gap-2"><i class="bi bi-pie-chart-fill"></i><span><?php echo e(__('super-admin.revenue_by_gateway')); ?></span></h5>
            </div>
            <div class="section-card-body">
                <div x-data="superAdminChart('revGatewayChart', 'donut', {
                    series: <?php echo e(json_encode(array_values($data['by_gateway'] ?? []))); ?>,
                    labels: <?php echo e(json_encode(collect($data['by_gateway'] ?? [])->keys()->map(fn($k) => __("super-admin.{$k}"))->toArray())); ?>,
                    colors: ['#15b76c', '#6366F1', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'],
                })">
                    <div style="min-height:280px"></div>
                </div>
            </div>
        </div>

        <div class="section-card">
            <div class="section-card-header">
                <h5 class="d-flex align-items-center gap-2"><i class="bi bi-layers-fill"></i><span><?php echo e(__('super-admin.revenue_by_plan')); ?></span></h5>
            </div>
            <div class="section-card-body">
                <div x-data="superAdminChart('revPlanChart', 'bar', {
                    series: [{ name: '<?php echo e(__("super-admin.revenue")); ?>', data: <?php echo e(json_encode(array_values($data['by_plan'] ?? []))); ?> }],
                    xaxis: { categories: <?php echo e(json_encode(array_keys($data['by_plan'] ?? []))); ?> },
                    colors: ['#6366F1'],
                })">
                    <div style="min-height:280px"></div>
                </div>
            </div>
        </div>
    </div>

    
    <?php elseif($data['current_tab'] === 'subscriptions'): ?>
    <?php $subPlans = $data['plan_options'] ?? []; ?>
    <div class="kpi-grid stagger-fade-in mb-4">
        <div class="kpi-card kpi-green">
            <div class="kpi-card-header"><div class="kpi-card-icon kpi-icon-green"><?php if (isset($component)) { $__componentOriginal916418750eca0f0299436c8f1a00baec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal916418750eca0f0299436c8f1a00baec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-icon','data' => ['domain' => 'general','status' => 'success','set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'success','set' => 'bi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $attributes = $__attributesOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__attributesOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $component = $__componentOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__componentOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?></div></div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.active')); ?></div>
            <div class="kpi-card-value"><?php echo e($data['active']); ?></div>
        </div>
        <div class="kpi-card kpi-amber">
            <div class="kpi-card-header"><div class="kpi-card-icon kpi-icon-amber"><i class="bi bi-hourglass-split"></i></div></div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.suspended')); ?></div>
            <div class="kpi-card-value"><?php echo e($data['suspended']); ?></div>
        </div>
        <div class="kpi-card kpi-red" style="--kpi-accent:#EF4444">
            <div class="kpi-card-header"><div class="kpi-card-icon" style="background:rgba(239,68,68,0.12);color:#EF4444"><?php if (isset($component)) { $__componentOriginal916418750eca0f0299436c8f1a00baec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal916418750eca0f0299436c8f1a00baec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-icon','data' => ['domain' => 'general','status' => 'danger','set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'danger','set' => 'bi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $attributes = $__attributesOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__attributesOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $component = $__componentOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__componentOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?></div></div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.canceled')); ?></div>
            <div class="kpi-card-value"><?php echo e($data['canceled']); ?></div>
        </div>
        <div class="kpi-card" style="--kpi-accent:#9ca3af">
            <div class="kpi-card-header"><div class="kpi-card-icon" style="background:rgba(156,163,175,0.12);color:#9ca3af"><?php if (isset($component)) { $__componentOriginal916418750eca0f0299436c8f1a00baec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal916418750eca0f0299436c8f1a00baec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-icon','data' => ['domain' => 'general','status' => 'expired','set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'expired','set' => 'bi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $attributes = $__attributesOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__attributesOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $component = $__componentOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__componentOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?></div></div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.expired')); ?></div>
            <div class="kpi-card-value"><?php echo e($data['expired']); ?></div>
        </div>
        <div class="kpi-card kpi-purple">
            <div class="kpi-card-header"><div class="kpi-card-icon kpi-icon-purple"><i class="bi bi-percent"></i></div></div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.churn_rate')); ?></div>
            <div class="kpi-card-value"><?php echo e($data['churn_rate']); ?>%</div>
        </div>
        <div class="kpi-card kpi-blue">
            <div class="kpi-card-header"><div class="kpi-card-icon kpi-icon-blue"><?php if (isset($component)) { $__componentOriginal916418750eca0f0299436c8f1a00baec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal916418750eca0f0299436c8f1a00baec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-icon','data' => ['domain' => 'general','status' => 'pending','set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'pending','set' => 'bi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $attributes = $__attributesOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__attributesOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal916418750eca0f0299436c8f1a00baec)): ?>
<?php $component = $__componentOriginal916418750eca0f0299436c8f1a00baec; ?>
<?php unset($__componentOriginal916418750eca0f0299436c8f1a00baec); ?>
<?php endif; ?></div></div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.avg_lifetime')); ?></div>
            <div class="kpi-card-value"><?php echo e($data['avg_lifetime_days']); ?></div>
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
        <form method="GET" action="<?php echo e(route('super.admin.dashboard')); ?>" class="d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="tab" value="subscriptions">
            <input type="hidden" name="period" value="<?php echo e($data['period']); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($subPlans)): ?>
            <select name="plan_id" class="form-control" style="width:auto;min-width:130px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                <option value=""><?php echo e(__('general.all_plans')); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subPlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($id); ?>" <?php echo e(request('plan_id') == $id ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer"><?php echo e(__('general.filter')); ?></button>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="section-card">
                <div class="section-card-header">
                    <h5 class="d-flex align-items-center gap-2"><i class="bi bi-pie-chart-fill"></i><span><?php echo e(__('super-admin.subscriptions_by_plan')); ?></span></h5>
                </div>
                <div class="section-card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = ($data['by_plan'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slug => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $total = max(array_sum($data['by_plan'] ?? []), 1);
                            $colors = ['#15b76c', '#6366F1', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'];
                        ?>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="width:10px;height:10px;border-radius:50%;background:<?php echo e($colors[$loop->index % count($colors)]); ?>;flex-shrink:0"></div>
                            <div style="flex:1;min-width:0">
                                <div class="d-flex justify-content-between mb-1">
                                    <span style="font-size:13px;font-weight:500;color:var(--text)"><?php echo e(ucfirst($slug)); ?></span>
                                    <span style="font-size:12px;font-weight:600;color:var(--text-muted)"><?php echo e($count); ?> (<?php echo e(round($count / $total * 100, 1)); ?>%)</span>
                                </div>
                                <div style="height:6px;background:var(--border);border-radius:3px;overflow:hidden">
                                    <div style="height:100%;width:<?php echo e($count / $total * 100); ?>%;background:<?php echo e($colors[$loop->index % count($colors)]); ?>;border-radius:3px"></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'bi bi-pie-chart','title' => __('general.no_data')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi bi-pie-chart','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('general.no_data'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="section-card">
                <div class="section-card-header">
                    <h5 class="d-flex align-items-center gap-2"><i class="bi bi-bar-chart-fill"></i><span><?php echo e(__('super-admin.subscription_status')); ?></span></h5>
                </div>
                <div class="section-card-body">
                    <div x-data="superAdminChart('subStatusChart', 'donut', {
                        series: [<?php echo e($data['active']); ?>, <?php echo e($data['canceled']); ?>, <?php echo e($data['expired']); ?>, <?php echo e($data['suspended']); ?>],
                        labels: ['<?php echo e(__("super-admin.active")); ?>', '<?php echo e(__("super-admin.canceled")); ?>', '<?php echo e(__("super-admin.expired")); ?>', '<?php echo e(__("super-admin.suspended")); ?>'],
                        colors: ['#15b76c', '#EF4444', '#9ca3af', '#F59E0B'],
                    })">
                        <div style="min-height:280px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php elseif($data['current_tab'] === 'team'): ?>
    <?php
        $memberOpts = $data['member_options'] ?? [];
        $members = $data['members'] ?? [];
    ?>

    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
        <form method="GET" action="<?php echo e(route('super.admin.dashboard')); ?>" class="d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="tab" value="team">
            <input type="hidden" name="period" value="<?php echo e($data['period']); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($memberOpts)): ?>
            <select name="member_id" class="form-control" style="width:auto;min-width:180px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                <option value=""><?php echo e(__('super-admin.all_team')); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $memberOpts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($id); ?>" <?php echo e(request('member_id') == $id ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer"><?php echo e(__('general.filter')); ?></button>
        </form>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($members)): ?>
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="section-card">
                <div class="section-card-header">
                    <h5 class="d-flex align-items-center gap-2"><i class="bi bi-people-fill"></i><span><?php echo e(__('super-admin.team_members_performance')); ?></span></h5>
                </div>
                <div class="section-card-body p-0">
                    <div class="table-responsive">
                        <table class="data-table mb-0">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('super-admin.member')); ?></th>
                                    <th><?php echo e(__('general.roles')); ?></th>
                                    <th><?php echo e(__('super-admin.verifications')); ?></th>
                                    <th><?php echo e(__('super-admin.verified_amount')); ?></th>
                                    <th><?php echo e(__('super-admin.refunds')); ?></th>
                                    <th><?php echo e(__('super-admin.refunded_amount_short')); ?></th>
                                    <th><?php echo e(__('super-admin.last_active')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:8px">
                                            <div style="width:32px;height:32px;border-radius:50%;background:var(--accent-light);color:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:600;font-size:13px">
                                                <?php echo e(substr($member['name'], 0, 2)); ?>

                                            </div>
                                            <div>
                                                <div style="font-weight:500;font-size:13px"><?php echo e($member['name']); ?></div>
                                                <div style="font-size:11px;color:var(--text-muted)"><?php echo e($member['email']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge" style="font-size:10px;background:var(--bg-subtle);color:var(--text);padding:2px 8px;border-radius:4px"><?php echo e($member['role']); ?></span></td>
                                    <td style="font-weight:600"><?php echo e($member['verifications_count']); ?></td>
                                    <td><?php echo e(number_format($member['verifications_total'], 0)); ?> <?php echo e($baseSym); ?></td>
                                    <td><?php echo e($member['refunds_count']); ?></td>
                                    <td><?php echo e(number_format($member['refunds_total'], 0)); ?> <?php echo e($baseSym); ?></td>
                                    <td style="font-size:12px;color:var(--text-muted)">
                                        <?php echo e($member['last_activity'] ? $member['last_activity']->diffForHumans() : '—'); ?>

                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="section-card">
                <div class="section-card-header">
                    <h5 class="d-flex align-items-center gap-2"><i class="bi bi-bar-chart-fill"></i><span><?php echo e(__('super-admin.team_comparison')); ?></span></h5>
                </div>
                <div class="section-card-body">
                    <div x-data="superAdminChart('teamChart', 'bar', {
                        series: [
                            { name: '<?php echo e(__("super-admin.verifications")); ?>', data: <?php echo e(json_encode(collect($members)->pluck('verifications_count')->toArray())); ?> },
                            { name: '<?php echo e(__("super-admin.refunds")); ?>', data: <?php echo e(json_encode(collect($members)->pluck('refunds_count')->toArray())); ?> },
                        ],
                        xaxis: { categories: <?php echo e(json_encode(collect($members)->pluck('name')->toArray())); ?> },
                        colors: ['#15b76c', '#EF4444'],
                    })">
                        <div style="min-height:300px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($data['roles'])): ?>
    <div class="section-card">
        <div class="section-card-header">
            <h5 class="d-flex align-items-center gap-2"><i class="bi bi-shield-check"></i><span><?php echo e(__('super-admin.team_roles')); ?></span></h5>
        </div>
        <div class="section-card-body">
            <div class="d-flex flex-wrap gap-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $data['roles']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="flex:1;min-width:140px;padding:16px;background:var(--bg);border-radius:var(--radius-sm);text-align:center">
                    <div style="font-size:24px;font-weight:700;color:var(--text)"><?php echo e($role['members_count']); ?></div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:4px"><?php echo e($role['name']); ?></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php else: ?>
    <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'bi bi-people','title' => __('general.no_data')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi bi-people','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('general.no_data'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal11b520df80702cb1ab8718e178b6ffa6)): ?>
<?php $attributes = $__attributesOriginal11b520df80702cb1ab8718e178b6ffa6; ?>
<?php unset($__attributesOriginal11b520df80702cb1ab8718e178b6ffa6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal11b520df80702cb1ab8718e178b6ffa6)): ?>
<?php $component = $__componentOriginal11b520df80702cb1ab8718e178b6ffa6; ?>
<?php unset($__componentOriginal11b520df80702cb1ab8718e178b6ffa6); ?>
<?php endif; ?><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\super-admin\dashboard.blade.php ENDPATH**/ ?>