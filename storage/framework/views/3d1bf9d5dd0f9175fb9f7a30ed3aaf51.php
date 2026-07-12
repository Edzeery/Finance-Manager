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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('transactions.title')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('transactions.title')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('transactions.all_transactions')); ?> <?php $__env->endSlot(); ?>

    <?php if (isset($component)) { $__componentOriginal526982350b860bbb0ef3834fb35dd9e5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal526982350b860bbb0ef3834fb35dd9e5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-tabs','data' => ['tabs' => $tabs,'current' => ''.e($tab).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-tabs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tabs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tabs),'current' => ''.e($tab).'']); ?>
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

    <div class="card-custom">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('transactions.index')); ?>" id="filterForm">
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-lg-3 col-md-4 col-12">
                        <?php if (isset($component)) { $__componentOriginal33e4867731ced0462908f8cc78d5ea1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-filter','data' => ['name' => 'search','value' => $search ?? request('search'),'placeholder' => ''.e(__('transactions.search_placeholder')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($search ?? request('search')),'placeholder' => ''.e(__('transactions.search_placeholder')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal33e4867731ced0462908f8cc78d5ea1b)): ?>
<?php $attributes = $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b; ?>
<?php unset($__attributesOriginal33e4867731ced0462908f8cc78d5ea1b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal33e4867731ced0462908f8cc78d5ea1b)): ?>
<?php $component = $__componentOriginal33e4867731ced0462908f8cc78d5ea1b; ?>
<?php unset($__componentOriginal33e4867731ced0462908f8cc78d5ea1b); ?>
<?php endif; ?>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <input type="date" name="date_from" class="form-custom" style="width:100%;padding:7px 12px;font-size:13px"
                               value="<?php echo e($dateFrom ?? request('date_from')); ?>" onchange="this.form.submit()">
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <input type="date" name="date_to" class="form-custom" style="width:100%;padding:7px 12px;font-size:13px"
                               value="<?php echo e($dateTo ?? request('date_to')); ?>" onchange="this.form.submit()">
                    </div>
                    <div class="col-lg-2 col-md-2 col-6">
                        <?php if (isset($component)) { $__componentOriginal350cc130478c4b4aced77f6fd760100d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal350cc130478c4b4aced77f6fd760100d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.per-page','data' => ['current' => (int) ($perPage ?? request('per_page', 15)),'options' => [15, 25, 50, 100],'preserve' => ['search','date_from','date_to','sort','direction','tab']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('per-page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((int) ($perPage ?? request('per_page', 15))),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([15, 25, 50, 100]),'preserve' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['search','date_from','date_to','sort','direction','tab'])]); ?>
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
                    <div class="col-lg-3 col-md-4 col-12 d-flex gap-2 justify-content-end">
                        <?php $canExportTxn = auth()->user()->hasPermission('transaction.export'); ?>
                        <?php if (isset($component)) { $__componentOriginal240c555ed297446ed18cd33870eb4d15 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal240c555ed297446ed18cd33870eb4d15 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data-toolbar','data' => ['entity' => 'transactions','showImport' => false,'showExport' => $canExportTxn]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data-toolbar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['entity' => 'transactions','show-import' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'show-export' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canExportTxn)]); ?>
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
                        <?php if (isset($component)) { $__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.clear-filters','data' => ['filters' => ['search','type','date_from','date_to'],'route' => route('transactions.index'),'label' => ''.e(__('transactions.clear_filters')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('clear-filters'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['filters' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['search','type','date_from','date_to']),'route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('transactions.index')),'label' => ''.e(__('transactions.clear_filters')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113)): ?>
