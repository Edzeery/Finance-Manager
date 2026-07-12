<div class="settings-card">
    <div class="d-flex align-items-center justify-content-between gap-2" style="margin-bottom:16px">
        <h5 class="section-title mb-0"><i class="bi bi-credit-card text-accent"></i><?php echo e(__('settings.subscription')); ?></h5>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription): ?>
            <span class="badge badge-status <?php echo e($subscription->isActive() ? 'badge-configured' : 'badge-not-configured'); ?>">
                <?php echo e($subscription->status->label()); ?>

            </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription && $subscription->plan): ?>
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h6 style="font-weight:600;margin-bottom:2px"><?php echo e($subscription->plan->name); ?></h6>
                <p class="plan-card-muted" style="margin:0;font-size:13px">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription->plan->isFree()): ?>
                        <?php echo e(__('settings.free_plan')); ?>

                    <?php else: ?>
                        $<?php echo e($subscription->plan->monthly_price); ?>/<?php echo e(__('general.month')); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription->plan->yearly_price > 0): ?>
                            &middot; $<?php echo e($subscription->plan->yearly_price); ?>/<?php echo e(__('general.year')); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
            </div>
            <div class="text-end">
                <div style="font-weight:600;font-size:14px">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription->isActive()): ?>
                        <span style="color:var(--success)">&#9679;</span> <?php echo e(__('settings.active_plan')); ?>

                    <?php elseif($subscription->status === \App\Enums\SubscriptionStatus::Canceled): ?>
                        <span style="color:var(--danger)">&#9679;</span> <?php echo e(__('settings.canceled_plan')); ?>

                    <?php elseif($subscription->isExpired()): ?>
                        <span style="color:var(--danger)">&#9679;</span> <?php echo e(__('settings.expired_plan')); ?>

                    <?php else: ?>
                        &#9679; <?php echo e($subscription->status->label()); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="text-muted-sm" style="font-size:11px"><?php echo e(__('settings.users_usage')); ?></div>
                <div style="font-weight:600;font-size:13px">
                    <?php echo e($workspace->userCount()); ?> / <?php echo e($workspace->userLimit()); ?>

                    <span class="text-muted-sm" style="font-size:11px"><?php echo e(__('general.users')); ?></span>
                </div>
            </div>
            <div class="col-6">
                <div class="text-muted-sm" style="font-size:11px"><?php echo e(__('settings.days_remaining')); ?></div>
                <div style="font-weight:600;font-size:13px"><?php echo e($subscription->daysRemaining()); ?> <?php echo e(__('general.days_left')); ?></div>
            </div>
        </div>

        <a href="<?php echo e(route('account.subscriptions')); ?>" class="btn btn-accent btn-custom btn-sm w-100 mb-2">
            <i class="bi bi-credit-card me-1"></i><?php echo e(__('settings.subscriptions')); ?>

        </a>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->isWorkspaceOwner($workspace) && !$subscription->plan->isFree() && $subscription->isActive() && !$subscription->canceled_at): ?>
            <button type="button" class="btn btn-sm btn-outline-danger btn-custom w-100" @click="confirmCancelSubscription()">
                <i class="bi bi-x-circle me-1"></i><?php echo e(__('settings.cancel_subscription')); ?>

            </button>
        <?php elseif($subscription->canceled_at): ?>
            <span class="text-muted-sm" style="font-size:13px;display:block;text-align:center;padding:8px 0">
                <i class="bi bi-info-circle me-1"></i><?php echo e(__('settings.cancel_scheduled')); ?>

            </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php else: ?>
        <p class="text-muted mb-3" style="font-size:13px"><?php echo e(__('settings.no_subscription')); ?></p>
        <a href="<?php echo e(route('account.subscriptions')); ?>" class="btn btn-accent btn-custom btn-sm w-100">
            <i class="bi bi-credit-card me-1"></i><?php echo e(__('settings.subscriptions')); ?>

        </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

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
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\settings\_subscription.blade.php ENDPATH**/ ?>