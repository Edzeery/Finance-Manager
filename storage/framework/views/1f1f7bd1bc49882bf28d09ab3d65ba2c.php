<?php if (isset($component)) { $__componentOriginal11b520df80702cb1ab8718e178b6ffa6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal11b520df80702cb1ab8718e178b6ffa6 = $attributes; } ?>
<?php $component = App\View\Components\SuperAdminLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('super-admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SuperAdminLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('super-admin.features')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('super-admin.features')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('super-admin.features_desc')); ?> <?php $__env->endSlot(); ?>

    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <form method="GET" action="<?php echo e(route('super.admin.features.index')); ?>" class="d-flex flex-wrap align-items-center gap-2">
                    <?php if (isset($component)) { $__componentOriginal33e4867731ced0462908f8cc78d5ea1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-filter','data' => ['name' => 'search','placeholder' => ''.e(__('general.search')).'...','value' => ''.e(request('search')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','placeholder' => ''.e(__('general.search')).'...','value' => ''.e(request('search')).'']); ?>
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
                    <?php if (isset($component)) { $__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-filter','data' => ['name' => 'type','options' => [
                        'boolean' => 'Boolean',
                        'value' => 'Value',
                        'text' => 'Text',
                    ],'placeholder' => ''.e(__('super-admin.all_types')).'','minWidth' => '110px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'type','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                        'boolean' => 'Boolean',
                        'value' => 'Value',
                        'text' => 'Text',
                    ]),'placeholder' => ''.e(__('super-admin.all_types')).'','min-width' => '110px']); ?>
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
                    <?php if (isset($component)) { $__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-filter','data' => ['name' => 'is_core','options' => [
                        'true' => __('super-admin.core'),
                        'false' => __('super-admin.addon'),
                    ],'placeholder' => ''.e(__('super-admin.all_features')).'','minWidth' => '90px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'is_core','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                        'true' => __('super-admin.core'),
                        'false' => __('super-admin.addon'),
                    ]),'placeholder' => ''.e(__('super-admin.all_features')).'','min-width' => '90px']); ?>
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
                    <?php if (isset($component)) { $__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.clear-filters','data' => ['filters' => ['search','type','is_core'],'route' => route('super.admin.features.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('clear-filters'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['filters' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['search','type','is_core']),'route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('super.admin.features.index'))]); ?>
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
            </div>
            <div class="data-grid-toolbar-right">
                <div class="d-flex align-items-center gap-2">
                    <?php if (isset($component)) { $__componentOriginal350cc130478c4b4aced77f6fd760100d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal350cc130478c4b4aced77f6fd760100d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.per-page','data' => ['current' => (int) request('per_page', 20),'route' => route('super.admin.features.index'),'preserve' => ['search','type','is_core'],'options' => [10, 20, 30, 50]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('per-page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((int) request('per_page', 20)),'route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('super.admin.features.index')),'preserve' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['search','type','is_core']),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([10, 20, 30, 50])]); ?>
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
                    <a href="<?php echo e(route('super.admin.features.create')); ?>" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                        <i class="bi bi-plus-lg"></i><?php echo e(__('super-admin.create_feature')); ?>

                    </a>
                </div>
            </div>
        </div>

        <div class="data-grid-body">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($features->count()): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th><?php echo e(__('super-admin.feature_name_en')); ?></th>
                            <th><?php echo e(__('super-admin.feature_name_ar')); ?></th>
                            <th><?php echo e(__('super-admin.feature_slug')); ?></th>
                            <th><?php echo e(__('super-admin.feature_type')); ?></th>
                            <th><?php echo e(__('general.status')); ?></th>
                            <th class="col-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td style="color:var(--text-muted);font-size:12px"><?php echo e($feature->sort_order); ?></td>
                                <td>
                                    <strong><?php echo e($feature->name_en); ?></strong>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($feature->is_core): ?>
                                        <span class="badge" style="font-size:9px;background:var(--info-light);color:var(--info);padding:2px 8px;border-radius:4px;font-weight:600;margin-inline-start:6px"><?php echo e(__('super-admin.core')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td style="font-size:13px;color:var(--text-secondary)"><?php echo e($feature->name_ar ?: '—'); ?></td>
                                <td><code style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px"><?php echo e($feature->slug); ?></code></td>
                                <td><span style="font-size:12px;color:var(--text-secondary)"><?php echo e($feature->type); ?></span></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($feature->is_core): ?>
                                        <span class="badge" style="font-size:10px;background:var(--success-light);color:var(--success);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e(__('super-admin.core')); ?></span>
                                    <?php else: ?>
                                        <span class="badge" style="font-size:10px;background:var(--bg-subtle);color:var(--text-muted);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e(__('super-admin.addon')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="col-actions">
                                    <div class="cell-actions">
                                        <a href="<?php echo e(route('super.admin.features.edit', $feature)); ?>" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="<?php echo e(__('general.edit')); ?>">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="<?php echo e(route('super.admin.features.destroy', $feature)); ?>" id="delete-feature-<?php echo e($feature->id); ?>" style="display:none">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        </form>
                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="<?php echo e(__('general.delete')); ?>" @click="confirmDeleteFeature(<?php echo e($feature->id); ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-list-check"></i></div>
                    <h4><?php echo e(__('general.no_data')); ?></h4>
                    <p><?php echo e(__('super-admin.no_features')); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($features->count()): ?>
            <div class="data-grid-footer">
                <?php if (isset($component)) { $__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination-info','data' => ['items' => $features]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination-info'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($features)]); ?>
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
                <div><?php echo e($features->appends(request()->except('page'))->links()); ?></div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
    function confirmDeleteFeature(id) {
        const form = document.getElementById('delete-feature-' + id);
        if (!form) return;
        showConfirmModal(
            '<?php echo e(__('general.confirm')); ?>',
            '<?php echo e(__('super-admin.confirm_delete_feature')); ?>',
            (confirmed) => { if (confirmed) form.submit(); },
            '<?php echo e(__('general.delete')); ?>',
            'btn-danger'
        );
    }
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal11b520df80702cb1ab8718e178b6ffa6)): ?>
<?php $attributes = $__attributesOriginal11b520df80702cb1ab8718e178b6ffa6; ?>
<?php unset($__attributesOriginal11b520df80702cb1ab8718e178b6ffa6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal11b520df80702cb1ab8718e178b6ffa6)): ?>
<?php $component = $__componentOriginal11b520df80702cb1ab8718e178b6ffa6; ?>
<?php unset($__componentOriginal11b520df80702cb1ab8718e178b6ffa6); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\super-admin\features.blade.php ENDPATH**/ ?>