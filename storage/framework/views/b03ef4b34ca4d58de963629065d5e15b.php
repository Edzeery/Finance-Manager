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
     <?php $__env->slot('title', null, []); ?> <?php echo e(locale_name($budget)); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(locale_name($budget)); ?> <?php $__env->endSlot(); ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-list-ul"></i>
                        <span><?php echo e(__('budget.categories')); ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($budget->categories->count()): ?>
                        <div class="table-responsive">
                            <table class="table-custom">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('budget.category')); ?></th>
                                    <th class="text-end"><?php echo e(__('budget.allocated_amount')); ?></th>
                                    <th class="text-end"><?php echo e(__('budget.spent_amount')); ?></th>
                                    <th class="text-end"><?php echo e(__('budget.remaining')); ?></th>
                                    <th style="width:120px"><?php echo e(__('budget.adherence')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $budget->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $pct = $bc->allocated_amount > 0 ? round(($bc->spent_amount / $bc->allocated_amount) * 100, 1) : 0;
                                        $color = $pct > 100 ? 'var(--danger)' : ($pct > 80 ? 'var(--warning)' : 'var(--success)');
                                    ?>
                                    <tr>
                                        <td>
                                            <i class="<?php echo e($bc->category?->icon ?? 'bi-tag'); ?>" style="color:<?php echo e($bc->category?->color ?? '#64748B'); ?>"></i>
                                            <?php echo e(locale_name($bc->category ?? new stdClass)); ?>

                                        </td>
                                        <td class="text-end"><?php echo e(number_format($bc->allocated_amount, 2)); ?></td>
                                        <td class="text-end fw-bold" style="color:var(--danger)"><?php echo e(number_format($bc->spent_amount, 2)); ?></td>
                                        <td class="text-end fw-bold" style="color:<?php echo e($bc->spent_amount > $bc->allocated_amount ? 'var(--danger)' : 'var(--success)'); ?>">
                                            <?php echo e(number_format(max(0, $bc->allocated_amount - $bc->spent_amount), 2)); ?>

                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress" style="flex:1; height:6px; background:var(--border); border-radius:3px">
                                                    <div class="progress-bar" style="width:<?php echo e(min($pct, 100)); ?>%; background:<?php echo e($color); ?>; border-radius:3px"></div>
                                                </div>
                                                <span style="font-size:12px; font-weight:600; color:<?php echo e($color); ?>"><?php echo e($pct); ?>%</span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr style="font-weight:700">
                                    <td><?php echo e(__('budget.total_amount')); ?></td>
                                    <td class="text-end"><?php echo e(number_format($budget->total_amount, 2)); ?></td>
                                    <td class="text-end" style="color:var(--danger)"><?php echo e(number_format($budget->totalSpent, 2)); ?></td>
                                    <td class="text-end" style="color:<?php echo e($budget->is_exceeded ? 'var(--danger)' : 'var(--success)'); ?>">
                                        <?php echo e(number_format(max(0, $budget->total_amount - $budget->totalSpent), 2)); ?>

                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress" style="flex:1; height:8px; background:var(--border); border-radius:4px">
                                                <div class="progress-bar" style="width:<?php echo e(min($budget->adherence_rate, 100)); ?>%; background:<?php echo e($budget->is_exceeded ? 'var(--danger)' : ($budget->adherence_rate > 80 ? 'var(--warning)' : 'var(--success)')); ?>; border-radius:4px"></div>
                                            </div>
                                            <span style="font-size:13px; font-weight:700"><?php echo e($budget->adherence_rate); ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                    <?php else: ?>
                        <div class="p-4 text-center" style="color:var(--text-muted)"><?php echo e(__('general.no_data')); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle"></i>
                        <span><?php echo e(__('budget.single')); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('budget.type')); ?></span>
                        <span class="info-value"><?php echo e(__("budget.{$budget->type}")); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('budget.total_amount')); ?></span>
                        <span class="info-value"><?php echo e(number_format($budget->total_amount, 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('budget.spent')); ?></span>
                        <span class="info-value" style="color:var(--danger)"><?php echo e(number_format($budget->totalSpent, 2)); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('budget.remaining')); ?></span>
                        <span class="info-value" style="color:<?php echo e($budget->is_exceeded ? 'var(--danger)' : 'var(--success)'); ?>">
                            <?php echo e(number_format(max(0, $budget->total_amount - $budget->totalSpent), 2)); ?>

                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('budget.start_date')); ?></span>
                        <span class="info-value"><?php echo e($budget->start_date->format('Y/m/d')); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('budget.end_date')); ?></span>
                        <span class="info-value"><?php echo e($budget->end_date?->format('Y/m/d') ?: 'â€”'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('budget.is_active')); ?></span>
                        <span class="info-value"><?php echo e($budget->is_active ? __('general.yes') : __('general.no')); ?></span>
                    </div>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($budget->notes): ?>
                <div class="card-custom mt-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2"><?php echo e(__('budget.notes')); ?></h6>
                        <p style="font-size:14px; color:var(--text-muted); margin:0"><?php echo e($budget->notes); ?></p>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php $canEditBudget = auth()->user()->hasPermission('budget.update'); $canDeleteBudget = auth()->user()->hasPermission('budget.delete'); ?>
            <div class="d-flex gap-2 mt-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditBudget): ?>
                    <a href="<?php echo e(route('budget.edit', $budget)); ?>" class="btn btn-outline-secondary btn-custom" style="flex:1">
                        <i class="bi bi-pencil me-1"></i><?php echo e(__('general.edit')); ?>

                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteBudget): ?>
                    <form action="<?php echo e(route('budget.destroy', $budget)); ?>" method="POST" id="delete-budget-<?php echo e($budget->id); ?>" style="display:none">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    </form>
                    <button type="button" class="btn btn-outline-danger btn-custom w-100" @click="window.confirmDelete('budget', <?php echo e($budget->id); ?>)">
                        <i class="bi bi-trash me-1"></i><?php echo e(__('general.delete')); ?>

                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/budget/show.blade.php ENDPATH**/ ?>