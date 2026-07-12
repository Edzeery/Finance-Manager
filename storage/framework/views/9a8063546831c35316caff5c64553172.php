<?php
    $status = request('status', 'success');
    $config = [
        'success' => ['icon' => 'bi-check-circle-fill', 'color' => '#198754', 'title' => __('payment.success_title'), 'message' => __('payment.success_message')],
        'canceled' => ['icon' => 'bi-x-circle-fill', 'color' => '#dc3545', 'title' => __('payment.canceled_title'), 'message' => __('payment.canceled_message')],
    ];
    $current = $config[$status] ?? $config['success'];
?>

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
    <div style="min-height:60vh;display:flex;align-items:center;justify-content:center">
        <div style="text-align:center;max-width:480px">
            <i class="bi <?php echo e($current['icon']); ?>" style="font-size:64px;color:<?php echo e($current['color']); ?>;margin-bottom:16px"></i>
            <h3 style="font-weight:600;margin-bottom:8px"><?php echo e($current['title']); ?></h3>
            <p style="color:var(--text-muted);margin-bottom:24px"><?php echo e($current['message']); ?></p>
            <a href="<?php echo e(route('settings.index')); ?>" class="btn btn-accent"><?php echo e(__('payment.back_to_settings')); ?></a>
        </div>
    </div>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\payment\result.blade.php ENDPATH**/ ?>