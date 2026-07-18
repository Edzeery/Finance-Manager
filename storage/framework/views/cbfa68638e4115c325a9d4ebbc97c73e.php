<div class="endpoint-card">
    <div class="endpoint-card-header" role="button">
        <span class="http-method <?php echo e(strtolower($method)); ?>"><?php echo e($method); ?></span>
        <span class="endpoint-url"><?php echo e($endpoint); ?></span>
        <span class="endpoint-desc" style="margin-left:auto;font-size:0.8rem;color:var(--text-muted,#6b7280);display:block;text-align:right;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:250px"><?php echo e($desc ?? ''); ?></span>
        <i class="bi bi-chevron-down" style="flex-shrink:0;color:var(--text-muted,#6b7280);transition:transform 0.2s"></i>
    </div>
    <div class="endpoint-card-body">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($ability) && $ability !== 'none'): ?>
            <div style="margin-bottom:0.75rem;font-size:0.8125rem;color:var(--text-muted,#6b7280)">
                <strong><?php echo e(__('api-docs.with_abilities')); ?></strong>
                <span class="ability-tag"><?php echo e($ability); ?></span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php echo e($slot); ?>

    </div>
</div>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\components\api-endpoint.blade.php ENDPATH**/ ?>