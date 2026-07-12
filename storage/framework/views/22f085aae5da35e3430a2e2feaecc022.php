
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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('super-admin.payment_methods')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('super-admin.payment_methods')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('super-admin.payment_methods_desc')); ?> <?php $__env->endSlot(); ?>

    <div x-data="{ tab: 'methods' }">
        <div class="d-flex gap-2 mb-4 border-bottom pb-2">
            <button @click="tab = 'methods'" :class="{ 'active-tab': tab === 'methods' }" class="btn btn-sm px-3 d-flex gap-2" style="border:none;background:none;font-weight:500;color:var(--text-muted);transition:all 0.15s">
                <i class="bi bi-credit-card-2-front me-1"></i><?php echo e(__('super-admin.payment_methods')); ?>

            </button>
            <button @click="tab = 'gateways'" :class="{ 'active-tab': tab === 'gateways' }" class="btn btn-sm px-3 d-flex gap-2" style="border:none;background:none;font-weight:500;color:var(--text-muted);transition:all 0.15s">
                <i class="bi bi-diagram-3 me-1"></i><?php echo e(__('super-admin.gateway_structures')); ?>

            </button>
        </div>

        <div x-show="tab === 'methods'" x-transition:enter.duration.200ms>
            <div class="data-grid">
                <div class="data-grid-toolbar">
                    <div class="data-grid-toolbar-left">
                        <form method="GET" action="<?php echo e(route('super.admin.payment-methods.index')); ?>" class="d-flex flex-wrap align-items-center gap-2">
                            <?php if (isset($component)) { $__componentOriginal33e4867731ced0462908f8cc78d5ea1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-filter','data' => ['name' => 'search','placeholder' => ''.e(__('super-admin.search_payment_method')).'...','value' => ''.e(request('search')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','placeholder' => ''.e(__('super-admin.search_payment_method')).'...','value' => ''.e(request('search')).'']); ?>
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
                                'online' => __('super-admin.online'),
                                'manual' => __('super-admin.manual'),
                                'auto_complete' => __('super-admin.auto_complete'),
                            ],'placeholder' => ''.e(__('general.all_types')).'','minWidth' => '130px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'type','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                                'online' => __('super-admin.online'),
                                'manual' => __('super-admin.manual'),
                                'auto_complete' => __('super-admin.auto_complete'),
                            ]),'placeholder' => ''.e(__('general.all_types')).'','min-width' => '130px']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-filter','data' => ['name' => 'status','options' => [
                                'active' => __('general.active'),
                                'inactive' => __('general.inactive'),
                            ],'placeholder' => ''.e(__('general.all_status')).'','minWidth' => '110px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'status','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                                'active' => __('general.active'),
                                'inactive' => __('general.inactive'),
                            ]),'placeholder' => ''.e(__('general.all_status')).'','min-width' => '110px']); ?>
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
                            <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer"><?php echo e(__('general.filter')); ?></button>
                            <?php if (isset($component)) { $__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.clear-filters','data' => ['filters' => ['search','type','status'],'route' => route('super.admin.payment-methods.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('clear-filters'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['filters' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['search','type','status']),'route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('super.admin.payment-methods.index'))]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.per-page','data' => ['current' => (int) request('per_page', 15),'route' => route('super.admin.payment-methods.index'),'preserve' => ['search','type','status'],'options' => [10, 15, 25, 50]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('per-page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((int) request('per_page', 15)),'route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('super.admin.payment-methods.index')),'preserve' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['search','type','status']),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([10, 15, 25, 50])]); ?>
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
                            <a href="<?php echo e(route('super.admin.payment-methods.create')); ?>" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                                <i class="bi bi-plus-lg"></i><?php echo e(__('super-admin.create_payment_method')); ?>

                            </a>
                        </div>
                    </div>
                </div>

                <div class="data-grid-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paymentMethods->count()): ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('super-admin.payment_method_icon')); ?></th>
                                    <th><?php echo e(__('super-admin.payment_method_key')); ?></th>
                                    <th><?php echo e(__('super-admin.payment_method_name')); ?></th>
                                    <th><?php echo e(__('super-admin.payment_method_type')); ?></th>
                                    <th><?php echo e(__('general.order')); ?></th>
                                    <th><?php echo e(__('super-admin.public')); ?></th>
                                    <th><?php echo e(__('general.status')); ?></th>
                                    <th class="col-actions"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <span style="width:34px;height:34px;border-radius:8px;background:var(--bg-subtle);display:flex;align-items:center;justify-content:center;font-size:16px">
                                                <i class="bi <?php echo e($method->icon ?: 'bi-credit-card'); ?>"></i>
                                            </span>
                                        </td>
                                        <td><code style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px"><?php echo e($method->key); ?></code></td>
                                        <td>
                                            <span style="font-weight:500"><?php echo e($method->name); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($method->description): ?>
                                                <div style="font-size:12px;color:var(--text-muted)"><?php echo e($method->description); ?></div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($method->isOnline()): ?>
                                                <span class="badge" style="font-size:10px;background:var(--info-light);color:var(--info);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e(__('super-admin.online')); ?></span>
                                            <?php elseif($method->isManual()): ?>
                                                <span class="badge" style="font-size:10px;background:var(--warning-light);color:var(--warning);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e(__('super-admin.manual')); ?></span>
                                            <?php else: ?>
                                                <span class="badge" style="font-size:10px;background:var(--accent-light);color:var(--accent);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e(__('super-admin.auto_complete')); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td><?php echo e($method->sort_order); ?></td>
                                        <td>
                                            <form method="POST" action="<?php echo e(route('super.admin.payment-methods.toggle-public', $method)); ?>" style="display:inline">
                                                <?php echo csrf_field(); ?>
                                                <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['name' => 'is_public','checked' => $method->is_public,'standalone' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'is_public','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($method->is_public),'standalone' => 'true']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal319c173192d983146c5bd67854bb9452)): ?>
<?php $attributes = $__attributesOriginal319c173192d983146c5bd67854bb9452; ?>
<?php unset($__attributesOriginal319c173192d983146c5bd67854bb9452); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal319c173192d983146c5bd67854bb9452)): ?>
<?php $component = $__componentOriginal319c173192d983146c5bd67854bb9452; ?>
<?php unset($__componentOriginal319c173192d983146c5bd67854bb9452); ?>
<?php endif; ?>
                                            </form>
                                        </td>
                                        <td>
                                            <form method="POST" action="<?php echo e(route('super.admin.payment-methods.toggle-status', $method)); ?>" style="display:inline">
                                                <?php echo csrf_field(); ?>
                                                <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['name' => 'is_active','checked' => $method->is_active,'standalone' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'is_active','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($method->is_active),'standalone' => 'true']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal319c173192d983146c5bd67854bb9452)): ?>
<?php $attributes = $__attributesOriginal319c173192d983146c5bd67854bb9452; ?>
<?php unset($__attributesOriginal319c173192d983146c5bd67854bb9452); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal319c173192d983146c5bd67854bb9452)): ?>
<?php $component = $__componentOriginal319c173192d983146c5bd67854bb9452; ?>
<?php unset($__componentOriginal319c173192d983146c5bd67854bb9452); ?>
<?php endif; ?>
                                            </form>
                                        </td>
                                        <td class="col-actions">
                                            <div class="cell-actions">
                                                <a href="<?php echo e(route('super.admin.payment-methods.edit', $method)); ?>" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="<?php echo e(__('general.edit')); ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" action="<?php echo e(route('super.admin.payment-methods.destroy', $method)); ?>" id="delete-payment-method-<?php echo e($method->id); ?>" style="display:none">
                                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                </form>
                                                <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="<?php echo e(__('general.delete')); ?>" @click="confirmDeletePaymentMethod(<?php echo e($method->id); ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                        <div class="data-grid-footer">
                            <span><?php echo e(__('general.showing')); ?> <?php echo e($paymentMethods->firstItem()); ?>–<?php echo e($paymentMethods->lastItem()); ?> <?php echo e(__('general.of')); ?> <?php echo e($paymentMethods->total()); ?></span>
                            <div><?php echo e($paymentMethods->appends(request()->except('page'))->links()); ?></div>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-credit-card-2-front"></i></div>
                            <h4><?php echo e(__('general.no_data')); ?></h4>
                            <p><?php echo e(__('super-admin.no_payment_methods')); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <div x-show="tab === 'gateways'" x-cloak x-transition:enter.duration.200ms>
            <div class="data-grid">
                <div class="data-grid-toolbar">
                    <div class="data-grid-toolbar-left"></div>
                    <div class="data-grid-toolbar-right">
                        <a href="<?php echo e(route('super.admin.gateways.create')); ?>" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                            <i class="bi bi-plus-lg"></i><?php echo e(__('super-admin.create_gateway_structure')); ?>

                        </a>
                    </div>
                </div>

                <div class="data-grid-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($gateways->count()): ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('super-admin.payment_method_icon')); ?></th>
                                    <th><?php echo e(__('super-admin.payment_method_key')); ?></th>
                                    <th><?php echo e(__('super-admin.payment_method_name')); ?></th>
                                    <th><?php echo e(__('super-admin.category')); ?></th>
                                    <th><?php echo e(__('super-admin.field_count')); ?></th>
                                    <th><?php echo e(__('general.order')); ?></th>
                                    <th class="col-actions"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <span style="width:34px;height:34px;border-radius:8px;background:var(--bg-subtle);display:flex;align-items:center;justify-content:center;font-size:16px">
                                                <i class="bi <?php echo e($gateway->icon ?: 'bi-diagram-3'); ?>"></i>
                                            </span>
                                        </td>
                                        <td><code style="font-size:12px;background:var(--bg-subtle);padding:2px 8px;border-radius:4px"><?php echo e($gateway->key); ?></code></td>
                                        <td>
                                            <span style="font-weight:500"><?php echo e($gateway->name); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($gateway->description): ?>
                                                <div style="font-size:12px;color:var(--text-muted)"><?php echo e($gateway->description); ?></div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td><span class="badge" style="font-size:10px;background:var(--info-light);color:var(--info);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e($gateway->category); ?></span></td>
                                        <td><?php echo e(count($gateway->fields ?? [])); ?></td>
                                        <td><?php echo e($gateway->sort_order); ?></td>
                                        <td class="col-actions">
                                            <div class="cell-actions">
                                                <a href="<?php echo e(route('super.admin.gateways.edit', $gateway)); ?>" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="<?php echo e(__('general.edit')); ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" action="<?php echo e(route('super.admin.gateways.destroy', $gateway)); ?>" id="delete-gateway-<?php echo e($gateway->id); ?>" style="display:none">
                                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                </form>
                                                <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="<?php echo e(__('general.delete')); ?>" @click="confirmDeleteGateway(<?php echo e($gateway->id); ?>, '<?php echo e($gateway->key); ?>')">
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
                            <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-diagram-3"></i></div>
                            <h4><?php echo e(__('general.no_data')); ?></h4>
                            <p><?php echo e(__('super-admin.no_gateway_structures')); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <style>
    .active-tab {
        color: var(--accent) !important;
        border-bottom: 2px solid var(--accent) !important;
        border-radius: 0 !important;
    }
    </style>

    <?php $__env->startPush('scripts'); ?>
    <script>
    function confirmDeletePaymentMethod(id) {
        const form = document.getElementById('delete-payment-method-' + id);
        if (!form) return;
        showConfirmModal(
            '<?php echo e(__('general.confirm')); ?>',
            '<?php echo e(__('super-admin.confirm_delete_payment_method')); ?>',
            (confirmed) => { if (confirmed) form.submit(); },
            '<?php echo e(__('general.delete')); ?>',
            'btn-danger'
        );
    }

    function confirmDeleteGateway(id, key) {
        const form = document.getElementById('delete-gateway-' + id);
        if (!form) return;
        showConfirmModal(
            '<?php echo e(__('general.confirm')); ?>',
            '<?php echo e(__('super-admin.confirm_delete_gateway')); ?>' + ' (' + key + ')',
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
<?php endif; ?><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\super-admin\payment-methods.blade.php ENDPATH**/ ?>