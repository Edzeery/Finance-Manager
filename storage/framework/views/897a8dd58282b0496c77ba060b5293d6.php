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
     <?php $__env->slot('title', null, []); ?> <?php echo e($gateway ? __('super-admin.edit_gateway_structure') : __('super-admin.create_gateway_structure')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e($gateway ? __('super-admin.edit_gateway_structure') : __('super-admin.create_gateway_structure')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e($gateway ? __('super-admin.edit_gateway_structure') : __('super-admin.create_gateway_structure')); ?> <?php $__env->endSlot(); ?>

    <form method="POST" action="<?php echo e($gateway ? route('super.admin.gateways.update', $gateway) : route('super.admin.gateways.store')); ?>" style="max-width:800px">
        <?php echo csrf_field(); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($gateway): ?> <?php echo method_field('PUT'); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label-custom"><?php echo e(__('super-admin.gateway_key')); ?> <span class="text-danger">*</span></label>
                <input type="text" name="key" class="form-custom <?php $__errorArgs = ['key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       value="<?php echo e(old('key', $gateway->key ?? '')); ?>" maxlength="50" required
                       placeholder="e.g. my_gateway">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label-custom"><?php echo e(__('super-admin.gateway_name')); ?> <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-custom <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       value="<?php echo e(old('name', $gateway->name ?? '')); ?>" maxlength="255" required>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label-custom"><?php echo e(__('super-admin.category')); ?> <span class="text-danger">*</span></label>
                <select name="category" class="form-custom <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $registry->categories(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catKey => $catLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($catKey); ?>" <?php echo e(old('category', $gateway->category ?? '') === $catKey ? 'selected' : ''); ?>><?php echo e($catLabel); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="col-md-4">
                <label class="form-label-custom"><?php echo e(__('super-admin.gateway_icon')); ?></label>
                <input type="text" name="icon" class="form-custom <?php $__errorArgs = ['icon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       value="<?php echo e(old('icon', $gateway->icon ?? '')); ?>" maxlength="100"
                       placeholder="bi-credit-card">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['icon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="col-md-4">
                <label class="form-label-custom"><?php echo e(__('general.order')); ?></label>
                <input type="number" name="sort_order" class="form-custom <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       value="<?php echo e(old('sort_order', $gateway->sort_order ?? 0)); ?>" min="0" max="999">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label-custom"><?php echo e(__('super-admin.gateway_description')); ?></label>
            <textarea name="description" class="form-custom <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                      rows="2" maxlength="500"><?php echo e(old('description', $gateway->description ?? '')); ?></textarea>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['name' => 'sandbox','value' => '1','checked' => $gateway->sandbox ?? false,'label' => 'Sandbox']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'sandbox','value' => '1','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($gateway->sandbox ?? false),'label' => 'Sandbox']); ?>
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
            </div>
            <div class="col-md-4">
                <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['name' => 'webhook','value' => '1','checked' => $gateway->webhook ?? false,'label' => 'Webhook']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'webhook','value' => '1','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($gateway->webhook ?? false),'label' => 'Webhook']); ?>
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
            </div>
            <div class="col-md-4">
                <label class="form-label-custom"><?php echo e(__('super-admin.supported_currencies')); ?></label>
                <?php $allCurrencies = \App\Helpers\CurrencyHelper::availableCurrencies(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allCurrencies): ?>
                    <div class="d-flex flex-wrap gap-3 mt-1" style="max-height:140px;overflow-y:auto">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allCurrencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="form-check form-check-inline m-0">
                                <input type="checkbox" name="supported_currencies[]" value="<?php echo e($cur['code']); ?>"
                                       id="cur_<?php echo e($cur['code']); ?>" class="form-check-input"
                                       <?php echo e(in_array($cur['code'], old('supported_currencies', $gateway->supported_currencies ?? ['DZD'])) ? 'checked' : ''); ?>>
                                <label class="form-check-label small" for="cur_<?php echo e($cur['code']); ?>"><?php echo e($cur['code']); ?> — <?php echo e($cur['name']); ?></label>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted small mt-1"><?php echo e(__('general.no_data')); ?></p>
                    <input type="hidden" name="supported_currencies" value="">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['supported_currencies'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['supported_currencies.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="section-card mb-4">
            <div class="section-card-header">
                <h6><i class="bi bi-layout-three-columns"></i> <?php echo e(__('super-admin.fields_builder')); ?></h6>
            </div>
            <div class="section-card-body">
                <div class="settings-section-desc small mb-3"><?php echo e(__('super-admin.gateway_save_hint')); ?></div>

                <div id="fields-builder">
                    <?php $savedFields = old('fields', $gateway->fields ?? []); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $savedFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isSelect = ($f['type'] ?? 'text') === 'select';
                            $defOld = old("fields.{$i}.default");
                            $defVal = $defOld !== null ? $defOld : ($f['default'] ?? '');
                            $optList = old("fields.{$i}.options");
                            if ($optList === null && !empty($f['options'])) {
                                $optList = $f['options'];
                            }
                        ?>
                        <div class="field-row border rounded p-2 mb-2" data-index="<?php echo e($i); ?>">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-2">
                                    <input type="text" name="fields[<?php echo e($i); ?>][key]" value="<?php echo e($f['key'] ?? ''); ?>" class="form-custom" placeholder="<?php echo e(__('super-admin.field_key')); ?>">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="fields[<?php echo e($i); ?>][label]" value="<?php echo e($f['label'] ?? ''); ?>" class="form-custom" placeholder="<?php echo e(__('super-admin.field_label')); ?>">
                                </div>
                                <div class="col-md-2">
                                    <select name="fields[<?php echo e($i); ?>][type]" class="form-custom">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['text','password','textarea','email','url','number','select','boolean']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($t); ?>" <?php echo e(($f['type'] ?? 'text') === $t ? 'selected' : ''); ?>><?php echo e($t); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="fields[<?php echo e($i); ?>][placeholder]" value="<?php echo e($f['placeholder'] ?? ''); ?>" class="form-custom" placeholder="<?php echo e(__('super-admin.field_placeholder')); ?>">
                                </div>
                                <div class="col-md-1 d-flex flex-column align-items-center">
                                    <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['name' => 'fields['.e($i).'][required]','checked' => $f['required'] ?? false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'fields['.e($i).'][required]','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($f['required'] ?? false)]); ?>
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
                                    <span class="small text-muted mt-1"><?php echo e(__('super-admin.field_required')); ?></span>
                                </div>
                                <div class="col-md-1 d-flex flex-column align-items-center">
                                    <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['name' => 'fields['.e($i).'][encrypted]','checked' => $f['encrypted'] ?? false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'fields['.e($i).'][encrypted]','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($f['encrypted'] ?? false)]); ?>
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
                                    <span class="small text-muted mt-1"><?php echo e(__('super-admin.field_encrypted')); ?></span>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-field-row"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                            <div class="row g-2 mt-1">
                                <div class="col-md-11">
                                    <input type="text" name="fields[<?php echo e($i); ?>][help]" value="<?php echo e($f['help'] ?? ''); ?>" class="form-custom" placeholder="<?php echo e(__('super-admin.field_help')); ?>">
                                </div>
                                <div class="col-md-1">
                                    <input type="hidden" name="fields[<?php echo e($i); ?>][maxLength]" value="<?php echo e($f['maxLength'] ?? 255); ?>">
                                </div>
                            </div>
                            <div class="row g-2 mt-1 select-options" style="<?php echo e($isSelect ? '' : 'display:none'); ?>">
                                <div class="col-md-2">
                                    <label class="small text-muted mb-1 d-block"><?php echo e(__('super-admin.field_default')); ?></label>
                                    <input type="text" name="fields[<?php echo e($i); ?>][default]" value="<?php echo e($defVal); ?>" class="form-custom" placeholder="<?php echo e(__('super-admin.field_default')); ?>">
                                </div>
                                <div class="col-md-10">
                                    <label class="small text-muted mb-1 d-block"><?php echo e(__('super-admin.field_options')); ?></label>
                                    <div class="options-list">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($optList): ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $optList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oi => $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php $ov = is_array($opt) ? ($opt['value'] ?? '') : ''; $ol = is_array($opt) ? ($opt['label'] ?? $ov) : ''; ?>
                                                <div class="row g-1 mb-1 option-row">
                                                    <div class="col-md-4">
                                                        <input type="text" name="fields[<?php echo e($i); ?>][options][<?php echo e($oi); ?>][value]" value="<?php echo e($ov); ?>" class="form-custom" placeholder="<?php echo e(__('super-admin.field_option_value')); ?>">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" name="fields[<?php echo e($i); ?>][options][<?php echo e($oi); ?>][label]" value="<?php echo e($ol); ?>" class="form-custom" placeholder="<?php echo e(__('super-admin.field_option_label')); ?>">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-sm btn-outline-danger remove-option"><i class="bi bi-x-lg"></i></button>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary add-option" data-fi="<?php echo e($i); ?>"><i class="bi bi-plus"></i> <?php echo e(__('super-admin.add_option')); ?></button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <button type="button" id="add-field-row" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-plus"></i> <?php echo e(__('super-admin.add_field')); ?>

                </button>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-accent btn-custom">
                <?php echo e($gateway ? __('general.update') : __('general.create')); ?>

            </button>
            <a href="<?php echo e(route('super.admin.payment-methods.index')); ?>" class="btn btn-outline-secondary btn-custom">
                <?php echo e(__('general.cancel')); ?>

            </a>
        </div>
    </form>

    <?php $__env->startPush('scripts'); ?>
    <script>
    function createToggleHTML(name, checked, hiddenId) {
        var isChecked = checked ? true : false;
        var icon = isChecked ? 'bi-toggle2-on' : 'bi-toggle2-off';
        var color = isChecked ? 'var(--success)' : 'var(--text-muted)';
        var val = isChecked ? '1' : '0';
        return '<input type="hidden" name="' + name + '" value="' + val + '" id="' + hiddenId + '">' +
            '<button type="button" class="btn btn-sm p-0 border-0 bg-transparent toggle-switch-btn" ' +
            '@click="toggleSwitch($el, \'' + hiddenId + '\')" aria-label="Toggle" style="transition:all 0.15s;cursor:pointer">' +
            '<i class="bi ' + icon + '" style="font-size:20px;color:' + color + ';pointer-events:none;transition:color 0.15s"></i>' +
            '</button>';
    }

    function createOptionRow(fi, oi, val, lbl) {
        var html = '<div class="row g-1 mb-1 option-row">' +
            '<div class="col-md-4"><input type="text" name="fields[' + fi + '][options][' + oi + '][value]" value="' + (val || '') + '" class="form-custom" placeholder="<?php echo e(__('super-admin.field_option_value')); ?>"></div>' +
            '<div class="col-md-6"><input type="text" name="fields[' + fi + '][options][' + oi + '][label]" value="' + (lbl || '') + '" class="form-custom" placeholder="<?php echo e(__('super-admin.field_option_label')); ?>"></div>' +
            '<div class="col-md-2"><button type="button" class="btn btn-sm btn-outline-danger remove-option"><i class="bi bi-x-lg"></i></button></div>' +
            '</div>';
        var div = document.createElement('div');
        div.innerHTML = html;
        var row = div.firstElementChild;
        row.querySelector('.remove-option').addEventListener('click', function() { row.remove(); });
        return row;
    }

    function initFieldBuilder() {
        var container = document.getElementById('fields-builder');
        var addBtn = document.getElementById('add-field-row');
        if (!container || !addBtn) return;

        function getNextIndex() {
            var rows = container.querySelectorAll('.field-row');
            var max = -1;
            rows.forEach(function(r) {
                var idx = parseInt(r.getAttribute('data-index'));
                if (idx > max) max = idx;
            });
            return max + 1;
        }

        function getNextOptionIndex(optionsList) {
            var rows = optionsList.querySelectorAll('.option-row');
            var max = -1;
            rows.forEach(function(r) {
                var inputs = r.querySelectorAll('input');
                for (var k = 0; k < inputs.length; k++) {
                    var m = inputs[k].name.match(/options\[(\d+)\]/);
                    if (m) { var v = parseInt(m[1]); if (v > max) max = v; }
                }
            });
            return max + 1;
        }

        function bindOptionRow(row) {
            row.querySelector('.remove-option').addEventListener('click', function() { row.remove(); });
        }

        function createFieldRow(index) {
            var row = document.createElement('div');
            row.className = 'field-row border rounded p-2 mb-2';
            row.setAttribute('data-index', index);
            var reqId = 'tgl_req_' + index;
            var encId = 'tgl_enc_' + index;
            var reqHtml = createToggleHTML('fields[' + index + '][required]', true, reqId);
            var encHtml = createToggleHTML('fields[' + index + '][encrypted]', false, encId);
            row.innerHTML =
                '<div class="row g-2 align-items-center">' +
                    '<div class="col-md-2"><input type="text" name="fields[' + index + '][key]" class="form-custom" placeholder="<?php echo e(__('super-admin.field_key')); ?>"></div>' +
                    '<div class="col-md-2"><input type="text" name="fields[' + index + '][label]" class="form-custom" placeholder="<?php echo e(__('super-admin.field_label')); ?>"></div>' +
                    '<div class="col-md-2"><select name="fields[' + index + '][type]" class="form-custom">' +
                        '<?php $__currentLoopData = ['text','password','textarea','email','url','number','select','boolean']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>' +
                        '<option value="<?php echo e($t); ?>"><?php echo e($t); ?></option>' +
                        '<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>' +
                    '</select></div>' +
                    '<div class="col-md-2"><input type="text" name="fields[' + index + '][placeholder]" class="form-custom" placeholder="<?php echo e(__('super-admin.field_placeholder')); ?>"></div>' +
                    '<div class="col-md-1 d-flex flex-column align-items-center">' + reqHtml + '<span class="small text-muted mt-1"><?php echo e(__('super-admin.field_required')); ?></span></div>' +
                    '<div class="col-md-1 d-flex flex-column align-items-center">' + encHtml + '<span class="small text-muted mt-1"><?php echo e(__('super-admin.field_encrypted')); ?></span></div>' +
                    '<div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger remove-field-row"><i class="bi bi-trash"></i></button></div>' +
                '</div>' +
                '<div class="row g-2 mt-1">' +
                    '<div class="col-md-11"><input type="text" name="fields[' + index + '][help]" class="form-custom" placeholder="<?php echo e(__('super-admin.field_help')); ?>"></div>' +
                    '<div class="col-md-1"><input type="hidden" name="fields[' + index + '][maxLength]" value="255"></div>' +
                '</div>' +
                '<div class="row g-2 mt-1 select-options" style="display:none">' +
                    '<div class="col-md-2">' +
                        '<label class="small text-muted mb-1 d-block"><?php echo e(__('super-admin.field_default')); ?></label>' +
                        '<input type="text" name="fields[' + index + '][default]" class="form-custom" placeholder="<?php echo e(__('super-admin.field_default')); ?>">' +
                    '</div>' +
                    '<div class="col-md-10">' +
                        '<label class="small text-muted mb-1 d-block"><?php echo e(__('super-admin.field_options')); ?></label>' +
                        '<div class="options-list"></div>' +
                        '<button type="button" class="btn btn-sm btn-outline-secondary add-option" data-fi="' + index + '"><i class="bi bi-plus"></i> <?php echo e(__('super-admin.add_option')); ?></button>' +
                    '</div>' +
                '</div>';

            row.querySelector('select[name="fields[' + index + '][type]"]').addEventListener('change', function() {
                var opts = row.querySelector('.select-options');
                if (opts) opts.style.display = this.value === 'select' ? '' : 'none';
            });

            row.querySelector('.add-option').addEventListener('click', function() {
                var list = this.parentElement.querySelector('.options-list');
                var oi = getNextOptionIndex(list);
                var optRow = createOptionRow(index, oi, '', '');
                list.appendChild(optRow);
            });

            return row;
        }

        function toggleSelectOptions(typeSelect) {
            var row = typeSelect.closest('.field-row');
            if (!row) return;
            var opts = row.querySelector('.select-options');
            if (opts) opts.style.display = typeSelect.value === 'select' ? '' : 'none';
        }

        addBtn.addEventListener('click', function() {
            var idx = getNextIndex();
            var row = createFieldRow(idx);
            container.appendChild(row);
            row.querySelector('.remove-field-row').addEventListener('click', function() {
                row.remove();
            });
            toggleSelectOptions(row.querySelector('select[name="fields[' + idx + '][type]"]'));
        });

        Array.from(container.querySelectorAll('.remove-field-row')).forEach(function(btn) {
            btn.addEventListener('click', function() {
                btn.closest('.field-row').remove();
            });
        });

        Array.from(container.querySelectorAll('.field-row')).forEach(function(row) {
            var sels = row.querySelectorAll('select');
            for (var s = 0; s < sels.length; s++) {
                if (sels[s].name && sels[s].name.endsWith('[type]')) {
                    toggleSelectOptions(sels[s]);
                    sels[s].addEventListener('change', function() { toggleSelectOptions(this); });
                }
            }
            var addOptBtn = row.querySelector('.add-option');
            if (addOptBtn) {
                addOptBtn.addEventListener('click', function() {
                    var list = this.parentElement.querySelector('.options-list');
                    var fi = this.getAttribute('data-fi');
                    var oi = getNextOptionIndex(list);
                    var optRow = createOptionRow(fi, oi, '', '');
                    list.appendChild(optRow);
                });
            }
            var optRows = row.querySelectorAll('.option-row');
            for (var r = 0; r < optRows.length; r++) {
                bindOptionRow(optRows[r]);
            }
        });
    }

    initFieldBuilder();
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\super-admin\payment-gateway-form.blade.php ENDPATH**/ ?>