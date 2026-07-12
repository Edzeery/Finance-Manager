<?php
    $perm = fn($p) => auth()->user()->hasPermission("debt.$p");
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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('debt.title')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('debt.title')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> 
        <span style="color:var(--danger)"><?php echo e(__('debt.total_debts_owed')); ?>: <strong><?php echo e(number_format($totalOwed - $paidOwed, 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></strong></span>
        &nbsp;|&nbsp;
        <span style="color:var(--success)"><?php echo e(__('debt.total_debts_owing')); ?>: <strong><?php echo e(number_format($totalOwing - $paidOwing, 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></strong></span>
     <?php $__env->endSlot(); ?>

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
        <form method="GET" action="<?php echo e(route('debt.index')); ?>" class="d-flex flex-wrap align-items-center gap-2">
            <?php if (isset($component)) { $__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-filter','data' => ['name' => 'type','options' => [
                \App\Enums\DebtType::Owed->value => __('debt.owed'),
                \App\Enums\DebtType::Owing->value => __('debt.owing'),
            ],'placeholder' => ''.e(__('general.all')).'','minWidth' => '100px','onchange' => 'this.form.submit()','class' => 'form-custom','style' => 'padding:6px 12px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'type','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                \App\Enums\DebtType::Owed->value => __('debt.owed'),
                \App\Enums\DebtType::Owing->value => __('debt.owing'),
            ]),'placeholder' => ''.e(__('general.all')).'','min-width' => '100px','onchange' => 'this.form.submit()','class' => 'form-custom','style' => 'padding:6px 12px']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c)): ?>
<?php $attributes = $__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c; ?>
<?php unset($__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c)): ?>
<?php $component = $__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c; ?>
<?php unset($__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c); ?>
<?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab !== 'trashed'): ?>
                <?php if (isset($component)) { $__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-filter','data' => ['name' => 'status','options' => [
                    \App\Enums\DebtStatus::Active->value => __('debt.active'),
                    \App\Enums\DebtStatus::Partial->value => __('debt.partial'),
                    \App\Enums\DebtStatus::Paid->value => __('debt.paid'),
                    \App\Enums\DebtStatus::Overdue->value => __('debt.overdue'),
                ],'placeholder' => ''.e(__('general.all')).' '.e(__('general.status')).'','minWidth' => '130px','onchange' => 'this.form.submit()','class' => 'form-custom','style' => 'padding:6px 12px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'status','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    \App\Enums\DebtStatus::Active->value => __('debt.active'),
                    \App\Enums\DebtStatus::Partial->value => __('debt.partial'),
                    \App\Enums\DebtStatus::Paid->value => __('debt.paid'),
                    \App\Enums\DebtStatus::Overdue->value => __('debt.overdue'),
                ]),'placeholder' => ''.e(__('general.all')).' '.e(__('general.status')).'','min-width' => '130px','onchange' => 'this.form.submit()','class' => 'form-custom','style' => 'padding:6px 12px']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c)): ?>
