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
     <?php $__env->slot('title', null, []); ?> <?php echo e($plan ? __('super-admin.edit_plan') : __('super-admin.create_plan')); ?> -
        <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e($plan ? __('super-admin.edit_plan') : __('super-admin.create_plan')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e($plan ? __('super-admin.edit_plan_desc') : __('super-admin.create_plan_desc')); ?> <?php $__env->endSlot(); ?>

    <?php
        $currentTab = request('tab', 'details');
        $tabOrder = ['details', 'features', 'prices'];
        $currentIdx = array_search($currentTab, $tabOrder);
        $currentTabPrev = $currentIdx > 0 ? $tabOrder[$currentIdx - 1] : null;
        $currentTabNext = $currentIdx < count($tabOrder) - 1 ? $tabOrder[$currentIdx + 1] : null;
    ?>

    <?php if (isset($component)) { $__componentOriginalb5964ceaff5596b67291a601bad6f23f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb5964ceaff5596b67291a601bad6f23f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.tabs','data' => ['tabs' => [
        'details' => ['label' => __('super-admin.plan_details'), 'icon' => 'bi bi-info-circle'],
        'features' => [
            'label' => __('super-admin.features'),
            'icon' => 'bi bi-list-check',
            'count' => $allFeatures->count(),
        ],
        'prices' => [
            'label' => __('super-admin.prices'),
            'icon' => 'bi bi-currency-dollar',
            'count' => $plan ? $prices->count() : null,
        ],
    ],'current' => $currentTab,'style' => 'underline','mode' => 'client','onTabClick' => 'switchTab']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('tabs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tabs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        'details' => ['label' => __('super-admin.plan_details'), 'icon' => 'bi bi-info-circle'],
        'features' => [
            'label' => __('super-admin.features'),
            'icon' => 'bi bi-list-check',
            'count' => $allFeatures->count(),
        ],
        'prices' => [
            'label' => __('super-admin.prices'),
            'icon' => 'bi bi-currency-dollar',
            'count' => $plan ? $prices->count() : null,
        ],
    ]),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentTab),'style' => 'underline','mode' => 'client','on-tab-click' => 'switchTab']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb5964ceaff5596b67291a601bad6f23f)): ?>
