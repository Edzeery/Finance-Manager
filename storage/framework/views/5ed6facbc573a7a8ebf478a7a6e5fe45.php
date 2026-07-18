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
     <?php $__env->slot('title', null, []); ?> <?php echo e($debt->counterparty_name); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e($debt->counterparty_name); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> 
        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'debt_type','status' => $debt->type->value,'set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'debt_type','status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($debt->type->value),'set' => 'bi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
        &nbsp;| <?php echo e(__('debt.remaining_amount')); ?>: <strong><?php echo e(number_format($debt->remaining_amount, 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></strong>
     <?php $__env->endSlot(); ?>


    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-box stat-income">
                <div class="stat-label"><?php echo e(__('debt.total_amount')); ?></div>
                <div class="stat-value"><?php echo e(number_format($debt->total_amount, 2)); ?></div>
                <small style="color:var(--text-muted)"><?php echo e(config('finance.currency_symbol')); ?></small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box stat-expense">
                <div class="stat-label"><?php echo e(__('debt.remaining_amount')); ?></div>
                <div class="stat-value"><?php echo e(number_format($debt->remaining_amount, 2)); ?></div>
                <small style="color:var(--text-muted)"><?php echo e(config('finance.currency_symbol')); ?></small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box" style="background:var(--bg)">
                <div class="stat-label"><?php echo e(__('general.status')); ?></div>
                <div class="d-flex align-items-center gap-2">
                    <div class="progress" style="flex:1; height:8px; background:var(--border)">
                        <div class="progress-bar" style="width:<?php echo e($debt->progress); ?>%; background:<?php echo e($debt->progress >= 100 ? 'var(--success)' : 'var(--accent)'); ?>; border-radius:4px"></div>
                    </div>
                    <span class="fw-bold" style="font-size:14px"><?php echo e($debt->progress); ?>%</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history"></i>
                        <span><?php echo e(__('debt.payment_history')); ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($debt->payments->count()): ?>
                        <div class="table-responsive">
                            <table class="table-custom">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('debt.payment_date')); ?></th>
                                    <th class="text-end"><?php echo e(__('debt.payment_amount')); ?></th>
                                    <th><?php echo e(__('debt.payment_notes')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $debt->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($payment->payment_date->format('Y/m/d')); ?></td>
                                        <td text-start fw-bold style="color:var(--success)">-<?php echo e(number_format($payment->amount, 2)); ?></td>
                                        <td><?php echo e($payment->notes ?: 'â€”'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                        </div>
                    <?php else: ?>
                        <div class="p-4 text-center" style="color:var(--text-muted); font-size:14px">
                            <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'bi bi-inbox','title' => __('general.no_data')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi bi-inbox','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('general.no_data'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($debt->status !== \App\Enums\DebtStatus::Paid): ?>
                <div class="card-custom mt-4">
                    <div class="card-header-custom">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle"></i>
                        <span><?php echo e(__('debt.add_payment')); ?></span>
                    </h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('debt.payments.store', $debt)); ?>" method="POST" class="row g-3">
                            <?php echo csrf_field(); ?>
                            <div class="col-md-4">
                                <label class="form-label-custom"><?php echo e(__('debt.payment_amount')); ?> <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0.01" name="amount" class="form-custom" required placeholder="0.00" max="<?php echo e($debt->remaining_amount); ?>">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px"><?php echo e(config('finance.currency_symbol')); ?></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom"><?php echo e(__('debt.payment_date')); ?> <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-custom" required value="<?php echo e(date('Y-m-d')); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom"><?php echo e(__('debt.payment_notes')); ?></label>
                                <input type="text" name="notes" class="form-custom" maxlength="1000">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-accent btn-custom">
                                    <i class="bi bi-check-lg me-1"></i><?php echo e(__('debt.add_payment')); ?>

                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="col-lg-5">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle"></i>
                        <span><?php echo e(__('debt.single')); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('debt.counterparty')); ?></span>
                        <span class="info-value"><?php echo e($debt->counterparty_name); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('debt.due_date')); ?></span>
                        <span class="info-value"><?php echo e($debt->due_date?->format('Y/m/d') ?: 'â€”'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('debt.reminder_date')); ?></span>
                        <span class="info-value"><?php echo e($debt->reminder_date?->format('Y/m/d') ?: 'â€”'); ?></span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($debt->description): ?>
                        <div class="info-row">
                            <span class="info-label"><?php echo e(__('debt.description')); ?></span>
                            <span class="info-value"><?php echo e($debt->description); ?></span>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($debt->notes): ?>
                        <div class="info-row">
                            <span class="info-label"><?php echo e(__('debt.notes')); ?></span>
                            <span class="info-value"><?php echo e($debt->notes); ?></span>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php $canEditDebt = auth()->user()->hasPermission('debt.update'); $canDeleteDebt = auth()->user()->hasPermission('debt.delete'); ?>
                    <div class="d-flex gap-2 mt-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditDebt): ?>
                            <a href="<?php echo e(route('debt.edit', $debt)); ?>" class="btn btn-outline-secondary btn-custom" style="flex:1">
                                <i class="bi bi-pencil me-1"></i><?php echo e(__('general.edit')); ?>

                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteDebt): ?>
                            <form action="<?php echo e(route('debt.destroy', $debt)); ?>" method="POST" id="delete-debt-<?php echo e($debt->id); ?>" style="display:none">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            </form>
                            <button type="button" class="btn btn-outline-danger btn-custom w-100" @click="window.confirmDelete('debt', <?php echo e($debt->id); ?>)">
                                <i class="bi bi-trash me-1"></i><?php echo e(__('general.delete')); ?>

                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\debt\show.blade.php ENDPATH**/ ?>