<?php $attributes = $__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c; ?>
<?php unset($__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c)): ?>
<?php $component = $__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c; ?>
<?php unset($__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.clear-filters','data' => ['filters' => ['type','status','search'],'route' => route('debt.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('clear-filters'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['filters' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['type','status','search']),'route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('debt.index'))]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data-toolbar','data' => ['entity' => 'debt','showExport' => $canExport]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data-toolbar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['entity' => 'debt','show-export' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canExport)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.per-page','data' => ['current' => request('per_page', 15),'route' => route('debt.index'),'preserve' => ['type','status','search','tab']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('per-page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('per_page', 15)),'route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('debt.index')),'preserve' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['type','status','search','tab'])]); ?>
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
                <a href="<?php echo e(route('debt.create')); ?>" class="btn btn-accent btn-custom">
                    <i class="bi bi-plus-lg me-1"></i><?php echo e(__('debt.add')); ?>

                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <form id="bulkForm" method="POST"
          data-bulk-delete-route="<?php echo e(route('debt.bulk-delete')); ?>"
          data-bulk-force-delete-route="<?php echo e(route('debt.bulk-force-delete')); ?>">
        <?php echo csrf_field(); ?>
    </form>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $debts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <form id="delete-form-debt-<?php echo e($debt->id); ?>" action="<?php echo e(route('debt.destroy', $debt)); ?>" method="POST" style="display:none">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        </form>
        <form id="force-delete-form-debt-<?php echo e($debt->id); ?>" action="<?php echo e(route('debt.force-delete', $debt->id)); ?>" method="POST" style="display:none">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        </form>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="card-custom">
        <div class="card-body p-0">
            <div id="bulkBar" class="bulk-bar" style="display:none; margin:12px">
                <span id="selectedCount">0</span> <span><?php echo e(__('general.selected')); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'trashed'): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canRestore): ?>
                        <button type="submit" form="bulkForm" formaction="<?php echo e(route('debt.bulk-restore')); ?>" class="btn btn-sm btn-outline-success btn-custom">
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
                        <button type="button" class="btn btn-sm btn-outline-danger btn-custom" @click="confirmBulkDelete('debt')">
                            <i class="bi bi-trash me-1"></i><?php echo e(__('general.delete')); ?>

                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($debts->count()): ?>
                <div class="table-responsive">
                    <table class="table-custom">
                    <thead>
                        <tr>
                            <th style="width:40px"><input type="checkbox" id="selectAll" @change="toggleSelectAll($el)"></th>
                            <th><?php echo e(__('debt.type')); ?></th>
                            <th><?php echo e(__('debt.counterparty')); ?></th>
                            <th class="text-end"><?php echo e(__('debt.total_amount')); ?></th>
                            <th class="text-end"><?php echo e(__('debt.remaining_amount')); ?></th>
                            <th><?php echo e(__('debt.due_date')); ?></th>
                            <th><?php echo e(__('general.status')); ?></th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasActions): ?>
                            <th class="text-center" style="width:80px"><?php echo e(__('general.actions')); ?></th>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $debts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr <?php if($tab === 'trashed'): ?> style="opacity:0.7" <?php else: ?> class="<?php echo e($debt->is_overdue ? 'bg-overdue' : ''); ?>" <?php endif; ?>>
                                <td><input type="checkbox" name="ids[]" value="<?php echo e($debt->id); ?>" class="select-item" form="bulkForm"></td>
                                <td>
                                    <span class="badge badge-custom <?php echo e($debt->type === \App\Enums\DebtType::Owed ? 'badge-expense' : 'badge-income'); ?>">
                                        <?php echo e($debt->type === \App\Enums\DebtType::Owed ? __('debt.owed') : __('debt.owing')); ?>

                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('debt.show', $debt)); ?>" class="text-decoration-none" style="color:var(--text); font-weight:500">
                                        <?php echo e($debt->counterparty_name); ?>

                                    </a>
                                </td>
                                <td class="text-end"><?php echo e(number_format($debt->total_amount, 2)); ?></td>
                                <td class="text-end fw-bold <?php echo e($debt->remaining_amount > 0 ? 'text-danger' : 'text-success'); ?>">
                                    <?php echo e(number_format($debt->remaining_amount, 2)); ?>

                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($debt->due_date): ?>
                                        <span style="color:<?php echo e($debt->is_overdue ? 'var(--danger)' : 'var(--text)'); ?>">
                                            <?php echo e($debt->due_date->format('Y/m/d')); ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($debt->is_overdue): ?>
                                                <i class="bi bi-exclamation-triangle-fill ms-1" style="color:var(--danger)" title="<?php echo e(__('debt.overdue')); ?>"></i>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </span>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                        $statusColors = [
                                            \App\Enums\DebtStatus::Active->value => 'var(--primary)',
                                            \App\Enums\DebtStatus::Partial->value => 'var(--warning)',
                                            \App\Enums\DebtStatus::Paid->value => 'var(--success)',
                                            \App\Enums\DebtStatus::Overdue->value => 'var(--danger)',
                                        ];
                                    ?>
                                    <span class="badge-custom badge-status" style="background:<?php echo e($statusColors[$debt->status->value] ?? 'var(--text-muted)'); ?>10; color:<?php echo e($statusColors[$debt->status->value] ?? 'var(--text-muted)'); ?>; border:1px solid <?php echo e($statusColors[$debt->status->value] ?? 'var(--text-muted)'); ?>40">
                                        <?php echo e(__("debt.{$debt->status->value}")); ?>

                                    </span>
                                </td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasActions): ?>
                                <td class="text-center">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'trashed'): ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canRestore): ?>
                                            <form action="<?php echo e(route('debt.restore', $debt)); ?>" method="POST" style="display:inline">
                                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="action-btn" title="<?php echo e(__('general.restore')); ?>" style="color:var(--success)">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canForceDelete): ?>
                                            <button type="button" class="action-btn" title="<?php echo e(__('general.force_delete')); ?>" style="color:var(--danger)" @click="confirmForceDelete('debt', <?php echo e($debt->id); ?>)">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php else: ?>
                                        <div class="action-group justify-content-center">
                                            <a href="<?php echo e(route('debt.show', $debt)); ?>" class="action-btn" title="<?php echo e(__('general.view')); ?>">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canUpdate): ?>
                                                <a href="<?php echo e(route('debt.edit', $debt)); ?>" class="action-btn" title="<?php echo e(__('general.edit')); ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDelete): ?>
                                                <button type="button" class="action-btn" title="<?php echo e(__('general.delete')); ?>" @click="confirmDelete('debt', <?php echo e($debt->id); ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
                </div>
                <div class="p-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <?php if (isset($component)) { $__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination-info','data' => ['items' => $debts]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination-info'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($debts)]); ?>
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
                        <?php echo e($debts->appends(request()->except('page'))->links()); ?>

                    </div>
                </div>
            <?php else: ?>
                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'bi-credit-card-2-front','title' => $tab === 'trashed' ? __('general.no_data') : __('debt.no_debts'),'message' => $tab === 'trashed' ? __('messages.no_trashed') : __('debt.create_first_debt'),'action' => $tab === 'trashed' ? route('debt.index') : ($canCreate ? route('debt.create') : '#'),'actionText' => $tab === 'trashed' ? __('general.back') : __('debt.add')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi-credit-card-2-front','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tab === 'trashed' ? __('general.no_data') : __('debt.no_debts')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tab === 'trashed' ? __('messages.no_trashed') : __('debt.create_first_debt')),'action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tab === 'trashed' ? route('debt.index') : ($canCreate ? route('debt.create') : '#')),'actionText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tab === 'trashed' ? __('general.back') : __('debt.add'))]); ?>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\debt\index.blade.php ENDPATH**/ ?>