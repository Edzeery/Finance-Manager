<?php $isOnboarded = auth()->check() && auth()->user()->hasCompletedOnboarding(); ?>
<div class="auth-footer mt-3">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOnboarded): ?>
        <a href="<?php echo e(route('account.subscriptions')); ?>" wire:navigate><?php echo e(__('settings.subscriptions')); ?></a>
        <span class="mx-2">|</span>
        <a href="<?php echo e(route('dashboard')); ?>" wire:navigate><?php echo e(__('onboarding.back_to_dashboard')); ?></a>
    <?php else: ?>
        <a href="<?php echo e(route('onboarding.plan')); ?>" wire:navigate><?php echo e(__('onboarding.back_to_plans')); ?></a>
        <span class="mx-2">|</span>
        <a href="<?php echo e(route('dashboard')); ?>" wire:navigate><?php echo e(__('onboarding.back_to_dashboard')); ?></a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/livewire/pages/onboarding/partials/auth-footer.blade.php ENDPATH**/ ?>