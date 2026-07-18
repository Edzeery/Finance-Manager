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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('report.title')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('report.title')); ?> <?php $__env->endSlot(); ?>

    <div class="row g-4">
        <div class="col-md-4">
            <a href="<?php echo e(route('report.monthly')); ?>" class="text-decoration-none">
                <div class="kpi-card text-center">
                    <div class="kpi-icon mx-auto" style="background: rgba(59,130,246,0.12); color: var(--info)">
                        <i class="bi bi-calendar-month"></i>
                    </div>
                    <div class="kpi-value" style="font-size:18px"><?php echo e(__('report.monthly')); ?></div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?php echo e(route('report.yearly')); ?>" class="text-decoration-none">
                <div class="kpi-card text-center">
                    <div class="kpi-icon mx-auto" style="background: rgba(34,197,94,0.12); color: var(--success)">
                        <i class="bi bi-calendar-year"></i>
                    </div>
                    <div class="kpi-value" style="font-size:18px"><?php echo e(__('report.yearly')); ?></div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <div class="kpi-card text-center">
                <div class="kpi-icon mx-auto" style="background: rgba(255,193,7,0.12); color: var(--accent)">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                </div>
                <div class="kpi-value" style="font-size:18px"><?php echo e(__('report.custom')); ?></div>
            </div>
        </div>
    </div>

    <div class="card-custom mt-4">
        <div class="card-body">
            <?php echo $__env->make('components.empty-state', [
                'icon' => 'bi-file-earmark-bar-graph-fill',
                'title' => __('general.no_data'),
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\report\index.blade.php ENDPATH**/ ?>