<?php if (isset($component)) { $__componentOriginal5ddd1e5ebb9bd9bcef1756d6bbfff042 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5ddd1e5ebb9bd9bcef1756d6bbfff042 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toast.container','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toast.container'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php if (isset($component)) { $__componentOriginal70f0ce12391d107558e9d6e93c455f49 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal70f0ce12391d107558e9d6e93c455f49 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toast.item','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toast.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal70f0ce12391d107558e9d6e93c455f49)): ?>
<?php $attributes = $__attributesOriginal70f0ce12391d107558e9d6e93c455f49; ?>
<?php unset($__attributesOriginal70f0ce12391d107558e9d6e93c455f49); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal70f0ce12391d107558e9d6e93c455f49)): ?>
<?php $component = $__componentOriginal70f0ce12391d107558e9d6e93c455f49; ?>
<?php unset($__componentOriginal70f0ce12391d107558e9d6e93c455f49); ?>
<?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5ddd1e5ebb9bd9bcef1756d6bbfff042)): ?>
<?php $attributes = $__attributesOriginal5ddd1e5ebb9bd9bcef1756d6bbfff042; ?>
<?php unset($__attributesOriginal5ddd1e5ebb9bd9bcef1756d6bbfff042); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5ddd1e5ebb9bd9bcef1756d6bbfff042)): ?>
<?php $component = $__componentOriginal5ddd1e5ebb9bd9bcef1756d6bbfff042; ?>
<?php unset($__componentOriginal5ddd1e5ebb9bd9bcef1756d6bbfff042); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/components/toast.blade.php ENDPATH**/ ?>