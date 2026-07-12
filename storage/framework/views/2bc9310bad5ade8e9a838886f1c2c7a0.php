<?php
    $perm = fn($p) => auth()->user()->hasPermission("asset.$p");
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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('asset.title')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('asset.title')); ?> <?php $__env->endSlot(); ?>

    
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:rgba(21,183,108,0.1); color:var(--accent)">
                    <i class="bi bi-safe2"></i>
                </div>
                <div class="kpi-label"><?php echo e(__('asset.total_assets')); ?></div>
                <div class="kpi-value"><?php echo e(number_format($totalValue, 2)); ?> <small style="font-size:14px; font-weight:400; color:var(--text-muted)"><?php echo e(config('finance.currency_symbol')); ?></small></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:rgba(21,183,108,0.1); color:var(--accent)">
                    <i class="bi bi-water"></i>
                </div>
                <div class="kpi-label"><?php echo e(__('asset.total_liquid')); ?></div>
                <div class="kpi-value"><?php echo e(number_format($liquidValue, 2)); ?> <small style="font-size:14px; font-weight:400; color:var(--text-muted)"><?php echo e(config('finance.currency_symbol')); ?></small></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:rgba(245,158,11,0.1); color:var(--warning)">
                    <i class="bi bi-heart"></i>
                </div>
                <div class="kpi-label"><?php echo e(__('asset.total_zakatable')); ?></div>
                <div class="kpi-value"><?php echo e(number_format($zakatableValue, 2)); ?> <small style="font-size:14px; font-weight:400; color:var(--text-muted)"><?php echo e(config('finance.currency_symbol')); ?></small></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:rgba(59,130,246,0.1); color:var(--info)">
                    <i class="bi bi-boxes"></i>
                </div>
                <div class="kpi-label"><?php echo e(__('asset.total_count')); ?></div>
                <div class="kpi-value"><?php echo e($assets->total()); ?></div>
            </div>
        </div>
    </div>

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

    <?php
        $typeOptions = collect($types)->mapWithKeys(fn($t) => [$t->value => $t->label()])->toArray();
    ?>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <form method="GET" action="<?php echo e(route('asset.index')); ?>" class="d-flex flex-wrap align-items-center gap-2">
            <?php if (isset($component)) { $__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-filter','data' => ['name' => 'type','options' => $typeOptions,'placeholder' => ''.e(__('general.all')).' '.e(__('asset.type')).'','minWidth' => '140px','onchange' => 'this.form.submit()','class' => 'form-custom','style' => 'padding:6px 12px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'type','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($typeOptions),'placeholder' => ''.e(__('general.all')).' '.e(__('asset.type')).'','min-width' => '140px','onchange' => 'this.form.submit()','class' => 'form-custom','style' => 'padding:6px 12px']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.clear-filters','data' => ['filters' => ['type','search'],'route' => route('asset.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('clear-filters'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['filters' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['type','search']),'route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('asset.index'))]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data-toolbar','data' => ['entity' => 'asset','showExport' => $canExport]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data-toolbar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['entity' => 'asset','show-export' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($canExport)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.per-page','data' => ['current' => request('per_page', 15),'route' => route('asset.index'),'preserve' => ['type','search','tab']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('per-page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('per_page', 15)),'route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('asset.index')),'preserve' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['type','search','tab'])]); ?>
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
                <a href="<?php echo e(route('asset.create')); ?>" class="btn btn-accent btn-custom">
                    <i class="bi bi-plus-lg me-1"></i><?php echo e(__('asset.add')); ?>

                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <form id="bulkForm" method="POST"
          data-bulk-delete-route="<?php echo e(route('asset.bulk-delete')); ?>"
          data-bulk-force-delete-route="<?php echo e(route('asset.bulk-force-delete')); ?>"><?php echo csrf_field(); ?></form>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <form id="delete-form-asset-<?php echo e($asset->id); ?>" action="<?php echo e(route('asset.destroy', $asset)); ?>" method="POST" style="display:none">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        </form>
        <form id="force-delete-form-asset-<?php echo e($asset->id); ?>" action="<?php echo e(route('asset.force-delete', $asset->id)); ?>" method="POST" style="display:none">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        </form>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="card-custom">
        <div class="card-body p-0">
            <div id="bulkBar" class="bulk-bar" style="display:none; margin:12px">
                <span id="selectedCount">0</span> <span><?php echo e(__('general.selected')); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'trashed'): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canRestore): ?>
                        <button type="submit" form="bulkForm" formaction="<?php echo e(route('asset.bulk-restore')); ?>" class="btn btn-sm btn-outline-success btn-custom">
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
                        <button type="button" class="btn btn-sm btn-outline-danger btn-custom" @click="confirmBulkDelete('asset')">
                            <i class="bi bi-trash me-1"></i><?php echo e(__('general.delete')); ?>

                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($assets->count()): ?>
                <div class="table-responsive">
                    <table class="table-custom">
                    <thead>
                        <tr>
                            <th style="width:40px"><input type="checkbox" id="selectAll" @change="toggleSelectAll($el)"></th>
                            <th><?php echo e(__('asset.type')); ?></th>
                            <th><?php echo e(__('asset.name')); ?></th>
                            <th class="text-end"><?php echo e(__('asset.quantity')); ?></th>
                            <th class="text-end"><?php echo e(__('asset.total_value')); ?></th>
                            <th><?php echo e(__('asset.is_liquid')); ?></th>
                            <th><?php echo e(__('asset.is_zakatable')); ?></th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasActions): ?>
                            <th class="text-center" style="width:80px"><?php echo e(__('general.actions')); ?></th>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $assetType = $asset->type; ?>
                            <tr <?php if($tab === 'trashed'): ?> style="opacity:0.7" <?php endif; ?>>
                                <td><input type="checkbox" name="ids[]" value="<?php echo e($asset->id); ?>" class="select-item" form="bulkForm"></td>
                                <td>
                                    <?php
                                        $typeColors = [
                                            'cash' => ['bg' => 'rgba(34,197,94,0.12)', 'color' => 'var(--success)'],
                                            'bank_account' => ['bg' => 'rgba(59,130,246,0.12)', 'color' => 'var(--info)'],
                                            'ccp' => ['bg' => 'rgba(139,92,246,0.12)', 'color' => '#8B5CF6'],
                                            'gold' => ['bg' => 'rgba(245,158,11,0.12)', 'color' => 'var(--warning)'],
                                            'silver' => ['bg' => 'rgba(148,163,184,0.12)', 'color' => '#94A3B8'],
                                            'real_estate' => ['bg' => 'rgba(239,68,68,0.12)', 'color' => 'var(--danger)'],
                                            'stocks' => ['bg' => 'rgba(34,197,94,0.12)', 'color' => 'var(--success)'],
                                            'crypto' => ['bg' => 'rgba(245,158,11,0.12)', 'color' => 'var(--warning)'],
                                            'other' => ['bg' => 'rgba(100,116,139,0.12)', 'color' => 'var(--text-muted)'],
                                        ];
                                        $tc = $typeColors[$asset->type->value] ?? ['bg' => 'rgba(100,116,139,0.12)', 'color' => 'var(--text-muted)'];
                                    ?>
                                    <span class="badge-custom badge-status" style="background:<?php echo e($tc['bg']); ?>; color:<?php echo e($tc['color']); ?>; border:1px solid <?php echo e($tc['color']); ?>30">
                                        <i class="<?php echo e($assetType?->icon() ?? 'bi-box'); ?> me-1"></i>
                                        <?php echo e($assetType?->label() ?? $asset->type); ?>

                                    </span>
                                </td>
                                <td>
                                    <span style="font-weight:500"><?php echo e($asset->name); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($asset->description): ?>
                                        <br><small style="color:var(--text-muted)"><?php echo e($asset->description); ?></small>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="text-end"><?php echo e($asset->quantity ? number_format($asset->quantity, 4) : '—'); ?></td>
                                <td class="text-end fw-bold"><?php echo e(number_format($asset->total_value, 2)); ?> <small style="font-weight:400; color:var(--text-muted)"><?php echo e(config('finance.currency_symbol')); ?></small></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($asset->is_liquid): ?>
                                        <span class="badge-custom badge-status" style="background:rgba(34,197,94,0.1); color:var(--success); border:1px solid rgba(34,197,94,0.3)">
                                            <i class="bi bi-check-circle me-1"></i><?php echo e(__('asset.liquid')); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="badge-custom badge-status" style="background:rgba(100,116,139,0.1); color:var(--text-muted); border:1px solid rgba(100,116,139,0.3)">
                                            <i class="bi bi-x-circle me-1"></i><?php echo e(__('asset.non_liquid')); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($asset->is_zakatable): ?>
                                        <span class="badge-custom badge-status" style="background:rgba(21,183,108,0.1); color:var(--accent); border:1px solid rgba(21,183,108,0.3)">
                                            <i class="bi bi-heart me-1"></i><?php echo e(__('asset.zakatable')); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="badge-custom badge-status" style="background:rgba(239,68,68,0.1); color:var(--danger); border:1px solid rgba(239,68,68,0.3)">
                                            <i class="bi bi-heartbreak me-1"></i><?php echo e(__('asset.non_zakatable')); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasActions): ?>
                                <td class="text-center">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'trashed'): ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canRestore): ?>
                                            <form action="<?php echo e(route('asset.restore', $asset)); ?>" method="POST" style="display:inline">
                                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="action-btn" title="<?php echo e(__('general.restore')); ?>" style="color:var(--success)">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canForceDelete): ?>
                                            <button type="button" class="action-btn" title="<?php echo e(__('general.force_delete')); ?>" style="color:var(--danger)" @click="confirmForceDelete('asset', <?php echo e($asset->id); ?>)">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php else: ?>
                                        <div class="action-group justify-content-center">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canUpdate): ?>
                                                <a href="<?php echo e(route('asset.edit', $asset)); ?>" class="action-btn" title="<?php echo e(__('general.edit')); ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDelete): ?>
                                                <button type="button" class="action-btn" title="<?php echo e(__('general.delete')); ?>" @click="confirmDelete('asset', <?php echo e($asset->id); ?>)">
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination-info','data' => ['items' => $assets]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination-info'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($assets)]); ?>
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
                        <?php echo e($assets->appends(request()->except('page'))->links()); ?>

                    </div>
                </div>
            <?php else: ?>
                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'bi-safe','title' => __('general.no_data'),'message' => $tab === 'trashed' ? __('messages.no_trashed') : __('messages.no_results'),'action' => $tab === 'trashed' ? route('asset.index') : ($canCreate ? route('asset.create') : '#'),'actionText' => $tab === 'trashed' ? __('general.back') : __('asset.add')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi-safe','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('general.no_data')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tab === 'trashed' ? __('messages.no_trashed') : __('messages.no_results')),'action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tab === 'trashed' ? route('asset.index') : ($canCreate ? route('asset.create') : '#')),'actionText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tab === 'trashed' ? __('general.back') : __('asset.add'))]); ?>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\asset\index.blade.php ENDPATH**/ ?>