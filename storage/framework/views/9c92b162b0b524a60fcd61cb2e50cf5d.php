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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('general.search_results')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('general.search_results')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo app('translator')->get('general.search_for'); ?>: <strong><?php echo e($q); ?></strong> <?php $__env->endSlot(); ?>

    <div class="card-custom">
        <div class="card-body p-0">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($results->count()): ?>
                <div class="table-responsive">
                    <table class="table-custom">
                    <thead>
                        <tr>
                            <th><?php echo e(__('general.type')); ?></th>
                            <th><?php echo e(__('general.description')); ?></th>
                            <th><?php echo e(__('general.category')); ?></th>
                            <th><?php echo e(__('general.date')); ?></th>
                            <th class="text-end"><?php echo e(__('general.amount')); ?></th>
                            <th class="text-center"><?php echo e(__('general.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <?php
                                        $typeStyles = [
                                            'income' => 'success',
                                            'expense' => 'danger',
                                            'debt' => 'warning',
                                            'asset' => 'info',
                                        ];
                                    ?>
                                    <span class="badge bg-<?php echo e($typeStyles[$r->type] ?? 'secondary'); ?>">
                                        <?php echo e(__("{$r->type}.title")); ?>

                                    </span>
                                </td>
                                <td><?php echo e($r->description ?: '—'); ?></td>
                                <td><?php echo e($r->category); ?></td>
                                <td style="white-space:nowrap"><?php echo e($r->date?->format('Y/m/d') ?: '—'); ?></td>
                                <td class="text-end fw-bold"><?php echo e(number_format($r->amount, 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></td>
                                <td class="text-center">
                                    <a href="<?php echo e($r->url); ?>" class="action-btn">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-search" style="font-size:48px; color:var(--text-muted); display:block; margin-bottom:16px"></i>
                    <h5 style="color:var(--text-muted)"><?php echo app('translator')->get('messages.no_search_results'); ?></h5>
                </div>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\search\results.blade.php ENDPATH**/ ?>