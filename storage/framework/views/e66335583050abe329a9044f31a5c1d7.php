<?php
    $perm = fn($p) => auth()->user()->hasPermission("budget.$p");
    $canCreate = $perm('create');
    $canUpdate = $perm('update');
    $canDelete = $perm('delete');
    $canRestore = $perm('restore');
    $canForceDelete = $perm('force-delete');
    $canExport = $perm('export');
    $hasActions = $canUpdate || $canDelete || $canRestore || $canForceDelete;
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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('budget.title')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('budget.title')); ?> <?php $__env->endSlot(); ?>

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

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <form method="GET" action="<?php echo e(route('budget.index')); ?>" class="d-flex flex-wrap align-items-center gap-2">
            <?php if (isset($component)) { $__componentOriginal33e4867731ced0462908f8cc78d5ea1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-filter','data' => ['name' => 'search','value' => request('search'),'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('search')),'size' => 'sm']); ?>
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
            <?php if (isset($component)) { $__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.clear-filters','data' => ['filters' => ['search'],'route' => route('budget.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('clear-filters'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['filters' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['search']),'route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('budget.index'))]); ?>
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
        </form>

        <div class="d-flex gap-2 align-items-center">
            <?php if (isset($component)) { $__componentOriginal240c555ed297446ed18cd33870eb4d15 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal240c555ed297446ed18cd33870eb4d15 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data-toolbar','data' => ['entity' => 'budget','showExport' => $canExport]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data-toolbar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['entity' => 'budget','show-export' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canExport)]); ?>
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
            <?php if (isset($component)) { $__componentOriginal350cc130478c4b4aced77f6fd760100d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal350cc130478c4b4aced77f6fd760100d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.per-page','data' => ['current' => request('per_page', 15),'route' => route('budget.index'),'preserve' => ['search','tab']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('per-page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('per_page', 15)),'route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('budget.index')),'preserve' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['search','tab'])]); ?>
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
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab !== 'trashed' && $canCreate): ?>
                <a href="<?php echo e(route('budget.create')); ?>" class="btn btn-accent btn-custom">
                    <i class="bi bi-plus-lg me-1"></i><?php echo e(__('budget.add')); ?>

                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <form id="bulkForm" method="POST"
          data-bulk-delete-route="<?php echo e(route('budget.bulk-delete')); ?>"
          data-bulk-force-delete-route="<?php echo e(route('budget.bulk-force-delete')); ?>">
        <?php echo csrf_field(); ?>
    </form>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $budgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $budget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <form id="delete-form-budget-<?php echo e($budget->id); ?>" action="<?php echo e(route('budget.destroy', $budget)); ?>" method="POST" style="display:none">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        </form>
        <form id="force-delete-form-budget-<?php echo e($budget->id); ?>" action="<?php echo e(route('budget.force-delete', $budget->id)); ?>" method="POST" style="display:none">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        </form>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="bulk-bar mb-3" id="bulkBar" style="display:none">
        <div class="d-flex align-items-center gap-3">
            <input type="checkbox" id="selectAll" @change="toggleSelectAll($el)" style="cursor:pointer">
            <span style="color:var(--text-muted); font-size:13px"><span id="selectedCount">0</span> <?php echo e(__('general.selected')); ?></span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'trashed'): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canRestore): ?>
                    <button type="button" class="btn btn-sm btn-outline-success btn-custom" @click="submitBulk('<?php echo e(route('budget.bulk-restore')); ?>')">
                        <i class="bi bi-arrow-counterclockwise me-1"></i><?php echo e(__('general.restore')); ?>

                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canForceDelete): ?>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-custom" @click="confirmBulkForceDelete()">
                        <i class="bi bi-trash3 me-1"></i><?php echo e(__('general.force_delete')); ?>

                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php else: ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDelete): ?>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-custom" @click="confirmBulkDelete('budget')">
                        <i class="bi bi-trash me-1"></i><?php echo e(__('general.delete')); ?>

                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($budgets->count()): ?>
        <div class="row g-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $budgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $budget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $pct = $budget->adherence_rate;
                    $isExceeded = $budget->is_exceeded;
                    $barColor = $isExceeded ? 'var(--danger)' : ($pct > 80 ? 'var(--warning)' : 'var(--success)');
                ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card-custom budget-card" <?php if($tab === 'trashed'): ?> style="opacity:0.7" <?php endif; ?>>
                        <div class="card-body position-relative">
                            <input type="checkbox" name="ids[]" value="<?php echo e($budget->id); ?>" class="select-item" form="bulkForm" style="position:absolute; top:12px; right:12px; cursor:pointer; z-index:2">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="mb-1" style="font-size:16px; font-weight:600">
                                        <a href="<?php echo e(route('budget.show', $budget)); ?>" class="text-decoration-none" style="color:var(--text)">
                                            <?php echo e(locale_name($budget)); ?>

                                        </a>
                                    </h5>
                                    <span class="badge badge-custom" style="background:var(--bg); color:var(--text-muted); font-size:11px">
                                        <?php echo e(__("budget.{$budget->type}")); ?>

                                    </span>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$budget->is_active): ?>
                                    <span class="badge-custom badge-status" style="background:rgba(100,116,139,0.1); color:var(--text-muted); border:1px solid rgba(100,116,139,0.3)">
                                        <?php echo e(__('general.inactive')); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="d-flex justify-content-between mb-2" style="font-size:13px">
                                <span style="color:var(--text-muted)"><?php echo e(__('budget.spent')); ?></span>
                                <span class="fw-bold" style="color:<?php echo e($barColor); ?>"><?php echo e(number_format($budget->totalSpent, 2)); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2" style="font-size:13px">
                                <span style="color:var(--text-muted)"><?php echo e(__('budget.remaining')); ?></span>
                                <span class="fw-bold"><?php echo e(number_format(max(0, $budget->total_amount - $budget->totalSpent), 2)); ?></span>
                            </div>

                            <div class="progress" style="height:10px; background:var(--border); border-radius:5px; margin-top:12px">
                                <div class="progress-bar" style="width:<?php echo e(min($pct, 100)); ?>%; background:<?php echo e($barColor); ?>; border-radius:5px; transition:width 0.4s"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1" style="font-size:12px">
                                <span style="color:var(--text-muted)"><?php echo e(__('budget.total_amount')); ?>: <?php echo e(number_format($budget->total_amount, 2)); ?></span>
                                <span class="fw-bold" style="color:<?php echo e($barColor); ?>"><?php echo e($pct); ?>%</span>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isExceeded): ?>
                                <div class="mt-2" style="font-size:12px; color:var(--danger); background:rgba(239,68,68,0.08); padding:6px 10px; border-radius:6px">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i><?php echo e(__('budget.exceeded')); ?>

                                </div>
                            <?php elseif($pct > 80): ?>
                                <div class="mt-2" style="font-size:12px; color:var(--warning); background:rgba(245,158,11,0.08); padding:6px 10px; border-radius:6px">
                                    <i class="bi bi-exclamation-circle me-1"></i><?php echo e(__('budget.warning', ['percent' => $pct])); ?>

                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="d-flex gap-2 mt-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'trashed'): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canRestore): ?>
                                        <form action="<?php echo e(route('budget.restore', $budget)); ?>" method="POST" style="display:inline; flex:1">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-success btn-custom" style="flex:1">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i><?php echo e(__('general.restore')); ?>

                                            </button>
                                        </form>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canForceDelete): ?>
                                        <button type="button" class="action-btn" title="<?php echo e(__('general.force_delete')); ?>" style="color:var(--danger)" @click="confirmForceDelete('budget', <?php echo e($budget->id); ?>)">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <a href="<?php echo e(route('budget.show', $budget)); ?>" class="btn btn-sm btn-outline-secondary btn-custom" style="flex:1">
                                        <i class="bi bi-eye me-1"></i><?php echo e(__('general.details')); ?>

                                    </a>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canUpdate): ?>
                                        <a href="<?php echo e(route('budget.edit', $budget)); ?>" class="action-btn" title="<?php echo e(__('general.edit')); ?>">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDelete): ?>
                                        <button type="button" class="action-btn" title="<?php echo e(__('general.delete')); ?>" @click="confirmDelete('budget', <?php echo e($budget->id); ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="mt-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <?php if (isset($component)) { $__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination-info','data' => ['items' => $budgets]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination-info'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($budgets)]); ?>
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
                <?php echo e($budgets->appends(request()->except('page'))->links()); ?>

            </div>
        </div>
    <?php else: ?>
        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'bi-pie-chart','title' => __('general.no_data'),'message' => $tab === 'trashed' ? __('messages.no_trashed') : __('budget.create_first_budget'),'action' => $tab === 'trashed' ? route('budget.index') : ($canCreate ? route('budget.create') : '#'),'actionText' => $tab === 'trashed' ? __('general.back') : __('budget.add')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi-pie-chart','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('general.no_data')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tab === 'trashed' ? __('messages.no_trashed') : __('budget.create_first_budget')),'action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tab === 'trashed' ? route('budget.index') : ($canCreate ? route('budget.create') : '#')),'actionText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tab === 'trashed' ? __('general.back') : __('budget.add'))]); ?>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\budget\index.blade.php ENDPATH**/ ?>