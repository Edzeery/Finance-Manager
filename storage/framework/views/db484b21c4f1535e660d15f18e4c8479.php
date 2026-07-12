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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('super-admin.super_dashboard')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('super-admin.super_dashboard')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('super-admin.dashboard_desc')); ?> <?php $__env->endSlot(); ?>

    
    <div class="kpi-grid stagger-fade-in mb-4">
        <div class="kpi-card kpi-green">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-green">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <span class="kpi-card-trend up">
                    <i class="bi bi-arrow-up"></i><?php echo e(number_format(($kpis['total_revenue'] ? $kpis['revenue_this_month'] / max($kpis['total_revenue'], 1) * 100 : 0), 1)); ?>%
                </span>
            </div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.total_revenue')); ?></div>
            <div class="kpi-card-value"><?php echo e(number_format($kpis['total_revenue'], 0)); ?> <?php echo e(config('finance.currency_symbol')); ?></div>
            <div class="kpi-card-compare">
                <i class="bi bi-calendar3"></i> <?php echo e(__('super-admin.this_month')); ?>: <?php echo e(number_format($kpis['revenue_this_month'], 0)); ?> <?php echo e(config('finance.currency_symbol')); ?>

            </div>
        </div>

        <div class="kpi-card kpi-indigo">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-indigo">
                    <i class="bi bi-people-fill"></i>
                </div>
                <span class="kpi-card-trend up">
                    <i class="bi bi-person-check"></i><?php echo e($kpis['active_users']); ?> <?php echo e(__('general.active')); ?>

                </span>
            </div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.users')); ?></div>
            <div class="kpi-card-value"><?php echo e($kpis['total_users']); ?></div>
            <div class="kpi-card-compare">
                <i class="bi bi-shield-shaded"></i> <?php echo e($kpis['super_admins']); ?> <?php echo e(__('super-admin.super_admins')); ?>

            </div>
        </div>

        <div class="kpi-card kpi-blue">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-blue">
                    <i class="bi bi-building"></i>
                </div>
                <span class="kpi-card-trend up">
                    <i class="bi bi-check-circle"></i><?php echo e($kpis['active_workspaces']); ?> <?php echo e(__('general.active')); ?>

                </span>
            </div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.workspaces')); ?></div>
            <div class="kpi-card-value"><?php echo e($kpis['total_workspaces']); ?></div>
            <div class="kpi-card-compare">
                <i class="bi bi-building"></i> <?php echo e(__('super-admin.total')); ?>

            </div>
        </div>

        <div class="kpi-card kpi-green">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-green">
                    <i class="bi bi-credit-card"></i>
                </div>
                <span class="kpi-card-trend up">
                    <i class="bi bi-check-circle"></i><?php echo e($kpis['active_subscriptions']); ?> <?php echo e(__('super-admin.active')); ?>

                </span>
            </div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.active_subscriptions')); ?></div>
            <div class="kpi-card-value"><?php echo e($kpis['active_subscriptions']); ?></div>
            <div class="kpi-card-compare">
                <i class="bi bi-x-circle"></i> <?php echo e($kpis['canceled_subscriptions']); ?> <?php echo e(__('super-admin.canceled')); ?>

            </div>
        </div>

        <div class="kpi-card kpi-amber">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-amber">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <span class="kpi-card-trend <?php echo e($kpis['pending_amount'] > 0 ? 'down' : 'up'); ?>">
                    <i class="bi bi-cash"></i><?php echo e(number_format($kpis['pending_amount'], 0)); ?> <?php echo e(config('finance.currency_symbol')); ?>

                </span>
            </div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.pending_payments')); ?></div>
            <div class="kpi-card-value"><?php echo e($kpis['pending_payments']); ?></div>
            <div class="kpi-card-compare">
                <i class="bi bi-check-circle"></i> <?php echo e($kpis['completed_payments']); ?> <?php echo e(__('super-admin.completed')); ?>

            </div>
        </div>

        <div class="kpi-card kpi-purple">
            <div class="kpi-card-header">
                <div class="kpi-card-icon kpi-icon-purple">
                    <i class="bi bi-tags-fill"></i>
                </div>
                <span class="kpi-card-trend up"><?php echo e($kpis['total_coupon_uses']); ?> <?php echo e(__('super-admin.total_uses')); ?></span>
            </div>
            <div class="kpi-card-label"><?php echo e(__('super-admin.coupon_stats')); ?></div>
            <div class="kpi-card-value"><?php echo e($kpis['active_coupons']); ?> / <?php echo e($kpis['total_coupons']); ?></div>
            <div class="kpi-card-compare">
                <i class="bi bi-clock"></i> <?php echo e($kpis['expired_coupons']); ?> <?php echo e(__('super-admin.expired')); ?>

            </div>
        </div>
    </div>

    
    <div class="analytics-grid mb-4">
        
        <div class="section-card">
            <div class="section-card-header">
                <h5 class="d-flex align-items-center gap-2"><i class="bi bi-pie-chart-fill"></i><span><?php echo e(__('super-admin.subscriptions_by_plan')); ?></span></h5>
            </div>
            <div class="section-card-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $kpis['subscriptions_by_plan']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slug => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $total = max(array_sum($kpis['subscriptions_by_plan']->toArray()), 1);
                        $percent = round($count / $total * 100, 1);
                        $colors = ['#15b76c', '#6366F1', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'];
                        $color = $colors[$loop->index % count($colors)];
                    ?>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:10px;height:10px;border-radius:50%;background:<?php echo e($color); ?>;flex-shrink:0"></div>
                        <div style="flex:1;min-width:0">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size:13px;font-weight:500;color:var(--text)"><?php echo e(ucfirst($slug)); ?></span>
                                <span style="font-size:12px;font-weight:600;color:var(--text-muted)"><?php echo e($count); ?> (<?php echo e($percent); ?>%)</span>
                            </div>
                            <div style="height:6px;background:var(--border);border-radius:3px;overflow:hidden">
                                <div style="height:100%;width:<?php echo e($percent); ?>%;background:<?php echo e($color); ?>;border-radius:3px;transition:width 0.6s ease"></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state">
                        <div class="empty-icon" style="background:var(--border);color:var(--text-muted)">
                            <i class="bi bi-pie-chart"></i>
                        </div>
                        <h4><?php echo e(__('general.no_data')); ?></h4>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="section-card">
            <div class="section-card-header">
                <h5 class="d-flex align-items-center gap-2"><i class="bi bi-bar-chart-fill"></i><span><?php echo e(__('super-admin.revenue_by_gateway')); ?></span></h5>
            </div>
            <div class="section-card-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $kpis['revenue_by_gateway']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway => $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $grandTotal = max(array_sum($kpis['revenue_by_gateway']->toArray()), 1);
                        $pct = round($total / $grandTotal * 100, 1);
                        $colors = ['#15b76c', '#6366F1', '#3B82F6', '#F59E0B', '#8B5CF6', '#EF4444'];
                        $color = $colors[$loop->index % count($colors)];
                        $icons = ['chargily' => 'bi-credit-card-2-front', 'baridimob' => 'bi-phone', 'redotpay' => 'bi-currency-bitcoin', 'wise_manual' => 'bi-bank', 'cash' => 'bi-cash', 'paypal' => 'bi-paypal'];
                    ?>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:32px;height:32px;border-radius:8px;background:var(--bg);display:flex;align-items:center;justify-content:center;font-size:14px;color:<?php echo e($color); ?>;flex-shrink:0">
                            <i class="bi <?php echo e($icons[$gateway] ?? 'bi-credit-card'); ?>"></i>
                        </div>
                        <div style="flex:1;min-width:0">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size:13px;font-weight:500;color:var(--text)"><?php echo e(__("super-admin.{$gateway}")); ?></span>
                                <span style="font-size:12px;font-weight:600;color:var(--text-muted)"><?php echo e(number_format($total, 0)); ?> <?php echo e(config('finance.currency_symbol')); ?></span>
                            </div>
                            <div style="height:5px;background:var(--border);border-radius:3px;overflow:hidden">
                                <div style="height:100%;width:<?php echo e($pct); ?>%;background:<?php echo e($color); ?>;border-radius:3px;transition:width 0.6s ease"></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state">
                        <div class="empty-icon" style="background:var(--border);color:var(--text-muted)">
                            <i class="bi bi-bar-chart"></i>
                        </div>
                        <h4><?php echo e(__('general.no_data')); ?></h4>
                    </div>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kpis['recent_payments']->count()): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $kpis['recent_payments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="activity-item px-4">
                                <div class="activity-icon" style="background:var(--accent-light);color:var(--accent)">
                                    <i class="bi bi-arrow-down-left"></i>
                                </div>
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
                        <div class="empty-state">
                            <div class="empty-icon" style="background:var(--border);color:var(--text-muted)">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <h4><?php echo e(__('general.no_data')); ?></h4>
                        </div>
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
                        <a href="<?php echo e(route('super.admin.users.index')); ?>" class="quick-action-card">
                            <i class="bi bi-people-fill"></i>
                            <span><?php echo e(__('super-admin.users')); ?></span>
                        </a>
                        <a href="<?php echo e(route('super.admin.workspaces.index')); ?>" class="quick-action-card">
                            <i class="bi bi-building"></i>
                            <span><?php echo e(__('super-admin.workspaces')); ?></span>
                        </a>
                        <a href="<?php echo e(route('super.admin.subscriptions.index')); ?>" class="quick-action-card">
                            <i class="bi bi-credit-card"></i>
                            <span><?php echo e(__('super-admin.subscriptions')); ?></span>
                        </a>
                        <a href="<?php echo e(route('super.admin.payments.index')); ?>" class="quick-action-card">
                            <i class="bi bi-cash-coin"></i>
                            <span><?php echo e(__('super-admin.payments')); ?></span>
                        </a>
                        <a href="<?php echo e(route('super.admin.plans.index')); ?>" class="quick-action-card">
                            <i class="bi bi-box"></i>
                            <span><?php echo e(__('super-admin.plans')); ?></span>
                        </a>
                        <a href="<?php echo e(route('super.admin.settings.index')); ?>" class="quick-action-card">
                            <i class="bi bi-gear-fill"></i>
                            <span><?php echo e(__('super-admin.settings')); ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal11b520df80702cb1ab8718e178b6ffa6)): ?>
<?php $attributes = $__attributesOriginal11b520df80702cb1ab8718e178b6ffa6; ?>
<?php unset($__attributesOriginal11b520df80702cb1ab8718e178b6ffa6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal11b520df80702cb1ab8718e178b6ffa6)): ?>
<?php $component = $__componentOriginal11b520df80702cb1ab8718e178b6ffa6; ?>
<?php unset($__componentOriginal11b520df80702cb1ab8718e178b6ffa6); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\super-admin\dashboard.blade.php ENDPATH**/ ?>