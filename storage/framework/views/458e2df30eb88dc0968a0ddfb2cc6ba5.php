
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
     <?php $__env->slot('title', null, []); ?> <?php echo e($paymentMethod ? __('super-admin.edit_payment_method') : __('super-admin.create_payment_method')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e($paymentMethod ? __('super-admin.edit_payment_method') : __('super-admin.create_payment_method')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e($paymentMethod ? __('super-admin.edit_payment_method_desc') : __('super-admin.create_payment_method_desc')); ?> <?php $__env->endSlot(); ?>

    <?php
        $knownGateway = $paymentMethod ? $registry->find($paymentMethod->key) : null;
        $gatewayFieldGroups = [];
        $registryArray = [];
        foreach ($registry->all() as $gKey => $gDef) {
            $nonEnabled = array_values(array_filter($gDef->fields, fn($f) => $f->key !== 'enabled'));
            $gatewayFieldGroups[$gKey] = [
                'name' => $gDef->name,
                'hasFields' => !empty($nonEnabled),
                'fields' => $nonEnabled,
            ];
            $registryArray[$gKey] = $gDef->toArray();
        }
        $selectedKey = old('key', $paymentMethod->key ?? '');
    ?>

    <form method="POST" action="<?php echo e($paymentMethod ? route('super.admin.payment-methods.update', $paymentMethod) : route('super.admin.payment-methods.store')); ?>" style="max-width:700px">
        <?php echo csrf_field(); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paymentMethod): ?> <?php echo method_field('PUT'); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label-custom"><?php echo e(__('super-admin.payment_method_key')); ?> <span class="text-danger">*</span></label>
                <select name="key" id="gateway-key" class="form-custom <?php $__errorArgs = ['key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <option value=""><?php echo e(__('super-admin.select_gateway')); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $registry->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $def): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e($selectedKey === $key ? 'selected' : ''); ?>

                            data-name="<?php echo e($def->name); ?>"
                            data-icon="<?php echo e($def->icon); ?>"
                            data-description="<?php echo e($def->description); ?>"
                            data-category="<?php echo e($def->category); ?>">
                            <?php echo e($def->name); ?> (<?php echo e($def->category); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
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
                <label class="form-label-custom"><?php echo e(__('super-admin.payment_method_name')); ?></label>
                <input type="text" name="name" id="gateway-name" class="form-custom <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       value="<?php echo e(old('name', $paymentMethod->name ?? '')); ?>" maxlength="255">
                <div class="form-hint small mt-1"><i class="bi bi-info-circle"></i> <?php echo e(__('super-admin.name_auto_fill_hint')); ?></div>
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

        <div class="mb-3">
            <label class="form-label-custom"><?php echo e(__('super-admin.payment_method_description')); ?></label>
            <textarea name="description" id="gateway-description" class="form-custom <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                      rows="2" maxlength="500"><?php echo e(old('description', $paymentMethod->description ?? '')); ?></textarea>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label-custom"><?php echo e(__('super-admin.payment_method_icon')); ?></label>
                <input type="text" name="icon" id="gateway-icon" class="form-custom <?php $__errorArgs = ['icon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       value="<?php echo e(old('icon', $paymentMethod->icon ?? '')); ?>" maxlength="100"
                       placeholder="e.g. bi-credit-card">
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
                <label class="form-label-custom"><?php echo e(__('super-admin.payment_method_type')); ?> <span class="text-danger">*</span></label>
                <select name="type" id="method-type" class="form-custom <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <option value="online" <?php echo e(old('type', $paymentMethod->type ?? 'online') === 'online' ? 'selected' : ''); ?>><?php echo e(__('super-admin.online')); ?></option>
                    <option value="manual" <?php echo e(old('type', $paymentMethod->type ?? 'online') === 'manual' ? 'selected' : ''); ?>><?php echo e(__('super-admin.manual')); ?></option>
                    <option value="auto_complete" <?php echo e(old('type', $paymentMethod->type ?? 'online') === 'auto_complete' ? 'selected' : ''); ?>><?php echo e(__('super-admin.auto_complete')); ?></option>
                </select>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['type'];
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
                       value="<?php echo e(old('sort_order', $paymentMethod->sort_order ?? 0)); ?>" min="0" max="999">
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

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                 <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['class' => 'mb-3','name' => 'is_active','value' => '1','checked' => $paymentMethod->is_active ?? false,'label' => ''.e(__('general.active')).'','hint' => ''.e(__('general.active_hint')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-3','name' => 'is_active','value' => '1','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentMethod->is_active ?? false),'label' => ''.e(__('general.active')).'','hint' => ''.e(__('general.active_hint')).'']); ?>
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
            <div class="col-md-6">
                 <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['class' => 'mb-3','name' => 'is_public','value' => '1','checked' => $paymentMethod->is_public ?? false,'label' => ''.e(__('super-admin.show_in_onboarding')).'','hint' => ''.e(__('super-admin.show_in_onboarding_hint')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-3','name' => 'is_public','value' => '1','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentMethod->is_public ?? false),'label' => ''.e(__('super-admin.show_in_onboarding')).'','hint' => ''.e(__('super-admin.show_in_onboarding_hint')).'']); ?>
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
        </div>

        <div class="mb-4">
            <div class="section-card">
                <div class="section-card-header">
                    <h6><i class="bi bi-lock"></i> <?php echo e(__('super-admin.gateway_credentials')); ?> <span id="creds-key-label" class="badge bg-secondary ms-2 d-none"></span></h6>
                </div>
                <div class="section-card-body">
                    <div class="settings-section-desc small mb-3"><?php echo e(__('super-admin.credentials_desc')); ?></div>
                    <p id="creds-no-selection" class="text-muted text-center py-4 mb-0 <?php echo e($selectedKey ? 'd-none' : ''); ?>"><?php echo e(__('super-admin.select_gateway_for_credentials')); ?></p>

                    <?php $creds = old('credentials', $currentCredentials ?? []); ?>

                    <div id="credentials-fields" class="<?php echo e($selectedKey ? '' : 'd-none'); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $gatewayFieldGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gKey => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="gateway-creds-group <?php echo e($selectedKey === $gKey ? '' : 'd-none'); ?>" data-gateway="<?php echo e($gKey); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group['hasFields']): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $group['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $fk = $field->key; ?>
                                        <div class="mb-3">
                                            <label class="form-label-custom">
                                                <?php echo e($field->label); ?>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field->required): ?> <span class="text-danger">*</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </label>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($field->type):
                                                case ('select'): ?>
                                                    <select name="credentials[<?php echo e($fk); ?>]" class="form-custom"  <?php if($field->required): ?> required <?php endif; ?>>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $field->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($opt['value']); ?>" <?php echo e(($creds[$fk] ?? $field->default) == $opt['value'] ? 'selected' : ''); ?>><?php echo e($opt['label']); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </select>
                                                    <?php break; ?>

                                                <?php case ('boolean'): ?>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['name' => 'credentials['.e($fk).']','checked' => filter_var($creds[$fk] ?? $field->default, FILTER_VALIDATE_BOOLEAN),'description' => ''.e($field->help ?? __('general.enabled')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'credentials['.e($fk).']','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(filter_var($creds[$fk] ?? $field->default, FILTER_VALIDATE_BOOLEAN)),'description' => ''.e($field->help ?? __('general.enabled')).'']); ?>
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
                                                    <?php break; ?>

                                                <?php case ('textarea'): ?>
                                                    <textarea  name="credentials[<?php echo e($fk); ?>]" class="form-custom" rows="3"
                                                              placeholder="<?php echo e($field->placeholder); ?>"><?php echo e($creds[$fk] ?? $field->default ?? ''); ?></textarea>
                                                    <?php break; ?>

                                                <?php case ('password'): ?>
                                                    <?php if (isset($component)) { $__componentOriginalb37ff04c7d1d761340845e7d275eabcc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb37ff04c7d1d761340845e7d275eabcc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.password-input','data' => ['name' => 'credentials['.e($fk).']','value' => $creds[$fk] ?? '','maxlength' => $field->maxLength ?? 255,'placeholder' => $field->placeholder ?? '••••••••']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('password-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'credentials['.e($fk).']','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($creds[$fk] ?? ''),'maxlength' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($field->maxLength ?? 255),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($field->placeholder ?? '••••••••')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb37ff04c7d1d761340845e7d275eabcc)): ?>
<?php $attributes = $__attributesOriginalb37ff04c7d1d761340845e7d275eabcc; ?>
<?php unset($__attributesOriginalb37ff04c7d1d761340845e7d275eabcc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb37ff04c7d1d761340845e7d275eabcc)): ?>
<?php $component = $__componentOriginalb37ff04c7d1d761340845e7d275eabcc; ?>
<?php unset($__componentOriginalb37ff04c7d1d761340845e7d275eabcc); ?>
<?php endif; ?>
                                                    <?php break; ?>

                                                <?php default: ?>
                                                    <input type="<?php echo e($field->type === 'url' ? 'url' : ($field->type === 'email' ? 'email' : 'text')); ?>"
                                                           name="credentials[<?php echo e($fk); ?>]" class="form-custom"
                                                           value="<?php echo e($creds[$fk] ?? $field->default ?? ''); ?>" maxlength="<?php echo e($field->maxLength ?? 255); ?>"
                                                           placeholder="<?php echo e($field->placeholder); ?>">
                                            <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field->help && $field->type !== 'boolean'): ?>
                                                <div class="form-hint small mt-1"><?php echo e($field->help); ?></div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <div class="alert alert-info mb-0">
                                        <i class="bi bi-info-circle"></i> <?php echo e(__('super-admin.no_credentials_needed')); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i> <?php echo e(__('super-admin.credentials_save_hint')); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="mb-4">
            <div class="section-card">
                <div class="section-card-header">
                    <h6><i class="bi bi-percent"></i> <?php echo e(__('super-admin.tax_rate_links') ?? 'ربط الضرائب والرسوم'); ?></h6>
                </div>
                <div class="section-card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taxRates->isNotEmpty()): ?>
                        <div class="table-responsive">
                            <table class="table table-custom mb-0" style="font-size:13px">
                                <thead>
                                    <tr>
                                        <th><?php echo e(__('super-admin.tax_rate_name') ?? 'المعدل'); ?></th>
                                        <th><?php echo e(__('general.type')); ?></th>
                                        <th><?php echo e(__('super-admin.tax_rate_value') ?? 'القيمة'); ?></th>
                                        <th><?php echo e(__('general.type')); ?> الربط</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $taxRates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taxRate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $existing = $linkedTaxRates->get($taxRate->id);
                                            $selectedChargeType = old("tax_rate_links.{$taxRate->id}.charge_type", $existing?->pivot?->charge_type ?? '');
                                        ?>
                                        <tr>
                                            <td>
                                                <span style="font-weight:500"><?php echo e($taxRate->name); ?></span>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taxRate->country): ?>
                                                    <div style="font-size:11px;color:var(--text-muted)"><?php echo e(strtoupper($taxRate->country)); ?></div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge" style="font-size:10px;background:var(--info-light);color:var(--info);padding:3px 8px;border-radius:4px">
                                                    <?php echo e($taxRate->type === 'percentage' ? '%' : __('general.fixed')); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taxRate->type === 'percentage'): ?>
                                                    <?php echo e(number_format($taxRate->rate, 2)); ?>%
                                                <?php else: ?>
                                                    <?php echo e(number_format($taxRate->rate, 2)); ?>

                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td>
                                                <select name="tax_rate_links[<?php echo e($taxRate->id); ?>][charge_type]" class="form-custom" style="font-size:12px;padding:4px 6px;min-width:140px">
                                                    <option value=""><?php echo e(__('general.none')); ?></option>
                                                    <option value="gateway_fee" <?php echo e($selectedChargeType === 'gateway_fee' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('super-admin.charge_gateway_fee') ?? 'رسم بوابة'); ?>

                                                    </option>
                                                    <option value="tax_added" <?php echo e($selectedChargeType === 'tax_added' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('super-admin.charge_tax_added') ?? 'ضريبة مضافة'); ?>

                                                    </option>
                                                    <option value="tax_disclosed" <?php echo e($selectedChargeType === 'tax_disclosed' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('super-admin.charge_tax_disclosed') ?? 'ضريبة إفصاح'); ?>

                                                    </option>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-hint small mt-2">
                            <i class="bi bi-info-circle"></i>
                            <strong>رسم بوابة</strong>: يُضاف للمبلغ المدفوع فعلاً.
                            <strong>ضريبة مضافة</strong>: تُضاف للمبلغ المدفوع.
                            <strong>ضريبة إفصاح</strong>: تُعرض فقط في الفاتورة ولا تُضاف للمبلغ.
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0 py-2"><?php echo e(__('super-admin.no_tax_rates') ?? 'لا توجد معدلات ضريبية. أنشئ معدلات ضريبية أولاً.'); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-accent btn-custom">
                <?php echo e($paymentMethod ? __('general.update') : __('general.create')); ?>

            </button>
            <a href="<?php echo e(route('super.admin.payment-methods.index')); ?>" class="btn btn-outline-secondary btn-custom">
                <?php echo e(__('general.cancel')); ?>

            </a>
        </div>
    </form>

    <script>
    (function() {
        var keySelect = document.getElementById('gateway-key');
        var nameInput = document.getElementById('gateway-name');
        var descInput = document.getElementById('gateway-description');
        var iconInput = document.getElementById('gateway-icon');
        var typeSelect = document.getElementById('method-type');
        var credsLabel = document.getElementById('creds-key-label');
        var noSelection = document.getElementById('creds-no-selection');
        var fieldsContainer = document.getElementById('credentials-fields');
        var allGroups = document.querySelectorAll('.gateway-creds-group');

        if (!keySelect || !fieldsContainer) return;

        var categoryMap = { online: 'online', bank_transfer: 'manual', wallet: 'auto_complete', cash: 'manual', delivery: 'manual', crypto: 'auto_complete', internal: 'online', custom: 'manual' };

        function updateFields(key) {
            var selected = document.querySelector('.gateway-creds-group[data-gateway="' + key + '"]');

            if (selected) {
                credsLabel.textContent = selected.querySelector('h6') ? '' : key;
                credsLabel.classList.remove('d-none');
                noSelection.classList.add('d-none');
                fieldsContainer.classList.remove('d-none');

                if (nameInput && (!nameInput.value.trim() || nameInput.dataset.autofilled === 'true')) {
                    nameInput.value = keySelect.options[keySelect.selectedIndex].getAttribute('data-name') || '';
                    nameInput.dataset.autofilled = 'true';
                }
                if (descInput && (!descInput.value.trim() || descInput.dataset.autofilled === 'true')) {
                    descInput.value = keySelect.options[keySelect.selectedIndex].getAttribute('data-description') || '';
                    descInput.dataset.autofilled = 'true';
                }
                if (iconInput && (!iconInput.value.trim() || iconInput.dataset.autofilled === 'true')) {
                    iconInput.value = keySelect.options[keySelect.selectedIndex].getAttribute('data-icon') || '';
                    iconInput.dataset.autofilled = 'true';
                }

                var cat = keySelect.options[keySelect.selectedIndex].getAttribute('data-category');
                if (typeSelect && categoryMap[cat]) {
                    typeSelect.value = categoryMap[cat];
                }

                for (var i = 0; i < allGroups.length; i++) {
                    var isTarget = allGroups[i] === selected;
                    allGroups[i].classList.toggle('d-none', !isTarget);
                    var inputs = allGroups[i].querySelectorAll('input, select, textarea');
                    for (var j = 0; j < inputs.length; j++) {
                        inputs[j].disabled = !isTarget;
                    }
                }
            } else {
                credsLabel.classList.add('d-none');
                noSelection.classList.remove('d-none');
                fieldsContainer.classList.add('d-none');
            }
        }

        keySelect.addEventListener('change', function() {
            updateFields(this.value);
        });

        if (keySelect.value) {
            updateFields(keySelect.value);
        }
    })();
    </script>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/super-admin/payment-methods-form.blade.php ENDPATH**/ ?>