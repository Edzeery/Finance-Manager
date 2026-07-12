
<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php
        $displayPrice = function (float $usdAmount) use ($userCurrency) {
            $converted = \App\Services\CurrencyHelper::fromUsd($usdAmount, $userCurrency);
            return number_format($converted, 2) . ' ' . \App\Services\CurrencyHelper::symbol($userCurrency);
        };
        $formatAmount = function (float $amount, string $currency) {
            return number_format($amount, 2) . ' ' . \App\Services\CurrencyHelper::symbol($currency);
        };
        $getMethodLabel = function (?string $method) {
            if (!$method) return '—';
            $labels = [
                'chargily' => __('super-admin.chargily'),
                'baridimob' => __('super-admin.baridimob'),
                'paypal' => __('super-admin.paypal'),
                'redotpay' => __('super-admin.redotpay'),
                'cash' => __('general.cash'),
                'delivery' => __('general.delivery'),
                'wise' => 'Wise',
                'wise_manual' => 'Wise Manual',
                'stripe' => 'Stripe',
                'payoneer' => 'Payoneer',
                'noest' => 'Noest',
            ];
            return $labels[$method] ?? ucfirst($method);
        };
        $getMethodType = function (?string $method): ?\App\Models\PaymentMethod {
            if (!$method) return null;
            return \App\Models\PaymentMethod::where('key', $method)->first();
        };
    ?>

     <?php $__env->slot('title', null, []); ?> <?php echo e(__('settings.subscriptions')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('settings.subscriptions')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('settings.subscriptions_desc')); ?> <?php $__env->endSlot(); ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingPayment): ?>
        <?php $continueUrl = $pendingPayment->getContinueUrl(); ?>
        <div class="alert alert-warning d-flex align-items-center gap-3 flex-wrap" role="alert">
            <i class="bi bi-exclamation-triangle" style="font-size:20px"></i>
            <span class="flex-grow-1"><?php echo e(__('settings.pending_payment_block')); ?></span>
            <div class="d-flex gap-2 flex-shrink-0">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($continueUrl): ?>
                    <a href="<?php echo e($continueUrl); ?>" target="_blank" class="btn btn-warning btn-custom btn-sm">
                        <i class="bi bi-credit-card me-1"></i><?php echo e(__('settings.complete_payment')); ?>

                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('payment.resume', $pendingPayment)); ?>" class="btn btn-warning btn-custom btn-sm">
                        <i class="bi bi-credit-card me-1"></i><?php echo e(__('settings.complete_payment')); ?>

                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <form method="POST" action="<?php echo e(route('account.subscriptions.cancel-payment', $pendingPayment)); ?>" onsubmit="return confirm('<?php echo e(__('settings.cancel_payment_confirm')); ?>')">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-outline-secondary btn-custom btn-sm">
                        <i class="bi bi-x-lg me-1"></i><?php echo e(__('settings.cancel_payment')); ?>

                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="row g-4">
        
        <div class="col-lg-8">
            
            <div class="settings-section">
                <div class="settings-card" style="overflow:hidden">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription && $subscription->plan): ?>
                        <?php $plan = $subscription->plan; ?>
                        <div style="background:linear-gradient(135deg, var(--accent), color-mix(in srgb, var(--accent) 70%, #000));margin:-1.25rem -1.25rem 1.25rem -1.25rem;padding:1.5rem 1.75rem;position:relative">
                            <div style="position:absolute;top:0;right:0;width:200px;height:200px;background:rgba(255,255,255,0.05);border-radius:50%;transform:translate(50%,-50%)"></div>
                            <div style="position:absolute;bottom:0;left:0;width:150px;height:150px;background:rgba(255,255,255,0.03);border-radius:50%;transform:translate(-30%,30%)"></div>
                            <div class="d-flex align-items-center justify-content-between gap-3" style="position:relative;z-index:1">
                                <div>
                                    <div style="font-size:13px;color:rgba(255,255,255,0.7);margin-bottom:4px"><?php echo e(__('settings.current_plan')); ?></div>
                                    <h3 style="font-weight:700;color:#fff;margin-bottom:4px"><?php echo e($plan->name); ?></h3>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->isFree()): ?>
                                        <span style="font-size:15px;color:rgba(255,255,255,0.9)"><?php echo e(__('settings.free_plan')); ?></span>
                                    <?php else: ?>
                                        <span style="font-size:20px;font-weight:700;color:#fff"><?php echo e($displayPrice($plan->monthly_price)); ?></span>
                                        <span style="font-size:13px;color:rgba(255,255,255,0.7)">/<?php echo e(__('general.month')); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->yearly_price > 0): ?>
                                            <span style="font-size:13px;color:rgba(255,255,255,0.6);margin-inline-start:8px"><?php echo e($displayPrice($plan->yearly_price)); ?>/<?php echo e(__('general.year')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->description): ?>
                                        <p style="font-size:13px;color:rgba(255,255,255,0.7);margin-top:4px;margin-bottom:0"><?php echo e($plan->description); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <span class="badge" style="background:rgba(255,255,255,0.2);color:#fff;font-size:12px;padding:6px 14px;border-radius:20px;font-weight:600">
                                        <?php echo e($subscription->status->label()); ?>

                                    </span>
                                </div>
                            </div>
                        </div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($workspace): ?>
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <?php
                                    $userCount = $workspace->userCount();
                                    $userLimit = $workspace->userLimit();
                                    $userPercent = $userLimit > 0 ? min(100, round(($userCount / $userLimit) * 100)) : 0;
                                ?>
                                <div>
                                    <div class="d-flex justify-content-between text-muted-sm" style="margin-bottom:4px">
                                        <span><i class="bi bi-people" style="margin-inline-end:4px"></i><?php echo e(__('settings.users_usage')); ?></span>
                                        <span><?php echo e($userCount); ?> / <?php echo e($userLimit); ?> <?php echo e(__('general.users')); ?></span>
                                    </div>
                                    <div class="progress" style="height:6px;border-radius:3px;background:var(--border)">
                                        <div class="progress-bar" role="progressbar"
                                             style="width:<?php echo e($userPercent); ?>%;border-radius:3px;background:<?php echo e($userPercent > 80 ? 'var(--danger)' : ($userPercent > 60 ? 'var(--warning)' : 'var(--accent)')); ?>"
                                             aria-valuenow="<?php echo e($userPercent); ?>" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?php
                                    $txCount = app(\App\Services\SubscriptionService::class)->transactionsThisMonth($workspace);
                                    $txLimit = app(\App\Services\SubscriptionService::class)->maxTransactionsPerMonth($workspace);
                                    $txPercent = $txLimit > 0 ? min(100, round(($txCount / $txLimit) * 100)) : 0;
                                ?>
                                <div>
                                    <div class="d-flex justify-content-between text-muted-sm" style="margin-bottom:4px">
                                        <span><i class="bi bi-arrow-left-right" style="margin-inline-end:4px"></i><?php echo e(__('settings.transactions_usage')); ?></span>
                                        <span><?php echo e($txCount); ?> / <?php echo e($txLimit); ?> <?php echo e(__('general.transactions')); ?></span>
                                    </div>
                                    <div class="progress" style="height:6px;border-radius:3px;background:var(--border)">
                                        <div class="progress-bar" role="progressbar"
                                             style="width:<?php echo e($txPercent); ?>%;border-radius:3px;background:<?php echo e($txPercent > 80 ? 'var(--danger)' : ($txPercent > 60 ? 'var(--warning)' : 'var(--accent)')); ?>"
                                             aria-valuenow="<?php echo e($txPercent); ?>" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <div class="row g-0" style="border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1rem">
                            <?php
                                $_pm = $getMethodType($subscription->payment_method);
                                $_methodTypeLabel = $_pm?->type?->label();
                                $_gatewayName = $_pm?->gateway?->name ?? $getMethodLabel($subscription->payment_method);
                            ?>
                            <div class="col-4" style="border-inline-end:1px solid var(--border);background:var(--bg-subtle)">
                                <div style="padding:12px 16px;text-align:center">
                                    <i class="bi bi-credit-card-2-front" style="font-size:16px;color:var(--text-muted);margin-bottom:4px;display:block"></i>
                                    <div class="text-muted-sm" style="font-size:11px;margin-bottom:2px"><?php echo e(__('settings.payment_method')); ?></div>
                                    <div style="font-weight:600;font-size:13px"><?php echo e($_gatewayName); ?></div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:1px"><?php echo e($_methodTypeLabel); ?></div>
                                </div>
                            </div>
                            <div class="col-4" style="border-inline-end:1px solid var(--border);background:var(--bg-subtle)">
                                <div style="padding:12px 16px;text-align:center">
                                    <i class="bi bi-calendar-event" style="font-size:16px;color:var(--text-muted);margin-bottom:4px;display:block"></i>
                                    <div class="text-muted-sm" style="font-size:11px;margin-bottom:2px"><?php echo e(__('settings.billing_period')); ?></div>
                                    <div style="font-weight:600;font-size:13px"><?php echo e($subscription->billing_period === 'yearly' ? __('general.yearly') : __('general.monthly')); ?></div>
                                </div>
                            </div>
                            <div class="col-4" style="border-inline-end:1px solid var(--border);background:var(--bg-subtle)">
                                <div style="padding:12px 16px;text-align:center">
                                    <i class="bi bi-hourglass-split" style="font-size:16px;color:var(--text-muted);margin-bottom:4px;display:block"></i>
                                    <div class="text-muted-sm" style="font-size:11px;margin-bottom:2px"><?php echo e(__('settings.days_remaining')); ?></div>
                                    <div style="font-weight:600;font-size:13px"><?php echo e($subscription->daysRemaining() . ' ' . __('general.days_left')); ?></div>
                                </div>
                            </div>
                        </div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOwner && !$pendingPayment): ?>
                            <div class="d-flex gap-2 flex-wrap">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription && $subscription->isActive()): ?>
                                    <a href="#available-plans" class="btn btn-accent btn-custom">
                                        <i class="bi bi-arrow-repeat me-1"></i><?php echo e(__('settings.change_plan')); ?>

                                    </a>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$plan->isFree() && !$subscription->canceled_at): ?>
                                        <button type="button" class="btn btn-outline-danger btn-custom" @click="confirmCancelSubscription()">
                                            <i class="bi bi-x-circle me-1"></i><?php echo e(__('settings.cancel_subscription')); ?>

                                        </button>
                                    <?php elseif($subscription->canceled_at && $subscription->isOnGrace()): ?>
                                        <form method="POST" action="<?php echo e(route('account.subscriptions.resume')); ?>" style="display:inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-outline-accent btn-custom"
                                                onclick="return confirm('<?php echo e(__('settings.resume_confirm')); ?>')">
                                                <i class="bi bi-arrow-repeat me-1"></i><?php echo e(__('settings.resume_subscription')); ?>

                                            </button>
                                        </form>
                                    <?php elseif($subscription->canceled_at): ?>
                                        <span class="text-muted-sm" style="font-size:13px;padding:8px 0">
                                            <i class="bi bi-info-circle me-1"></i><?php echo e(__('settings.cancel_scheduled')); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <a href="#available-plans" class="btn btn-accent btn-custom">
                                        <i class="bi bi-rocket-takeoff me-1"></i><?php echo e(__('settings.renew_subscription')); ?>

                                    </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-credit-card-2-front" style="font-size:48px;color:var(--text-muted);opacity:0.4"></i>
                            <p class="text-muted mt-3 mb-3"><?php echo e(__('settings.no_subscription')); ?></p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOwner): ?>
                                <a href="#available-plans" class="btn btn-accent btn-custom">
                                    <i class="bi bi-rocket-takeoff me-1"></i><?php echo e(__('settings.choose_plan')); ?>

                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="settings-section">
                <div class="settings-card">
                    <h5 class="section-title mb-1 d-flex align-items-center gap-2"><i class="bi bi-receipt text-accent"></i><span><?php echo e(__('settings.payment_history')); ?></span></h5>
                    <p class="section-desc mb-3"><?php echo e(__('settings.payment_history_desc')); ?></p>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payments && $payments->isNotEmpty()): ?>
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                        <tr>
                            <th><?php echo e(__('settings.invoice_date')); ?></th>
                            <th><?php echo e(__('settings.invoice_amount')); ?></th>
                            <th><?php echo e(__('settings.payment_method')); ?></th>
                            <th><?php echo e(__('general.status')); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $continueUrl = $payment->isPending() ? $payment->getContinueUrl() : null; ?>
                            <tr>
                                <td style="font-size:13px"><?php echo e($payment->created_at->format('Y/m/d H:i')); ?></td>
                                <td>
                                    <span style="font-weight:600">
                                        <?php echo e($formatAmount($payment->amount, $payment->currency ?? 'USD')); ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->original_amount > $payment->amount): ?>
                                            <span style="text-decoration:line-through;color:var(--text-muted);font-weight:400;font-size:12px">
                                                <?php echo e($formatAmount($payment->original_amount, $payment->currency ?? 'USD')); ?>

                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </span>
                                </td>
                                <td style="font-size:13px"><?php echo e($getMethodLabel($payment->method)); ?></td>
                                <td>
                                    <?php echo e($payment->status->label()); ?>

                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($continueUrl): ?>
                                        <a href="<?php echo e($continueUrl); ?>" target="_blank" class="btn btn-sm btn-warning">
                                            <i class="bi bi-credit-card me-1"></i><?php echo e(__('settings.complete_payment')); ?>

                                        </a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 text-end">
                            <a href="<?php echo e(route('account.payments')); ?>" class="btn btn-outline-accent btn-custom btn-sm">
                                <i class="bi bi-eye me-1"></i><?php echo e(__('settings.view_all_payments')); ?>

                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-inbox" style="font-size:32px;color:var(--text-muted);opacity:0.4"></i>
                            <p class="text-muted mt-2 mb-0"><?php echo e(__('settings.no_payments')); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasSubscriptionHistory && $allSubscriptions->isNotEmpty()): ?>
            <div class="settings-section">
                <div class="settings-card">
                    <h5 class="section-title mb-1 d-flex align-items-center gap-2"><i class="bi bi-clock-history text-accent"></i><span><?php echo e(__('settings.subscription_history')); ?></span></h5>
                    <p class="section-desc mb-3"><?php echo e(__('settings.subscription_history_desc')); ?></p>

                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('settings.plan')); ?></th>
                                    <th><?php echo e(__('general.status')); ?></th>
                                    <th><?php echo e(__('settings.billing_period')); ?></th>
                                    <th><?php echo e(__('super-admin.started')); ?></th>
                                    <th><?php echo e(__('super-admin.ends_at')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allSubscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription && $sub->id === $subscription->id && $subscription->isActive()): ?>
                                        <?php continue; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <tr>
                                        <td>
                                            <span style="font-weight:500"><?php echo e($sub->plan?->name ?? __('general.unknown')); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sub->workspace): ?>
                                                <div style="font-size:12px;color:var(--text-muted)"><?php echo e($sub->workspace->name); ?></div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                                $hColors = ['active' => 'success', 'trialing' => 'info', 'past_due' => 'warning', 'canceled' => 'secondary', 'expired' => 'danger'];
                                                $hBadge = $hColors[$sub->status->value] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?php echo e($hBadge); ?>"><?php echo e($sub->status->label()); ?></span>
                                        </td>
                                        <td style="font-size:13px"><?php echo e($sub->billing_period === 'yearly' ? __('general.yearly') : __('general.monthly')); ?></td>
                                        <td style="font-size:13px;color:var(--text-muted)"><?php echo e($sub->starts_at?->format('Y/m/d') ?? '—'); ?></td>
                                        <td style="font-size:13px;color:var(--text-muted)"><?php echo e($sub->ends_at?->format('Y/m/d') ?? '—'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="settings-section">
                <div class="settings-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="section-title mb-1 d-flex align-items-center gap-2"><i class="bi bi-file-text text-accent"></i><span><?php echo e(__('settings.invoices')); ?></span></h5>
                            <p class="section-desc mb-0"><?php echo e(__('settings.invoices_desc')); ?></p>
                        </div>
                        <a href="<?php echo e(route('account.invoices.index')); ?>" class="btn btn-accent btn-custom">
                            <i class="bi bi-receipt me-1"></i><?php echo e(__('settings.view_all_invoices')); ?>

                        </a>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4" id="available-plans">
            <div class="settings-section" style="position:sticky;top:24px">
                <div class="settings-card">
                    <h5 class="section-title mb-1 d-flex align-items-center gap-2"><i class="bi bi-grid-3x3-gap text-accent"></i><span><?php echo e(__('settings.available_plans')); ?></span></h5>
                    <p class="section-desc mb-3"><?php echo e(__('settings.available_plans_desc')); ?></p>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingPayment): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-exclamation-triangle" style="font-size:32px;color:var(--warning);opacity:0.6"></i>
                            <p class="text-muted mt-2 mb-0"><?php echo e(__('settings.pending_payment_block')); ?></p>
                        </div>
                    <?php elseif($isOwner): ?>
                        <?php $currentPlan = $subscription?->plan; ?>
                        <?php $hasActive = $subscription && $subscription->isActive(); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php $isCurrent = $currentPlan && $currentPlan->slug === $plan->slug && $hasActive; ?>
                            <div class="plan-card mb-3 <?php echo e($isCurrent ? 'plan-current' : ''); ?>" style="border:1px solid <?php echo e($isCurrent ? 'var(--accent)' : 'var(--border)'); ?>;border-radius:12px;padding:16px;transition:all 0.2s">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 style="font-weight:600;margin-bottom:2px;font-size:14px"><?php echo e($plan->name); ?></h6>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->is_free): ?>
                                            <span style="font-size:13px;color:var(--text-muted)"><?php echo e(__('settings.free')); ?></span>
                                        <?php else: ?>
                                            <span style="font-size:20px;font-weight:700"><?php echo e($displayPrice($plan->monthly_price)); ?>

                                                <span style="font-size:12px;font-weight:400;color:var(--text-muted)">/<?php echo e(__('general.month')); ?></span>
                                            </span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->yearly_price > 0): ?>
                                                <div style="font-size:12px;color:var(--text-muted);margin-top:2px">
                                                    <?php echo e($displayPrice($plan->yearly_price)); ?>/<?php echo e(__('general.year')); ?>

                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCurrent): ?>
                                        <span class="badge" style="background:var(--accent-light);color:var(--accent);font-size:10px;padding:3px 10px;border-radius:20px;font-weight:600">
                                            <?php echo e(__('settings.current')); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <?php $features = $plan->planFeatures; $visibleFeatures = $features->take(5); $hiddenFeatures = $features->slice(5); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($features->isNotEmpty()): ?>
                                    <div style="margin-bottom:12px">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $visibleFeatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $fName = $feature->{'name_' . app()->getLocale()} ?? $feature->name_en; ?>
                                            <div style="font-size:12px;color:var(--text-muted);padding:2px 0">
                                                <i class="bi bi-check-circle" style="color:var(--success);margin-inline-end:6px;font-size:11px"></i>
                                                <?php echo e($fName); ?><?php echo e($feature->pivot->value ? ': ' . $feature->pivot->value : ''); ?>

                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hiddenFeatures->isNotEmpty()): ?>
                                            <div class="plan-extra-features-<?php echo e($plan->id); ?> d-none">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $hiddenFeatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $fName = $feature->{'name_' . app()->getLocale()} ?? $feature->name_en; ?>
                                                    <div style="font-size:12px;color:var(--text-muted);padding:2px 0">
                                                        <i class="bi bi-check-circle" style="color:var(--success);margin-inline-end:6px;font-size:11px"></i>
                                                        <?php echo e($fName); ?><?php echo e($feature->pivot->value ? ': ' . $feature->pivot->value : ''); ?>

                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <button type="button" class="btn btn-link p-0 plan-toggle-features" data-plan="<?php echo e($plan->id); ?>" data-expanded="false" style="font-size:12px;color:var(--accent);text-decoration:none">
                                                <?php echo e(__('general.show_more')); ?> (<?php echo e($hiddenFeatures->count()); ?>)
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <div style="font-size:12px;color:var(--text-muted);margin-bottom:12px">
                                    <i class="bi bi-people"></i> <?php echo e(__('admin.users_count')); ?>: <?php echo e($plan->max_users); ?>

                                </div>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isCurrent): ?>
                                    <form action="<?php echo e(route('account.subscriptions.change-plan')); ?>" method="POST" class="plan-form" onsubmit="return handlePlanSubmit(this)">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="plan_slug" value="<?php echo e($plan->slug); ?>">
                                        <input type="hidden" name="billing" value="monthly">

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$plan->is_free): ?>
                                            <div class="mb-2">
                                                <select name="billing" class="form-custom billing-select" style="font-size:12px;padding:5px 8px;width:100%">
                                                    <option value="monthly"><?php echo e($displayPrice($plan->monthly_price)); ?>/<?php echo e(__('general.month')); ?></option>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->yearly_price > 0): ?>
                                                        <option value="yearly"><?php echo e($displayPrice($plan->yearly_price)); ?>/<?php echo e(__('general.year')); ?></option>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <select name="payment_method" class="form-custom" required style="font-size:12px;padding:5px 8px;width:100%">
                                                    <option value=""><?php echo e(__('payment.select_method')); ?></option>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($pm['id']); ?>"><?php echo e($pm['name']); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </select>
                                            </div>
                                             <div class="mb-2">
                                                 <input type="text" name="coupon" class="form-custom coupon-input"
                                                        placeholder="<?php echo e(__('payment.coupon_placeholder')); ?>"
                                                        data-plan-price="<?php echo e($plan->monthly_price); ?>"
                                                        data-plan-slug="<?php echo e($plan->slug); ?>"
                                                        style="font-size:12px;padding:5px 8px;width:100%">
                                             </div>

                                             <div class="fee-breakdown-<?php echo e($plan->id); ?>" style="display:none"></div>
                                         <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                         <div class="d-grid gap-2">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$hasActive): ?>
                                                <button type="submit" class="btn btn-accent btn-custom btn-sm renew-btn">
                                                    <span class="btn-text"><i class="bi bi-rocket-takeoff me-1"></i><?php echo e(__('settings.renew_subscription')); ?></span>
                                                    <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-1"></span><span class="btn-redirect-text"><?php echo e(__('settings.redirecting_to_payment')); ?></span></span>
                                                </button>
                                            <?php elseif($plan->sort_order > ($currentPlan->sort_order ?? -1)): ?>
                                                <button type="submit" class="btn btn-accent btn-custom btn-sm">
                                                    <span class="btn-text"><i class="bi bi-arrow-up-circle me-1"></i><?php echo e(__('settings.upgrade')); ?></span>
                                                    <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-1"></span><?php echo e(__('general.processing')); ?></span>
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-outline-secondary btn-custom btn-sm"
                                                    data-confirm-message="<?php echo e(__('settings.downgrade_confirm')); ?>"
                                                    @click="return confirmDowngrade(this, $el.dataset.confirmMessage)">
                                                    <span class="btn-text"><i class="bi bi-arrow-down-circle me-1"></i><?php echo e(__('settings.downgrade')); ?></span>
                                                    <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-1"></span><?php echo e(__('general.processing')); ?></span>
                                                </button>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <div class="d-grid">
                                        <button type="button" class="btn btn-outline-accent btn-custom btn-sm" disabled>
                                            <i class="bi bi-check-lg me-1"></i><?php echo e(__('settings.current_plan_label')); ?>

                                        </button>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-muted text-center mb-0 py-3"><?php echo e(__('settings.no_plans_available')); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0"><?php echo e(__('settings.only_owner_can_change')); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('styles'); ?>
    <style>
        .price-breakdown {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 16px;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            padding: 4px 0;
            color: var(--text-muted);
        }
        .price-row.total {
            font-weight: 700;
            font-size: 15px;
            color: var(--text);
        }
        .price-row.total.free {
            color: var(--accent);
        }
        .price-row.discount {
            color: var(--success, #28a745);
        }
        .price-divider {
            height: 1px;
            background: var(--border);
            margin: 6px 0;
        }
    </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
    <script>
    function confirmCancelSubscription() {
        showConfirmModal(
            '<?php echo e(__('general.confirm')); ?>',
            '<?php echo e(__('settings.cancel_confirm')); ?>',
            (confirmed) => {
                if (confirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '<?php echo e(route('account.subscriptions.cancel')); ?>';
                    form.innerHTML = '<?php echo csrf_field(); ?>';
                    document.body.appendChild(form);
                    form.submit();
                }
            },
            '<?php echo e(__('settings.cancel_subscription')); ?>',
            'btn-danger'
        );
    }

    function handlePlanSubmit(form) {
        const btn = form.querySelector('button[type="submit"]');
        const text = btn.querySelector('.btn-text');
        const loading = btn.querySelector('.btn-loading');
        text.classList.add('d-none');
        loading.classList.remove('d-none');
        btn.disabled = true;
        if (loading.querySelector('.btn-redirect-text')) {
            loading.querySelector('.btn-redirect-text').textContent = '<?php echo e(__('settings.redirecting_to_payment')); ?>';
        }
        setTimeout(function() {
            if (btn.disabled) {
                text.classList.remove('d-none');
                loading.classList.add('d-none');
                btn.disabled = false;
            }
        }, 30000);
        return true;
    }

    function confirmDowngrade(btn, message) {
        showConfirmModal(
            '<?php echo e(__('general.confirm')); ?>',
            message,
            function(confirmed) {
                if (confirmed) {
                    const form = btn.closest('form');
                    const loading = btn.querySelector('.btn-loading');
                    const text = btn.querySelector('.btn-text');
                    text.classList.add('d-none');
                    loading.classList.remove('d-none');
                    btn.disabled = true;
                    form.submit();
                    setTimeout(function() {
                        if (btn.disabled) {
                            text.classList.remove('d-none');
                            loading.classList.add('d-none');
                            btn.disabled = false;
                        }
                    }, 30000);
                }
            },
            '<?php echo e(__('settings.downgrade')); ?>',
            'btn-warning'
        );
        return false;
    }

    function initSubscriptions() {
        document.querySelectorAll('.billing-select').forEach(function(sel) {
            sel.addEventListener('change', function () {
                var form = this.closest('.plan-form');
                var couponInput = form.querySelector('.coupon-input');
                if (!couponInput) return;
                var selectedOption = this.options[this.selectedIndex];
                var priceMatch = selectedOption.text.match(/[\d.]+/);
                couponInput.dataset.planPrice = priceMatch ? parseFloat(priceMatch[0]) : 0;
                if (couponInput.value) {
                    validateCoupon(couponInput);
                }
                updateFeeBreakdown(form);
            });
        });

        document.querySelectorAll('.coupon-input').forEach(function(input) {
            var debounceTimer;
            input.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    validateCoupon(input);
                    updateFeeBreakdown(input.closest('form'));
                }, 400);
            });
        });

        document.querySelectorAll('.plan-form select[name="payment_method"]').forEach(function(sel) {
            sel.addEventListener('change', function () {
                updateFeeBreakdown(this.closest('form'));
            });
        });

        document.querySelectorAll('.plan-toggle-features').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var planId = this.dataset.plan;
                var extras = document.querySelector('.plan-extra-features-' + planId);
                if (!extras) return;
                var expanded = this.dataset.expanded === 'true';
                extras.classList.toggle('d-none', expanded);
                this.dataset.expanded = !expanded;
                var hiddenCount = extras.querySelectorAll('div').length;
                this.textContent = expanded
                    ? '<?php echo e(__('general.show_more')); ?> (' + hiddenCount + ')'
                    : '<?php echo e(__('general.show_less')); ?>';
            });
        });
    }
    initSubscriptions();

    function updateFeeBreakdown(form) {
        if (!form) return;
        var pm = form.querySelector('select[name="payment_method"]');
        if (!pm || !pm.value) return;
        var billing = form.querySelector('select[name="billing"]');
        var coupon = form.querySelector('.coupon-input');
        var planSlug = form.querySelector('input[name="plan_slug"]');
        if (!planSlug) return;

        var params = new URLSearchParams();
        params.set('plan_slug', planSlug.value);
        params.set('billing', billing ? billing.value : 'monthly');
        params.set('payment_method', pm.value);
        if (coupon && coupon.value) params.set('coupon', coupon.value);

        var container = form.querySelector('[class^="fee-breakdown-"]');
        if (!container) return;

        fetch('<?php echo e(route('account.subscriptions.fee-breakdown')); ?>?' + params.toString())
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data) return;
                var fmt = function(amount) {
                    return parseFloat(amount).toFixed(2) + ' ' + data.currency;
                };
                var html = '<div class="price-breakdown mb-3">';
                html += '<div class="price-row original"><span><?php echo e(__('onboarding.plan_price')); ?></span><span>' + fmt(data.original) + '</span></div>';
                if (parseFloat(data.discount_usd) > 0) {
                    html += '<div class="price-row discount"><span><?php echo e(__('onboarding.coupon_discount')); ?></span><span>-' + fmt(data.discount) + '</span></div>';
                }
                if (parseFloat(data.gateway_fee_usd) > 0) {
                    html += '<div class="price-row fee"><span><?php echo e(__('onboarding.gateway_fee')); ?></span><span>+' + fmt(data.gateway_fee) + '</span></div>';
                }
                if (parseFloat(data.tax_added_usd) > 0) {
                    html += '<div class="price-row fee"><span><?php echo e(__('onboarding.tax_added')); ?></span><span>+' + fmt(data.tax_added) + '</span></div>';
                }
                if (parseFloat(data.tax_disclosed_usd) > 0) {
                    html += '<div class="price-row"><span><?php echo e(__('onboarding.tax_disclosed')); ?></span><span>' + fmt(data.tax_disclosed) + '</span></div>';
                }
                html += '<div class="price-divider"></div>';
                html += '<div class="price-row total' + (parseFloat(data.total_usd) <= 0 ? ' free' : '') + '"><span><?php echo e(__('onboarding.total')); ?></span><span>';
                if (parseFloat(data.total_usd) <= 0) {
                    html += '<?php echo e(__('onboarding.free')); ?>';
                } else {
                    html += fmt(data.total);
                }
                html += '</span></div></div>';
                container.innerHTML = html;
                container.style.display = 'block';
            })
            .catch(function() {});
    }

    function validateCoupon(input) {
        const code = input.value.trim();
        const price = input.dataset.planPrice || 0;
        const feedback = input.nextElementSibling?.classList?.contains('coupon-feedback')
            ? input.nextElementSibling
            : (() => {
                const el = document.createElement('div');
                el.className = 'coupon-feedback';
                el.style.cssText = 'font-size:11px;margin-top:2px';
                input.parentNode.appendChild(el);
                return el;
            })();

        if (!code) {
            feedback.textContent = '';
            feedback.style.color = '';
            return;
        }

        fetch('<?php echo e(route('coupon.validate', ['code' => '__CODE__', 'amount' => '__AMOUNT__'])); ?>'
            .replace('__CODE__', code)
            .replace('__AMOUNT__', price))
            .then(r => r.json())
            .then(data => {
                if (data.valid) {
                    const label = data.type === 'percentage' ? data.value + '%' : '<?php echo e(config('finance.currency_symbol')); ?>' + data.discount;
                    feedback.textContent = '<?php echo e(__('messages.coupon_applied')); ?> (' + label + ')';
                    feedback.style.color = 'var(--success)';
                } else {
                    feedback.textContent = data.message || '<?php echo e(__('messages.coupon_invalid')); ?>';
                    feedback.style.color = 'var(--danger)';
                }
            })
            .catch(() => {
                feedback.textContent = '<?php echo e(__('messages.coupon_check_error')); ?>';
                feedback.style.color = 'var(--danger)';
            });
    }
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/account/subscriptions.blade.php ENDPATH**/ ?>