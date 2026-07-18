<?php
    $current = request()->route()->getName();
?>

<div class="d-flex gap-2 mb-4">
    <a href="<?php echo e(route('zakat.calculator')); ?>"
       class="btn btn-custom <?php echo e($current === 'zakat.calculator' ? 'btn-accent' : 'btn-outline-secondary'); ?>">
        <i class="bi bi-calculator me-1"></i><?php echo e(__('zakat.calculate')); ?>

    </a>
    <a href="<?php echo e(route('zakat.history')); ?>"
       class="btn btn-custom <?php echo e($current === 'zakat.history' ? 'btn-accent' : 'btn-outline-secondary'); ?>">
        <i class="bi bi-clock-history me-1"></i><?php echo e(__('zakat.history')); ?>

    </a>
</div>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\zakat\_nav.blade.php ENDPATH**/ ?>