<?php $attributes = $__attributesOriginalb5964ceaff5596b67291a601bad6f23f; ?>
<?php unset($__attributesOriginalb5964ceaff5596b67291a601bad6f23f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb5964ceaff5596b67291a601bad6f23f)): ?>
<?php $component = $__componentOriginalb5964ceaff5596b67291a601bad6f23f; ?>
<?php unset($__componentOriginalb5964ceaff5596b67291a601bad6f23f); ?>
<?php endif; ?>

    <div style="max-width:860px">
        <form method="POST" class="mb-3"
            action="<?php echo e($plan ? route('super.admin.plans.update', $plan) : route('super.admin.plans.store')); ?>"
            id="planForm">
            <?php echo csrf_field(); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan): ?>
                <?php echo method_field('PUT'); ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="tab-panel" id="panel-details" style="display:<?php echo e($currentTab === 'details' ? 'block' : 'none'); ?>">
                <div class="section-card">
                    <div class="section-card-header">
                        <h5><i class="bi bi-info-circle"></i><?php echo e(__('super-admin.plan_information')); ?></h5>
                    </div>
                    <div class="section-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <input type="text" name="name_en"
                                        class="form-control <?php $__errorArgs = ['name_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="<?php echo e(__('super-admin.name_en')); ?>"
                                        value="<?php echo e(old('name_en', $plan->name_en ?? '')); ?>" maxlength="255" required>
                                    <label><?php echo e(__('super-admin.name_en')); ?> <span class="text-danger">*</span></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <input type="text" name="slug"
                                        class="form-control <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="<?php echo e(__('super-admin.plan_slug')); ?>"
                                        value="<?php echo e(old('slug', $plan->slug ?? '')); ?>" maxlength="100" required>
                                    <label><?php echo e(__('super-admin.plan_slug')); ?> <span class="text-danger">*</span></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <input type="text" name="name_ar"
                                        class="form-control <?php $__errorArgs = ['name_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="<?php echo e(__('super-admin.name_ar')); ?>"
                                        value="<?php echo e(old('name_ar', $plan->name_ar ?? '')); ?>" maxlength="255">
                                    <label><?php echo e(__('super-admin.name_ar')); ?></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <input type="text" name="name_fr"
                                        class="form-control <?php $__errorArgs = ['name_fr'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="<?php echo e(__('super-admin.name_fr')); ?>"
                                        value="<?php echo e(old('name_fr', $plan->name_fr ?? '')); ?>" maxlength="255">
                                    <label><?php echo e(__('super-admin.name_fr')); ?></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name_fr'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating-group">
                            <textarea name="description_en" class="form-control <?php $__errorArgs = ['description_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                placeholder="<?php echo e(__('super-admin.desc_en')); ?>" rows="3" maxlength="1000"
                                style="min-height:70px;padding-top:20px"><?php echo e(old('description_en', $plan->description_en ?? '')); ?></textarea>
                            <label><?php echo e(__('super-admin.desc_en')); ?></label>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['description_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <textarea name="description_ar" class="form-control <?php $__errorArgs = ['description_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="<?php echo e(__('super-admin.desc_ar')); ?>" rows="3" maxlength="1000"
                                        style="min-height:70px;padding-top:20px"><?php echo e(old('description_ar', $plan->description_ar ?? '')); ?></textarea>
                                    <label><?php echo e(__('super-admin.desc_ar')); ?></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['description_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <textarea name="description_fr" class="form-control <?php $__errorArgs = ['description_fr'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="<?php echo e(__('super-admin.desc_fr')); ?>" rows="3" maxlength="1000"
                                        style="min-height:70px;padding-top:20px"><?php echo e(old('description_fr', $plan->description_fr ?? '')); ?></textarea>
                                    <label><?php echo e(__('super-admin.desc_fr')); ?></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['description_fr'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 mb-3"
                            style="font-size:13px;background:var(--info-light);color:var(--info);border-radius:var(--radius-sm);padding:10px 14px;display:flex;align-items:center;gap:8px">
                            <i class="bi bi-currency-dollar"></i>
                            <?php echo e(__('super-admin.prices_manage_hint')); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan): ?>
                                <a href="<?php echo e(route('super.admin.plans.edit', [$plan, 'tab' => 'prices'])); ?>"
                                    style="margin-inline-start:auto;font-weight:600;color:var(--accent)"><?php echo e(__('super-admin.plan_prices')); ?>

                                    &rarr;</a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating-group">
                                    <input type="number" name="sort_order"
                                        class="form-control <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="<?php echo e(__('super-admin.plan_order')); ?>"
                                        value="<?php echo e(old('sort_order', $plan->sort_order ?? '')); ?>" min="0">
                                    <label><?php echo e(__('super-admin.plan_order')); ?></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-3">
                                <div class="form-floating-group">
                                    <input type="number" name="yearly_discount_percent"
                                        class="form-control <?php $__errorArgs = ['yearly_discount_percent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="<?php echo e(__('super-admin.yearly_discount')); ?>"
                                        value="<?php echo e(old('yearly_discount_percent', $plan->yearly_discount_percent ?? '')); ?>"
                                        min="0" max="100" step="0.01">
                                    <label><?php echo e(__('super-admin.yearly_discount')); ?></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['yearly_discount_percent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <div class="form-hint"><?php echo e(__('super-admin.yearly_discount_hint')); ?></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating-group">
                                    <input type="text" name="button_text_en"
                                        class="form-control <?php $__errorArgs = ['button_text_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="<?php echo e(__('super-admin.btn_en')); ?>"
                                        value="<?php echo e(old('button_text_en', $plan->button_text_en ?? '')); ?>"
                                        maxlength="100">
                                    <label><?php echo e(__('super-admin.btn_en')); ?></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['button_text_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating-group">
                                    <input type="text" name="button_link"
                                        class="form-control <?php $__errorArgs = ['button_link'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="<?php echo e(__('super-admin.button_link')); ?>"
                                        value="<?php echo e(old('button_link', $plan->button_link ?? '')); ?>" maxlength="500">
                                    <label><?php echo e(__('super-admin.button_link')); ?></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['button_link'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <input type="text" name="button_text_ar"
                                        class="form-control <?php $__errorArgs = ['button_text_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="<?php echo e(__('super-admin.btn_ar')); ?>"
                                        value="<?php echo e(old('button_text_ar', $plan->button_text_ar ?? '')); ?>"
                                        maxlength="100">
                                    <label><?php echo e(__('super-admin.btn_ar')); ?></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['button_text_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <input type="text" name="button_text_fr"
                                        class="form-control <?php $__errorArgs = ['button_text_fr'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="<?php echo e(__('super-admin.btn_fr')); ?>"
                                        value="<?php echo e(old('button_text_fr', $plan->button_text_fr ?? '')); ?>"
                                        maxlength="100">
                                    <label><?php echo e(__('super-admin.btn_fr')); ?></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['button_text_fr'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3 mt-3">
                            <label class="d-flex align-items-center gap-2"
                                style="cursor:pointer;padding:8px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)"
                                @click="event.preventDefault();this.querySelector('.toggle-switch-btn').click()">
                                <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['name' => 'is_free','checked' => $plan->is_free ?? false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'is_free','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($plan->is_free ?? false)]); ?>
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
                                <span
                                    style="font-size:13px;font-weight:500;color:var(--text)"><?php echo e(__('super-admin.free')); ?></span>
                            </label>
                            <label class="d-flex align-items-center gap-2"
                                style="cursor:pointer;padding:8px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)"
                                @click="event.preventDefault();this.querySelector('.toggle-switch-btn').click()">
                                <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['name' => 'is_active','checked' => $plan->is_active ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'is_active','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($plan->is_active ?? true)]); ?>
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
                                <span
                                    style="font-size:13px;font-weight:500;color:var(--text)"><?php echo e(__('general.active')); ?></span>
                            </label>
                            <label class="d-flex align-items-center gap-2"
                                style="cursor:pointer;padding:8px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)"
                                @click="event.preventDefault();this.querySelector('.toggle-switch-btn').click()">
                                <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['name' => 'is_public','checked' => $plan->is_public ?? true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'is_public','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($plan->is_public ?? true)]); ?>
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
                                <span
                                    style="font-size:13px;font-weight:500;color:var(--text)"><?php echo e(__('super-admin.public')); ?></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="tab-panel" id="panel-features"
                style="display:<?php echo e($currentTab === 'features' ? 'block' : 'none'); ?>">
                <div class="section-card">
                    <div class="section-card-header">
                        <h5><i class="bi bi-puzzle"></i><?php echo e(__('super-admin.feature_assignment')); ?></h5>
                    </div>
                    <div class="section-card-body">
                        <div class="settings-section-desc small mb-3">
                            <?php echo e(__('super-admin.feature_assignment_hint')); ?>

                            <a href="<?php echo e(route('super.admin.features.create')); ?>"
                                style="color:var(--accent);text-decoration:none;font-weight:600" target="_blank">
                                <i class="bi bi-plus-circle"></i> <?php echo e(__('super-admin.create_feature')); ?>

                            </a>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allFeatures->isNotEmpty()): ?>
                            <div class="table-responsive">
                                <table class="data-table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th style="width:30px"></th>
                                            <th><?php echo e(__('super-admin.feature_name_en')); ?></th>
                                            <th><?php echo e(__('super-admin.feature_slug')); ?></th>
                                            <th style="width:140px"><?php echo e(__('super-admin.feature_value')); ?></th>
                                            <th style="width:80px"><?php echo e(__('super-admin.feature_order')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allFeatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $assigned = $assignedFeatures->get($feature->id);
                                                $checked = $assigned || $feature->is_core;
                                                $val = $assigned['value'] ?? '';
                                                $order = $assigned['sort_order'] ?? $feature->sort_order;
                                                $disabled = $feature->is_core ? 'disabled' : '';
                                            ?>
                                            <tr style="<?php echo e($feature->is_core ? 'opacity:0.7' : ''); ?>"
                                                class="feature-row">
                                                <td>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($feature->is_core): ?>
                                                        <input type="hidden"
                                                            name="plan_features[<?php echo e($feature->id); ?>][feature_id]"
                                                            value="<?php echo e($feature->id); ?>">
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <input type="checkbox"
                                                        name="plan_features[<?php echo e($feature->id); ?>][feature_id]"
                                                        value="<?php echo e($feature->id); ?>" <?php echo e($checked ? 'checked' : ''); ?>

                                                        <?php echo e($disabled); ?>

                                                        style="width:16px;height:16px;accent-color:var(--accent);cursor:pointer"
                                                        data-feature-id="<?php echo e($feature->id); ?>"
                                                        class="feature-checkbox">
                                                </td>
                                                <td>
                                                    <span
                                                        style="font-size:13px;font-weight:500"><?php echo e($feature->name_en); ?></span>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($feature->is_core): ?>
                                                        <span class="badge"
                                                            style="font-size:9px;background:var(--info-light);color:var(--info);padding:1px 6px;border-radius:3px;font-weight:600;margin-inline-start:4px"><?php echo e(__('super-admin.core')); ?></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                                <td><code
                                                        style="font-size:11px;background:var(--bg-subtle);padding:1px 6px;border-radius:3px"><?php echo e($feature->slug); ?></code>
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        name="plan_features[<?php echo e($feature->id); ?>][value]"
                                                        value="<?php echo e($val); ?>" class="form-control"
                                                        style="padding:4px 8px;font-size:12px;height:auto"
                                                        placeholder="<?php echo e($feature->type === 'boolean' ? 'true/false' : ($feature->type === 'value' ? 'number' : 'text')); ?>"
                                                        data-feature-input="<?php echo e($feature->id); ?>">
                                                </td>
                                                <td>
                                                    <input type="number"
                                                        name="plan_features[<?php echo e($feature->id); ?>][sort_order]"
                                                        value="<?php echo e($order); ?>" class="form-control"
                                                        style="padding:4px 8px;font-size:12px;height:auto;width:70px"
                                                        min="0" data-feature-input="<?php echo e($feature->id); ?>">
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0 py-2">
                                <?php echo e(__('super-admin.no_features_available')); ?>

                                <a href="<?php echo e(route('super.admin.features.create')); ?>"
                                    style="color:var(--accent)"><?php echo e(__('super-admin.create_feature')); ?></a>
                            </p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div id="wizard-nav" class="d-flex gap-2 mt-4" style="justify-content:space-between;align-items:center">
                <div>
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'outline','href' => route('super.admin.plans.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('super.admin.plans.index'))]); ?><?php echo e(__('general.cancel')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                </div>
                <div class="d-flex gap-2">
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'outline','icon' => 'bi bi-chevron-left','dataPrev' => true,'style' => 'display:'.e($currentTab === 'details' ? 'none' : '').'','onclick' => 'switchTab(\''.e($currentTabPrev ?? 'details').'\')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','icon' => 'bi bi-chevron-left','data-prev' => true,'style' => 'display:'.e($currentTab === 'details' ? 'none' : '').'','onclick' => 'switchTab(\''.e($currentTabPrev ?? 'details').'\')']); ?><?php echo e(__('general.previous')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'accent','icon' => 'bi bi-chevron-right','iconPosition' => 'right','dataNext' => true,'style' => 'display:'.e($currentTab === 'prices' ? 'none' : '').'','onclick' => 'switchTab(\''.e($currentTabNext ?? 'prices').'\')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'accent','icon' => 'bi bi-chevron-right','icon-position' => 'right','data-next' => true,'style' => 'display:'.e($currentTab === 'prices' ? 'none' : '').'','onclick' => 'switchTab(\''.e($currentTabNext ?? 'prices').'\')']); ?><?php echo e(__('general.next')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'accent','dataSubmit' => true,'submit' => true,'style' => 'display:'.e($currentTab === 'prices' ? '' : 'none').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'accent','data-submit' => true,'submit' => true,'style' => 'display:'.e($currentTab === 'prices' ? '' : 'none').'']); ?>
                        <?php echo e($plan ? __('general.update') : __('general.create')); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan): ?>
        </form>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="tab-panel" id="panel-prices" style="display:<?php echo e($currentTab === 'prices' ? 'block' : 'none'); ?>">
            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-currency-dollar"></i><?php echo e(__('super-admin.plan_prices')); ?></h5>
                </div>
                <div class="section-card-body">
                    <div class="settings-section-desc small mb-3"><?php echo e(__('super-admin.prices_manage_hint')); ?></div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prices->isNotEmpty()): ?>
                            <div class="table-responsive">
                                <table class="data-table" style="width:100%" id="edit-prices-table">
                                    <thead>
                                        <tr>
                                            <th><?php echo e(__('super-admin.price_period')); ?></th>
                                            <th><?php echo e(__('super-admin.price_currency')); ?></th>
                                            <th><?php echo e(__('super-admin.price_amount')); ?></th>
                                            <th><?php echo e(__('general.status')); ?></th>
                                            <th class="col-actions"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $prices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $price): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <span class="badge" style="font-size:11px;background:var(--bg-subtle);color:var(--text);padding:3px 10px;border-radius:6px;font-weight:600">
                                                        <?php echo e($price->billing_period === 'monthly' ? __('super-admin.monthly') : __('super-admin.yearly')); ?>

                                                    </span>
                                                </td>
                                                <td><strong><?php echo e($price->currency); ?></strong></td>
                                                <td><strong><?php echo e(number_format($price->price, 2)); ?></strong></td>
                                                <td>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($price->is_active): ?>
                                                        <span class="badge" style="font-size:10px;background:var(--success-light);color:var(--success);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e(__('general.active')); ?></span>
                                                    <?php else: ?>
                                                        <span class="badge" style="font-size:10px;background:var(--border);color:var(--text-muted);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e(__('general.inactive')); ?></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                                <td class="col-actions">
                                                    <div class="cell-actions">
                                                        <a href="<?php echo e(route('super.admin.plans.prices.edit', [$plan, $price])); ?>" class="action-btn" title="<?php echo e(__('general.edit')); ?>">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form method="POST" action="<?php echo e(route('super.admin.plans.prices.destroy', [$plan, $price]) . '?_tab=prices'); ?>" style="display:inline" id="delete-price-<?php echo e($price->id); ?>">
                                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                        </form>
                                                        <button type="button" class="action-btn" style="color:var(--danger);border-color:transparent" title="<?php echo e(__('general.delete')); ?>" onclick="confirmDeletePrice(<?php echo e($price->id); ?>)">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state py-4">
                                <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted);width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                                    <i class="bi bi-currency-dollar" style="font-size:22px"></i>
                                </div>
                                <h4 style="font-size:15px;font-weight:600;margin:0 0 4px"><?php echo e(__('super-admin.no_prices')); ?></h4>
                                <p style="font-size:13px;color:var(--text-muted);margin:0"><?php echo e(__('super-admin.no_prices_for_plan')); ?></p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <div id="create-prices-empty" class="empty-state py-4">
                            <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted);width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                                <i class="bi bi-currency-dollar" style="font-size:22px"></i>
                            </div>
                            <h4 style="font-size:15px;font-weight:600;margin:0 0 4px"><?php echo e(__('super-admin.no_prices')); ?></h4>
                            <p style="font-size:13px;color:var(--text-muted);margin:0"><?php echo e(__('super-admin.no_prices_for_plan')); ?></p>
                        </div>
                        <div class="table-responsive" id="create-prices-table-wrapper" style="display:none">
                            <table class="data-table" style="width:100%" id="create-prices-table">
                                <thead>
                                    <tr>
                                        <th><?php echo e(__('super-admin.price_period')); ?></th>
                                        <th><?php echo e(__('super-admin.price_currency')); ?></th>
                                        <th><?php echo e(__('super-admin.price_amount')); ?></th>
                                        <th><?php echo e(__('general.status')); ?></th>
                                        <th class="col-actions"></th>
                                    </tr>
                                </thead>
                                <tbody id="create-prices-tbody"></tbody>
                            </table>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <hr style="border-color:var(--border);margin:20px 0">

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan): ?>
                        <h6 style="font-size:14px;font-weight:600;margin:0 0 12px;display:flex;align-items:center;gap:6px">
                            <i class="bi bi-plus-circle"></i><?php echo e(__('super-admin.create_price')); ?>

                        </h6>
                        <form method="POST" action="<?php echo e(route('super.admin.plans.prices.store', $plan) . '?_tab=prices'); ?>" style="max-width:540px">
                            <?php echo csrf_field(); ?>
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <div class="form-floating-group">
                                        <select name="billing_period" class="form-control" style="padding:7px 10px;font-size:13px" required>
                                            <option value="monthly"><?php echo e(__('super-admin.monthly')); ?></option>
                                            <option value="yearly"><?php echo e(__('super-admin.yearly')); ?></option>
                                        </select>
                                        <label style="font-size:11px"><?php echo e(__('super-admin.price_period')); ?></label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating-group">
                                        <input type="text" name="currency" class="form-control" style="padding:7px 10px;font-size:13px" value="USD" maxlength="10" required>
                                        <label style="font-size:11px"><?php echo e(__('super-admin.price_currency')); ?></label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating-group">
                                        <input type="number" name="price" class="form-control" style="padding:7px 10px;font-size:13px" step="0.01" min="0" required>
                                        <label style="font-size:11px"><?php echo e(__('super-admin.price_amount')); ?></label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'accent','icon' => 'bi bi-plus-lg','submit' => true,'block' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'accent','icon' => 'bi bi-plus-lg','submit' => true,'block' => true]); ?><?php echo e(__('general.add')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <input type="hidden" name="is_active" value="0">
                                <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['name' => 'is_active','checked' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'is_active','checked' => true]); ?>
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
                                <span style="font-size:12px;color:var(--text-muted)"><?php echo e(__('general.active')); ?></span>
                            </div>
                        </form>
                    <?php else: ?>
                        <h6 style="font-size:14px;font-weight:600;margin:0 0 12px;display:flex;align-items:center;gap:6px">
                            <i class="bi bi-plus-circle"></i><?php echo e(__('super-admin.create_price')); ?>

                        </h6>
                        <p class="small text-muted mb-2"><?php echo e(__('super-admin.prices_manage_create_hint')); ?></p>
                        <div class="row g-2 align-items-end" style="max-width:540px">
                            <div class="col-md-3">
                                <div class="form-floating-group">
                                    <select id="price-period-input" class="form-control" style="padding:7px 10px;font-size:13px">
                                        <option value="monthly"><?php echo e(__('super-admin.monthly')); ?></option>
                                        <option value="yearly"><?php echo e(__('super-admin.yearly')); ?></option>
                                    </select>
                                    <label style="font-size:11px"><?php echo e(__('super-admin.price_period')); ?></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating-group">
                                    <input type="text" id="price-currency-input" class="form-control" style="padding:7px 10px;font-size:13px" value="USD" maxlength="10">
                                    <label style="font-size:11px"><?php echo e(__('super-admin.price_currency')); ?></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating-group">
                                    <input type="number" id="price-amount-input" class="form-control" style="padding:7px 10px;font-size:13px" step="0.01" min="0">
                                    <label style="font-size:11px"><?php echo e(__('super-admin.price_amount')); ?></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'accent','icon' => 'bi bi-plus-lg','block' => true,'onclick' => 'addPriceRow()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'accent','icon' => 'bi bi-plus-lg','block' => true,'onclick' => 'addPriceRow()']); ?><?php echo e(__('general.add')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <input type="hidden" id="price-active-input-hidden" value="1">
                            <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['id' => 'price-active-input','checked' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'price-active-input','checked' => true]); ?>
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
                            <span style="font-size:12px;color:var(--text-muted)"><?php echo e(__('general.active')); ?></span>
                        </div>
                        <input type="hidden" name="prices_count" id="prices_count" value="-1">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$plan): ?>
            </form>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            var tabOrder = ['details', 'features', 'prices'];

            function switchTab(tab) {
                var tabs = document.querySelectorAll('.tabs-tab');
                var panels = {
                    details: document.getElementById('panel-details'),
                    features: document.getElementById('panel-features'),
                    prices: document.getElementById('panel-prices'),
                };

                tabs.forEach(function(t) {
                    t.classList.toggle('active', t.dataset.tab === tab);
                });

                Object.keys(panels).forEach(function(key) {
                    if (panels[key]) {
                        panels[key].style.display = key === tab ? 'block' : 'none';
                    }
                });

                var idx = tabOrder.indexOf(tab);
                var navBtns = document.getElementById('wizard-nav');
                if (navBtns) {
                    navBtns.querySelectorAll('[data-prev]').forEach(function(b) {
                        b.style.display = idx <= 0 ? 'none' : '';
                    });
                    navBtns.querySelectorAll('[data-next]').forEach(function(b) {
                        b.style.display = idx >= tabOrder.length - 1 ? 'none' : '';
                    });
                    navBtns.querySelectorAll('[data-submit]').forEach(function(b) {
                        b.style.display = idx >= tabOrder.length - 1 ? '' : 'none';
                    });
                    if (idx >= 0 && idx < tabOrder.length - 1) {
                        navBtns.querySelector('[data-next]').onclick = function() {
                            switchTab(tabOrder[idx + 1]);
                        };
                    }
                    if (idx > 0) {
                        navBtns.querySelector('[data-prev]').onclick = function() {
                            switchTab(tabOrder[idx - 1]);
                        };
                    }
                }

                var url = new URL(window.location.href);
                url.searchParams.set('tab', tab);
                window.history.replaceState({}, '', url);
            }

            (function() {
                switchTab('<?php echo e($currentTab); ?>');

                document.querySelectorAll('.feature-checkbox').forEach(function(cb) {
                    cb.addEventListener('change', function() {
                        var id = this.dataset.featureId;
                        var featureInputs = document.querySelectorAll('[data-feature-input="' + id + '"]');
                        var row = this.closest('.feature-row');
                        if (this.checked) {
                            row.style.opacity = '1';
                            featureInputs.forEach(function(input) {
                                input.disabled = false;
                            });
                        } else {
                            row.style.opacity = '0.5';
                            featureInputs.forEach(function(input) {
                                input.disabled = true;
                            });
                        }
                    });
                    cb.dispatchEvent(new Event('change'));
                });
            })();

            function addPriceRow() {
                var period = document.getElementById('price-period-input');
                var currency = document.getElementById('price-currency-input');
                var amount = document.getElementById('price-amount-input');
                var activeHidden = document.getElementById('price-active-input-hidden');

                if (!amount.value || parseFloat(amount.value) < 0) {
                    amount.focus();
                    return;
                }

                var countInput = document.getElementById('prices_count');
                var idx = parseInt(countInput.value, 10) + 1;
                countInput.value = idx;

                var isActive = activeHidden ? activeHidden.value === '1' : true;
                var periodLabel = period.value === 'monthly' ? '<?php echo e(__('super-admin.monthly')); ?>' : '<?php echo e(__('super-admin.yearly')); ?>';

                var tbody = document.getElementById('create-prices-tbody');
                var tr = document.createElement('tr');
                tr.innerHTML =
                    '<td><span class="badge" style="font-size:11px;background:var(--bg-subtle);color:var(--text);padding:3px 10px;border-radius:6px;font-weight:600">' + periodLabel + '</span></td>' +
                    '<td><strong>' + currency.value + '</strong></td>' +
                    '<td><strong>' + parseFloat(amount.value).toFixed(2) + '</strong></td>' +
                    '<td>' +
                    '<input type="hidden" name="prices[' + idx + '][billing_period]" value="' + period.value + '">' +
                    '<input type="hidden" name="prices[' + idx + '][currency]" value="' + currency.value + '">' +
                    '<input type="hidden" name="prices[' + idx + '][price]" value="' + amount.value + '">' +
                    '<input type="hidden" name="prices[' + idx + '][is_active]" value="' + (isActive ? '1' : '0') + '">' +
                    (isActive
                        ? '<span class="badge" style="font-size:10px;background:var(--success-light);color:var(--success);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e(__('general.active')); ?></span>'
                        : '<span class="badge" style="font-size:10px;background:var(--border);color:var(--text-muted);padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e(__('general.inactive')); ?></span>'
                    ) + '</td>' +
                    '<td class="col-actions"><div class="cell-actions">' +
                    '<button type="button" class="action-btn" style="color:var(--danger);border-color:transparent" title="<?php echo e(__('general.remove')); ?>" onclick="removePriceRow(this)"><i class="bi bi-x-lg"></i></button>' +
                    '</div></td>';

                tbody.appendChild(tr);

                document.getElementById('create-prices-table-wrapper').style.display = '';
                var emptyEl = document.getElementById('create-prices-empty');
                if (emptyEl) emptyEl.style.display = 'none';

                period.value = 'monthly';
                currency.value = 'USD';
                amount.value = '';
                if (activeHidden) {
                    var toggleBtn = document.getElementById('price-active-input');
                    if (toggleBtn && typeof setToggle === 'function') {
                        setToggle('price-active-input', true);
                    }
                }
            }

            function removePriceRow(btn) {
                var tr = btn.closest('tr');
                tr.remove();
                var tbody = document.getElementById('create-prices-tbody');
                if (!tbody || tbody.children.length === 0) {
                    document.getElementById('create-prices-table-wrapper').style.display = 'none';
                    var emptyEl = document.getElementById('create-prices-empty');
                    if (emptyEl) emptyEl.style.display = '';
                }
            }

            function confirmDeletePrice(id) {
                var form = document.getElementById('delete-price-' + id);
                if (!form) return;
                showConfirmModal(
                    '<?php echo e(__('general.confirm')); ?>',
                    '<?php echo e(__('super-admin.confirm_delete_price')); ?>',
                    function(confirmed) {
                        if (confirmed) form.submit();
                    },
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/super-admin/plans-form.blade.php ENDPATH**/ ?>