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
    <?php
        $displayPrice = function (float $amount, ?string $currency = null) use ($userCurrency) {
            $cur = $currency ?: $userCurrency;
            return number_format($amount, 2) . ' ' . \App\Services\CurrencyHelper::symbol($cur);
        };
    ?>
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('settings.invoices')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('settings.invoices')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('settings.invoices_desc')); ?> <?php $__env->endSlot(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$hasSubscriptions): ?>
        <div class="settings-card">
            <div class="text-center py-5">
                <i class="bi bi-receipt" style="font-size:48px;color:var(--text-muted);opacity:0.4"></i>
                <p class="text-muted mt-3 mb-0"><?php echo e(__('settings.no_subscription')); ?></p>
            </div>
        </div>
    <?php else: ?>
    <?php if (isset($component)) { $__componentOriginal526982350b860bbb0ef3834fb35dd9e5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal526982350b860bbb0ef3834fb35dd9e5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-tabs','data' => ['tabs' => [
        'all' => ['label' => __('general.all'), 'count' => $countAll, 'icon' => 'bi-list-ul'],
        'paid' => ['label' => __('general.paid'), 'count' => $countPaid, 'icon' => 'bi-check-circle'],
        'overdue' => ['label' => __('general.overdue'), 'count' => $countOverdue, 'icon' => 'bi-exclamation-triangle'],
        'draft' => ['label' => __('general.draft'), 'count' => $countDraft, 'icon' => 'bi-pencil'],
        'cancelled' => ['label' => __('general.cancelled'), 'count' => $countCancelled, 'icon' => 'bi-x-circle'],
    ],'current' => ''.e(request('status', 'all')).'','keyParam' => 'status']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-tabs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tabs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        'all' => ['label' => __('general.all'), 'count' => $countAll, 'icon' => 'bi-list-ul'],
        'paid' => ['label' => __('general.paid'), 'count' => $countPaid, 'icon' => 'bi-check-circle'],
        'overdue' => ['label' => __('general.overdue'), 'count' => $countOverdue, 'icon' => 'bi-exclamation-triangle'],
        'draft' => ['label' => __('general.draft'), 'count' => $countDraft, 'icon' => 'bi-pencil'],
        'cancelled' => ['label' => __('general.cancelled'), 'count' => $countCancelled, 'icon' => 'bi-x-circle'],
    ]),'current' => ''.e(request('status', 'all')).'','keyParam' => 'status']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal526982350b860bbb0ef3834fb35dd9e5)): ?>
<?php $attributes = $__attributesOriginal526982350b860bbb0ef3834fb35dd9e5; ?>
<?php unset($__attributesOriginal526982350b860bbb0ef3834fb35dd9e5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal526982350b860bbb0ef3834fb35dd9e5)): ?>
<?php $component = $__componentOriginal526982350b860bbb0ef3834fb35dd9e5; ?>
<?php unset($__componentOriginal526982350b860bbb0ef3834fb35dd9e5); ?>
<?php endif; ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div></div>
        <div class="d-flex gap-2 align-items-center">
            <?php if (isset($component)) { $__componentOriginal350cc130478c4b4aced77f6fd760100d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal350cc130478c4b4aced77f6fd760100d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.per-page','data' => ['current' => (int) request('per_page', 15)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('per-page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((int) request('per_page', 15))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal350cc130478c4b4aced77f6fd760100d)): ?>
<?php $attributes = $__attributesOriginal350cc130478c4b4aced77f6fd760100d; ?>
<?php unset($__attributesOriginal350cc130478c4b4aced77f6fd760100d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal350cc130478c4b4aced77f6fd760100d)): ?>
<?php $component = $__componentOriginal350cc130478c4b4aced77f6fd760100d; ?>
<?php unset($__componentOriginal350cc130478c4b4aced77f6fd760100d); ?>
<?php endif; ?>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th><?php echo e(__('settings.invoice_number')); ?></th>
                            <th><?php echo e(__('settings.invoice_plan')); ?></th>
                            <th><?php echo e(__('settings.invoice_amount')); ?></th>
                            <th><?php echo e(__('settings.invoice_period')); ?></th>
                            <th><?php echo e(__('general.status')); ?></th>
                            <th><?php echo e(__('general.date')); ?></th>
                            <th class="text-end"><?php echo e(__('general.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <code style="font-size:13px;font-weight:600"><?php echo e($invoice->number); ?></code>
                                </td>
                                <td><?php echo e($invoice->subscription?->plan?->name ?? '—'); ?></td>
                                <td style="font-weight:600"><?php echo e($displayPrice($invoice->total, $invoice->currency)); ?></td>
                                <td style="font-size:13px">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoice->period_start && $invoice->period_end): ?>
                                        <?php echo e($invoice->period_start->format('M Y')); ?> — <?php echo e($invoice->period_end->format('M Y')); ?>

                                    <?php else: ?>
                                        —
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                        $badge = match($invoice->status->value) {
                                            'paid' => 'success',
                                            'draft' => 'warning',
                                            'overdue' => 'danger',
                                            'cancelled' => 'secondary',
                                            default => 'primary',
                                        };
                                    ?>
                                    <span class="badge bg-<?php echo e($badge); ?>"><?php echo e($invoice->status->label()); ?></span>
                                </td>
                                <td style="font-size:13px;color:var(--text-muted)"><?php echo e($invoice->created_at->format('Y/m/d')); ?></td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="<?php echo e(route('account.invoices.show', $invoice)); ?>" class="btn btn-sm btn-outline-primary btn-custom" title="<?php echo e(__('general.view')); ?>">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-receipt" style="font-size:40px;color:var(--text-muted);opacity:0.3"></i>
                                    <p class="text-muted mt-2 mb-0"><?php echo e(__('settings.no_invoices')); ?></p>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($invoices) && $invoices->hasPages()): ?>
                <div class="p-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <?php if (isset($component)) { $__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination-info','data' => ['items' => $invoices]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination-info'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoices)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105)): ?>
<?php $attributes = $__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105; ?>
<?php unset($__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105)): ?>
<?php $component = $__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105; ?>
<?php unset($__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105); ?>
<?php endif; ?>
                    <div>
                        <?php echo e($invoices->appends(request()->query())->links()); ?>

                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\account\invoices-index.blade.php ENDPATH**/ ?>