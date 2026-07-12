<div class="empty-state">
    <i class="<?php echo e($icon); ?>"></i>
    <h4><?php echo e($title ?? __('general.no_data')); ?></h4>
    <p><?php echo e($message ?? ''); ?></p>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($action) && isset($actionText)): ?>
        <a href="<?php echo e($action); ?>" class="btn btn-accent btn-custom">
            <i class="bi bi-plus-lg me-1"></i><?php echo e($actionText); ?>

        </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/components/empty-state.blade.php ENDPATH**/ ?>