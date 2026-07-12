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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('zakat.history')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('zakat.history')); ?> <?php $__env->endSlot(); ?>

    <div class="d-flex justify-content-end gap-2 mb-3">
        <?php $canExportZakat = auth()->user()->hasPermission('zakat.export'); ?>
        <?php if (isset($component)) { $__componentOriginal240c555ed297446ed18cd33870eb4d15 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal240c555ed297446ed18cd33870eb4d15 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data-toolbar','data' => ['entity' => 'zakat','showImport' => false,'showExport' => $canExportZakat]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data-toolbar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['entity' => 'zakat','show-import' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'show-export' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canExportZakat)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal240c555ed297446ed18cd33870eb4d15)): ?>
<?php $attributes = $__attributesOriginal240c555ed297446ed18cd33870eb4d15; ?>
<?php unset($__attributesOriginal240c555ed297446ed18cd33870eb4d15); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal240c555ed297446ed18cd33870eb4d15)): ?>
<?php $component = $__componentOriginal240c555ed297446ed18cd33870eb4d15; ?>
<?php unset($__componentOriginal240c555ed297446ed18cd33870eb4d15); ?>
<?php endif; ?>
    </div>

    <div class="card-custom">
        <div class="card-body p-0">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($records->count()): ?>
                <div class="table-responsive">
                    <table class="table-custom">
                    <thead>
                        <tr>
                            <th><?php echo e(__('zakat.calculation_date')); ?></th>
                            <th class="text-end"><?php echo e(__('zakat.total_wealth')); ?></th>
                            <th class="text-end"><?php echo e(__('zakat.total_zakatable')); ?></th>
                            <th><?php echo e(__('zakat.exceeds_nisab')); ?></th>
                            <th class="text-end"><?php echo e(__('zakat.zakat_amount')); ?></th>
                            <th class="text-center" style="width:80px"><?php echo e(__('general.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($record->calculation_date->format('Y/m/d')); ?></td>
                                <td class="text-end"><?php echo e(number_format($record->total_wealth, 2)); ?></td>
                                <td class="text-end"><?php echo e(number_format($record->total_zakatable, 2)); ?></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($record->exceeds_nisab): ?>
                                        <span style="color:var(--success)"><i class="bi bi-check-circle"></i></span>
                                    <?php else: ?>
                                        <span style="color:var(--danger)"><i class="bi bi-x-circle"></i></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="text-end fw-bold" style="color:var(--accent)"><?php echo e(number_format($record->zakat_amount, 2)); ?></td>
                                <td class="text-center">
                                    <a href="<?php echo e(route('zakat.report', $record)); ?>" class="action-btn" title="<?php echo e(__('zakat.report')); ?>">
                                        <i class="bi bi-file-text"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
                </div>
                <div class="p-3">
                    <?php echo e($records->links()); ?>

                </div>
            <?php else: ?>
                <?php echo $__env->make('components.empty-state', [
                    'icon' => 'bi-clock-history',
                    'title' => __('zakat.no_records'),
                    'message' => __('zakat.calculate_first'),
                    'action' => route('zakat.calculator'),
                    'actionText' => __('zakat.calculate'),
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/zakat/history.blade.php ENDPATH**/ ?>