<?php $attributes = $__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113; ?>
<?php unset($__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113)): ?>
<?php $component = $__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113; ?>
<?php unset($__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113); ?>
<?php endif; ?>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>
                                <a href="<?php echo e(route('transactions.index', array_merge(request()->query(), ['sort' => 'date', 'direction' => $sortField === 'date' && $sortDir === 'asc' ? 'desc' : 'asc']))); ?>" class="text-decoration-none" style="color:inherit">
                                    <?php echo e(__('transactions.date')); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sortField === 'date'): ?> <i class="bi bi-arrow-<?php echo e($sortDir === 'asc' ? 'up' : 'down'); ?>"></i> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="<?php echo e(route('transactions.index', array_merge(request()->query(), ['sort' => 'type', 'direction' => $sortField === 'type' && $sortDir === 'asc' ? 'desc' : 'asc']))); ?>" class="text-decoration-none" style="color:inherit">
                                    <?php echo e(__('transactions.type')); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sortField === 'type'): ?> <i class="bi bi-arrow-<?php echo e($sortDir === 'asc' ? 'up' : 'down'); ?>"></i> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="<?php echo e(route('transactions.index', array_merge(request()->query(), ['sort' => 'category', 'direction' => $sortField === 'category' && $sortDir === 'asc' ? 'desc' : 'asc']))); ?>" class="text-decoration-none" style="color:inherit">
                                    <?php echo e(__('transactions.category')); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sortField === 'category'): ?> <i class="bi bi-arrow-<?php echo e($sortDir === 'asc' ? 'up' : 'down'); ?>"></i> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </a>
                            </th>
                            <th><?php echo e(__('transactions.description')); ?></th>
                            <th class="text-end">
                                <a href="<?php echo e(route('transactions.index', array_merge(request()->query(), ['sort' => 'amount', 'direction' => $sortField === 'amount' && $sortDir === 'asc' ? 'desc' : 'asc']))); ?>" class="text-decoration-none" style="color:inherit">
                                    <?php echo e(__('transactions.amount')); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sortField === 'amount'): ?> <i class="bi bi-arrow-<?php echo e($sortDir === 'asc' ? 'up' : 'down'); ?>"></i> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </a>
                            </th>
                            <th class="text-center"><?php echo e(__('transactions.status')); ?></th>
                            <th class="text-end"><?php echo e(__('transactions.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $txn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td style="white-space:nowrap"><?php echo e($txn['date']->format('Y/m/d')); ?></td>
                                <td>
                                    <span class="badge" style="background:<?php echo e($txn['type'] === 'income' ? 'rgba(34,197,94,0.12)' : 'rgba(239,68,68,0.12)'); ?>; color:<?php echo e($txn['type'] === 'income' ? 'var(--success)' : 'var(--danger)'); ?>; padding:4px 10px; border-radius:6px; font-weight:500; font-size:12px">
                                        <?php echo e($txn['type'] === 'income' ? __('transactions.income') : __('transactions.expense')); ?>

                                    </span>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($txn['category_name'] !== '—'): ?>
                                        <span class="d-flex align-items-center gap-1">
                                            <span style="width:8px; height:8px; border-radius:50%; display:inline-block; background:<?php echo e($txn['category_color']); ?>; flex-shrink:0"></span>
                                            <span><?php echo e($txn['category_name']); ?></span>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap">
                                    <?php echo e($txn['description'] ?: '—'); ?>

                                </td>
                                <td class="text-end fw-bold" style="color:<?php echo e($txn['type'] === 'income' ? 'var(--success)' : 'var(--danger)'); ?>">
                                    <?php echo e($txn['type'] === 'income' ? '+' : '-'); ?><?php echo e(number_format($txn['amount'], 2)); ?>

                                </td>
                                <td class="text-center">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($txn['is_archived']): ?>
                                        <span class="badge" style="background:rgba(148,163,184,0.12); color:var(--text-muted); padding:4px 8px; border-radius:6px; font-size:11px"><?php echo e(__('transactions.archived')); ?></span>
                                    <?php else: ?>
                                        <span class="badge" style="background:rgba(34,197,94,0.12); color:var(--success); padding:4px 8px; border-radius:6px; font-size:11px"><?php echo e(__('transactions.active')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php
                                        $canEditTxn = auth()->user()->hasPermission($txn['type'] === 'income' ? 'income.update' : 'expense.update');
                                        $canDeleteTxn = auth()->user()->hasPermission($txn['type'] === 'income' ? 'income.delete' : 'expense.delete');
                                    ?>
                                    <div class="d-flex gap-1 justify-content-end">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditTxn): ?>
                                        <a href="<?php echo e($txn['type'] === 'income' ? route('income.edit', $txn['id']) : route('expense.edit', $txn['id'])); ?>"
                                           class="action-btn" title="<?php echo e(__('transactions.edit')); ?>">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteTxn): ?>
                                        <form method="POST" action="<?php echo e($txn['type'] === 'income' ? route('income.destroy', $txn['id']) : route('expense.destroy', $txn['id'])); ?>" id="delete-txn-<?php echo e($txn['id']); ?>" style="display:none">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        </form>
                                        <button type="button" class="action-btn" style="color:var(--danger)"
                                                title="<?php echo e(__('transactions.delete')); ?>" @click="confirmDeleteTxn(<?php echo e($txn['id']); ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'bi bi-inbox','title' => __('transactions.no_transactions'),'message' => __('transactions.no_transactions_desc')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi bi-inbox','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('transactions.no_transactions')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('transactions.no_transactions_desc'))]); ?>
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
                                    <?php $canAddIncome = auth()->user()->hasPermission('income.create'); $canAddExpense = auth()->user()->hasPermission('expense.create'); ?>
                                    <div class="d-flex gap-2 justify-content-center mt-3">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAddIncome): ?>
                                        <a href="<?php echo e(route('income.create')); ?>" class="btn btn-accent btn-custom btn-sm">
                                            <i class="bi bi-plus-circle"></i> <?php echo e(__('general.add')); ?> <?php echo e(__('transactions.income')); ?>

                                        </a>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAddExpense): ?>
                                        <a href="<?php echo e(route('expense.create')); ?>" class="btn btn-custom btn-sm" style="background:var(--danger); color:#fff; border:none">
                                            <i class="bi bi-plus-circle"></i> <?php echo e(__('general.add')); ?> <?php echo e(__('transactions.expense')); ?>

                                        </a>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($transactions->hasPages()): ?>
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-3">
                    <?php if (isset($component)) { $__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination-info','data' => ['items' => $transactions]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination-info'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($transactions)]); ?>
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
                    <?php echo e($transactions->appends(request()->query())->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
    function confirmDeleteTxn(id) {
        const form = document.getElementById('delete-txn-' + id);
        if (!form) return;
        showConfirmModal(
            '<?php echo e(__('general.confirm')); ?>',
            '<?php echo e(__('messages.confirm_delete')); ?>',
            (confirmed) => { if (confirmed) form.submit(); },
            '<?php echo e(__('general.delete')); ?>',
            'btn-danger'
        );
    }
    </script>
    <?php $__env->stopPush(); ?>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\transactions\index.blade.php ENDPATH**/ ?>