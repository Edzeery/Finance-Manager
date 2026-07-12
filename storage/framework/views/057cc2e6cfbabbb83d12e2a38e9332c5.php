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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('super-admin.subscriptions')); ?> #<?php echo e($subscription->id); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('super-admin.subscriptions')); ?> #<?php echo e($subscription->id); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e($subscription->workspace?->name ?? __('general.unknown')); ?> &mdash; <?php echo e($subscription->plan?->name ?? '—'); ?> <?php $__env->endSlot(); ?>

    <div class="detail-grid">
        <div class="detail-main">
            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-info-circle"></i><?php echo e(__('super-admin.subscription_details')); ?></h5>
                </div>
                <div class="section-card-body">
                    <table class="info-table">
                        <tr>
                            <td class="info-label"><?php echo e(__('super-admin.workspace')); ?></td>
                            <td class="info-value"><?php echo e($subscription->workspace?->name ?? __('general.unknown')); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label"><?php echo e(__('settings.plan')); ?></td>
                            <td class="info-value"><span class="badge" style="font-size:11px;background:var(--bg-subtle);color:var(--text);padding:2px 10px;border-radius:6px"><?php echo e($subscription->plan?->name ?? '—'); ?></span></td>
                        </tr>
                        <tr>
                            <td class="info-label"><?php echo e(__('general.status')); ?></td>
                            <td class="info-value">
                                <?php $sc = ['active' => ['bg' => 'var(--success-light)', 'c' => 'var(--success)'], 'trialing' => ['bg' => 'var(--info-light)', 'c' => 'var(--info)'], 'past_due' => ['bg' => 'var(--warning-light)', 'c' => 'var(--warning)'], 'canceled' => ['bg' => 'var(--border)', 'c' => 'var(--text-muted)'], 'expired' => ['bg' => 'var(--danger-light)', 'c' => 'var(--danger)']]; ?>
                                <span class="badge" style="font-size:10px;background:<?php echo e($sc[$subscription->status->value]['bg'] ?? 'var(--border)'); ?>;color:<?php echo e($sc[$subscription->status->value]['c'] ?? 'var(--text-muted)'); ?>;padding:3px 12px;border-radius:6px;font-weight:600"><?php echo e($subscription->status->label()); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="info-label"><?php echo e(__('super-admin.started')); ?></td>
                            <td class="info-value"><?php echo e($subscription->starts_at?->format('Y/m/d H:i') ?? '—'); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label"><?php echo e(__('super-admin.ends_at')); ?></td>
                            <td class="info-value"><?php echo e($subscription->ends_at?->format('Y/m/d H:i') ?? '—'); ?></td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription->trial_ends_at): ?>
                        <tr>
                            <td class="info-label"><?php echo e(__('general.days_left')); ?></td>
                            <td class="info-value"><?php echo e($subscription->daysRemaining()); ?></td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <tr>
                            <td class="info-label"><?php echo e(__('super-admin.auto_renew')); ?></td>
                            <td class="info-value">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription->auto_renew): ?>
                                    <span class="badge" style="font-size:10px;background:var(--success-light);color:var(--success);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e(__('general.yes')); ?></span>
                                <?php else: ?>
                                    <span class="badge" style="font-size:10px;background:var(--border);color:var(--text-muted);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e(__('general.no')); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="info-label"><?php echo e(__('general.type')); ?></td>
                            <td class="info-value"><?php echo e($subscription->payment_method ? __("super-admin.{$subscription->payment_method}") : '—'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-receipt"></i><?php echo e(__('super-admin.invoices')); ?></h5>
                </div>
                <div class="section-card-body p-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription->invoices->count()): ?>
                        <div style="overflow-x:auto">
                            <table class="data-table mb-0">
                                <thead>
                                    <tr>
                                        <th><?php echo e(__('super-admin.invoice_number')); ?></th>
                                        <th><?php echo e(__('super-admin.invoice_amount')); ?></th>
                                        <th><?php echo e(__('general.status')); ?></th>
                                        <th><?php echo e(__('general.date')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subscription->invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><code style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px"><?php echo e($inv->number); ?></code></td>
                                        <td><strong><?php echo e(number_format($inv->total, 2)); ?> <?php echo e($inv->currency ?? config('finance.currency_symbol')); ?></strong></td>
                                        <td>
                                            <?php $bi = ['paid' => ['bg' => 'var(--success-light)', 'c' => 'var(--success)'], 'draft' => ['bg' => 'var(--warning-light)', 'c' => 'var(--warning)'], 'overdue' => ['bg' => 'var(--danger-light)', 'c' => 'var(--danger)']]; $b = $bi[$inv->status->value] ?? ['bg' => 'var(--border)', 'c' => 'var(--text-muted)']; ?>
                                            <span class="badge" style="font-size:10px;background:<?php echo e($b['bg']); ?>;color:<?php echo e($b['c']); ?>;padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e($inv->status->label()); ?></span>
                                        </td>
                                        <td class="cell-muted"><?php echo e($inv->created_at->format('Y/m/d')); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-receipt"></i></div>
                            <h4><?php echo e(__('super-admin.no_invoices')); ?></h4>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="detail-sidebar">
            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-lightning-fill"></i><?php echo e(__('general.actions')); ?></h5>
                </div>
                <div class="section-card-body d-flex flex-column gap-2">
                    <form method="POST" action="<?php echo e(route('super.admin.subscriptions.cancel', $subscription)); ?>" id="cancel-subscription-<?php echo e($subscription->id); ?>" style="display:none">
                        <?php echo csrf_field(); ?>
                    </form>
                    <button type="button" class="btn" style="width:100%;padding:8px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--danger);background:transparent;color:var(--danger);font-weight:500;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px" @click="confirmCancelSubscription(<?php echo e($subscription->id); ?>)" <?php echo e($subscription->status === \App\Enums\SubscriptionStatus::Canceled || $subscription->status === \App\Enums\SubscriptionStatus::Expired ? 'disabled' : ''); ?>>
                        <i class="bi bi-x-circle"></i><?php echo e(__('settings.cancel_subscription')); ?>

                    </button>
                    <form method="POST" action="<?php echo e(route('super.admin.subscriptions.toggle-renew', $subscription)); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn" style="width:100%;padding:8px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text);font-weight:500;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px">
                            <i class="bi bi-arrow-repeat"></i>
                            <?php echo e($subscription->auto_renew ? __('super-admin.auto_renew') . ': ' . __('general.no') : __('super-admin.auto_renew') . ': ' . __('general.yes')); ?>

                        </button>
                    </form>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription->isActive() && $plans->count() > 1): ?>
                    <form method="POST" action="<?php echo e(route('super.admin.subscriptions.change-plan', $subscription)); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="d-flex gap-2">
                            <select name="subscription_plan_id" class="form-control" style="flex:1;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($plan->id); ?>" <?php echo e($plan->id === $subscription->subscription_plan_id ? 'selected' : ''); ?>><?php echo e($plan->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer">
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-cash-coin"></i><?php echo e(__('super-admin.payments')); ?></h5>
                </div>
                <div class="section-card-body p-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription->payments->count()): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subscription->payments->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid var(--border-light)">
                                <div>
                                    <strong style="font-size:13px"><?php echo e(number_format($payment->amount, 2)); ?> <?php echo e($payment->currency); ?></strong>
                                    <div class="cell-muted" style="font-size:11px"><?php echo e(__("super-admin.{$payment->method}")); ?> &middot; <?php echo e($payment->paid_at?->format('Y/m/d') ?? $payment->created_at->format('Y/m/d')); ?></div>
                                </div>
                                <?php $pc = ['completed' => 'var(--success)', 'pending' => 'var(--warning)', 'failed' => 'var(--danger)', 'refunded' => 'var(--info)']; ?>
                                <span class="badge" style="font-size:10px;background:var(--border);color:<?php echo e($pc[$payment->status->value] ?? 'var(--text-muted)'); ?>;padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e($payment->status->label()); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-cash-coin"></i></div>
                            <h4><?php echo e(__('messages.no_results')); ?></h4>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="<?php echo e(route('super.admin.subscriptions.index')); ?>" class="btn" style="padding:8px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text);font-weight:500;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
            <i class="bi bi-arrow-left"></i><?php echo e(__('general.back')); ?>

        </a>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
    function confirmCancelSubscription(id) {
        const form = document.getElementById('cancel-subscription-' + id);
        if (!form) return;
        showConfirmModal(
            '<?php echo e(__('general.confirm')); ?>',
            '<?php echo e(__('settings.cancel_confirm')); ?>',
            (confirmed) => { if (confirmed) form.submit(); },
            '<?php echo e(__('settings.cancel_subscription')); ?>',
            'btn-danger'
        );
    }
    </script>
    <?php $__env->stopPush(); ?>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\super-admin\subscription-show.blade.php ENDPATH**/ ?>