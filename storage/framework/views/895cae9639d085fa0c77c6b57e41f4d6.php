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
     <?php $__env->slot('title', null, []); ?> <?php echo e($plan ? __('super-admin.edit_plan') : __('super-admin.create_plan')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e($plan ? __('super-admin.edit_plan') : __('super-admin.create_plan')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e($plan ? __('super-admin.edit_plan_desc') : __('super-admin.create_plan_desc')); ?> <?php $__env->endSlot(); ?>

    <?php
        $currentTab = request('tab', 'details');
    ?>

    <div class="tabs-wrapper" style="margin-bottom:24px">
        <div class="tabs-header" style="display:flex;gap:0;border-bottom:2px solid var(--border);overflow-x:auto">
            <button type="button" class="tab-btn" data-tab="details"
                style="padding:10px 20px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;border-bottom:2px solid <?php echo e($currentTab === 'details' ? 'var(--accent)' : 'transparent'); ?>;margin-bottom:-2px;transition:all 0.15s;color:<?php echo e($currentTab === 'details' ? 'var(--accent)' : 'var(--text-muted)'); ?>">
                <i class="bi bi-info-circle"></i><?php echo e(__('super-admin.plan_details')); ?>

            </button>
            <button type="button" class="tab-btn" data-tab="features"
                style="padding:10px 20px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;border-bottom:2px solid <?php echo e($currentTab === 'features' ? 'var(--accent)' : 'transparent'); ?>;margin-bottom:-2px;transition:all 0.15s;color:<?php echo e($currentTab === 'features' ? 'var(--accent)' : 'var(--text-muted)'); ?>">
                <i class="bi bi-list-check"></i><?php echo e(__('super-admin.features')); ?> <span class="badge" style="font-size:9px;background:var(--bg-subtle);color:var(--text-muted);padding:1px 6px;border-radius:4px"><?php echo e($allFeatures->count()); ?></span>
            </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan): ?>
            <button type="button" class="tab-btn" data-tab="prices"
                style="padding:10px 20px;font-size:13px;font-weight:600;border:none;background:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;border-bottom:2px solid <?php echo e($currentTab === 'prices' ? 'var(--accent)' : 'transparent'); ?>;margin-bottom:-2px;transition:all 0.15s;color:<?php echo e($currentTab === 'prices' ? 'var(--accent)' : 'var(--text-muted)'); ?>">
                <i class="bi bi-currency-dollar"></i><?php echo e(__('super-admin.prices')); ?> <span class="badge" style="font-size:9px;background:var(--bg-subtle);color:var(--text-muted);padding:1px 6px;border-radius:4px"><?php echo e($prices->count()); ?></span>
            </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <div style="max-width:860px">
        <form method="POST" action="<?php echo e($plan ? route('super.admin.plans.update', $plan) : route('super.admin.plans.store')); ?>" id="planForm">
            <?php echo csrf_field(); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan): ?> <?php echo method_field('PUT'); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="tab-panel" id="panel-details" style="display:<?php echo e($currentTab === 'details' ? 'block' : 'none'); ?>">
                <div class="section-card">
                    <div class="section-card-header">
                        <h5><i class="bi bi-info-circle"></i><?php echo e(__('super-admin.plan_information')); ?></h5>
                    </div>
                    <div class="section-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <input type="text" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(__('general.name')); ?>" value="<?php echo e(old('name', $plan->name ?? '')); ?>" maxlength="255" required>
                                    <label><?php echo e(__('general.name')); ?> <span class="text-danger">*</span></label>
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
                            <div class="col-md-6">
                                <div class="form-floating-group">
                                    <input type="text" name="slug" class="form-control <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(__('super-admin.plan_slug')); ?>" value="<?php echo e(old('slug', $plan->slug ?? '')); ?>" maxlength="100" required>
                                    <label><?php echo e(__('super-admin.plan_slug')); ?> <span class="text-danger">*</span></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating-group">
                            <textarea name="description" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(__('general.description')); ?>" rows="3" maxlength="1000" style="min-height:70px;padding-top:20px"><?php echo e(old('description', $plan->description ?? '')); ?></textarea>
                            <label><?php echo e(__('general.description')); ?></label>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="mt-3 mb-3" style="font-size:13px;background:var(--info-light);color:var(--info);border-radius:var(--radius-sm);padding:10px 14px;display:flex;align-items:center;gap:8px">
                            <i class="bi bi-currency-dollar"></i>
                            <?php echo e(__('super-admin.prices_manage_hint')); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan): ?>
                                <a href="<?php echo e(route('super.admin.plans.edit', [$plan, 'tab' => 'prices'])); ?>" style="margin-inline-start:auto;font-weight:600;color:var(--accent)"><?php echo e(__('super-admin.plan_prices')); ?> &rarr;</a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating-group">
                                    <input type="number" name="sort_order" class="form-control <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(__('super-admin.plan_order')); ?>" value="<?php echo e(old('sort_order', $plan->sort_order ?? '')); ?>" min="0">
                                    <label><?php echo e(__('super-admin.plan_order')); ?></label>
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
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-3">
                                <div class="form-floating-group">
                                    <input type="number" name="yearly_discount_percent" class="form-control <?php $__errorArgs = ['yearly_discount_percent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(__('super-admin.yearly_discount')); ?>" value="<?php echo e(old('yearly_discount_percent', $plan->yearly_discount_percent ?? '')); ?>" min="0" max="100" step="0.01">
                                    <label><?php echo e(__('super-admin.yearly_discount')); ?></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['yearly_discount_percent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <div class="form-hint"><?php echo e(__('super-admin.yearly_discount_hint')); ?></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating-group">
                                    <input type="text" name="button_text" class="form-control <?php $__errorArgs = ['button_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(__('super-admin.button_text')); ?>" value="<?php echo e(old('button_text', $plan->button_text ?? '')); ?>" maxlength="100">
                                    <label><?php echo e(__('super-admin.button_text')); ?></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['button_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating-group">
                                    <input type="text" name="button_link" class="form-control <?php $__errorArgs = ['button_link'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(__('super-admin.button_link')); ?>" value="<?php echo e(old('button_link', $plan->button_link ?? '')); ?>" maxlength="500">
                                    <label><?php echo e(__('super-admin.button_link')); ?></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['button_link'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3 mt-3">
                            <label class="d-flex align-items-center gap-2" style="cursor:pointer;padding:8px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)" @click="event.preventDefault();this.querySelector('.toggle-switch-btn').click()">
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
                                <span style="font-size:13px;font-weight:500;color:var(--text)"><?php echo e(__('super-admin.free')); ?></span>
                            </label>
                            <label class="d-flex align-items-center gap-2" style="cursor:pointer;padding:8px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)" @click="event.preventDefault();this.querySelector('.toggle-switch-btn').click()">
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
                                <span style="font-size:13px;font-weight:500;color:var(--text)"><?php echo e(__('general.active')); ?></span>
                            </label>
                            <label class="d-flex align-items-center gap-2" style="cursor:pointer;padding:8px 14px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border)" @click="event.preventDefault();this.querySelector('.toggle-switch-btn').click()">
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
                                <span style="font-size:13px;font-weight:500;color:var(--text)"><?php echo e(__('super-admin.public')); ?></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="tab-panel" id="panel-features" style="display:<?php echo e($currentTab === 'features' ? 'block' : 'none'); ?>">
                <div class="section-card">
                    <div class="section-card-header">
                        <h5><i class="bi bi-puzzle"></i><?php echo e(__('super-admin.feature_assignment')); ?></h5>
                    </div>
                    <div class="section-card-body">
                        <div class="settings-section-desc small mb-3">
                            <?php echo e(__('super-admin.feature_assignment_hint')); ?>

                            <a href="<?php echo e(route('super.admin.features.create')); ?>" style="color:var(--accent);text-decoration:none;font-weight:600" target="_blank">
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
                                            <tr style="<?php echo e($feature->is_core ? 'opacity:0.7' : ''); ?>" class="feature-row">
                                                <td>
                                                    <input type="checkbox" name="plan_features[<?php echo e($feature->id); ?>][feature_id]" value="<?php echo e($feature->id); ?>"
                                                        <?php echo e($checked ? 'checked' : ''); ?>

                                                        <?php echo e($disabled); ?>

                                                        style="width:16px;height:16px;accent-color:var(--accent);cursor:pointer"
                                                        data-feature-id="<?php echo e($feature->id); ?>"
                                                        class="feature-checkbox">
                                                </td>
                                                <td>
                                                    <span style="font-size:13px;font-weight:500"><?php echo e($feature->name_en); ?></span>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($feature->is_core): ?>
                                                        <span class="badge" style="font-size:9px;background:var(--info-light);color:var(--info);padding:1px 6px;border-radius:3px;font-weight:600;margin-inline-start:4px"><?php echo e(__('super-admin.core')); ?></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                                <td><code style="font-size:11px;background:var(--bg-subtle);padding:1px 6px;border-radius:3px"><?php echo e($feature->slug); ?></code></td>
                                                <td>
                                                    <input type="text" name="plan_features[<?php echo e($feature->id); ?>][value]" value="<?php echo e($val); ?>"
                                                        class="form-control" style="padding:4px 8px;font-size:12px;height:auto"
                                                        placeholder="<?php echo e($feature->type === 'boolean' ? 'true/false' : ($feature->type === 'value' ? 'number' : 'text')); ?>"
                                                        data-feature-input="<?php echo e($feature->id); ?>">
                                                </td>
                                                <td>
                                                    <input type="number" name="plan_features[<?php echo e($feature->id); ?>][sort_order]" value="<?php echo e($order); ?>"
                                                        class="form-control" style="padding:4px 8px;font-size:12px;height:auto;width:70px" min="0">
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0 py-2">
                                <?php echo e(__('super-admin.no_features_available')); ?>

                                <a href="<?php echo e(route('super.admin.features.create')); ?>" style="color:var(--accent)"><?php echo e(__('super-admin.create_feature')); ?></a>
                            </p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div id="form-actions" style="display:<?php echo e(in_array($currentTab, ['details', 'features']) ? 'flex' : 'none'); ?>" class="d-flex gap-2 mt-4">
                <button type="submit" class="btn" style="padding:9px 22px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
                    <?php echo e($plan ? __('general.update') : __('general.create')); ?>

                </button>
                <a href="<?php echo e(route('super.admin.plans.index')); ?>" class="btn" style="padding:9px 22px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text);font-weight:500;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
                    <?php echo e(__('general.cancel')); ?>

                </a>
            </div>
        </form>

        
        <div class="tab-panel" id="panel-prices" style="display:<?php echo e($currentTab === 'prices' ? 'block' : 'none'); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan): ?>
                <div class="section-card">
                    <div class="section-card-header">
                        <h5><i class="bi bi-currency-dollar"></i><?php echo e(__('super-admin.plan_prices')); ?></h5>
                    </div>
                    <div class="section-card-body">
                        <div class="settings-section-desc small mb-3">
                            <?php echo e(__('super-admin.prices_manage_hint')); ?>

                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prices->isNotEmpty()): ?>
                            <div class="table-responsive">
                                <table class="data-table" style="width:100%">
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
                                                        <a href="<?php echo e(route('super.admin.plans.prices.edit', [$plan, $price])); ?>" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text-muted);font-size:13px;text-decoration:none;transition:all 0.15s" title="<?php echo e(__('general.edit')); ?>">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form method="POST" action="<?php echo e(route('super.admin.plans.prices.destroy', [$plan, $price]) . '?_tab=prices'); ?>" style="display:inline" id="delete-price-<?php echo e($price->id); ?>">
                                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                        </form>
                                                        <button type="button" class="btn" style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-xs);border:1px solid transparent;background:transparent;color:var(--danger);font-size:13px;transition:all 0.15s" title="<?php echo e(__('general.delete')); ?>" @click="confirmDeletePrice(<?php echo e($price->id); ?>)">
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

                        <hr style="border-color:var(--border);margin:20px 0">

                        <h6 style="font-size:14px;font-weight:600;margin:0 0 12px;display:flex;align-items:center;gap:6px">
                            <i class="bi bi-plus-circle"></i><?php echo e(__('super-admin.create_price')); ?>

                        </h6>
                        <form method="POST" action="<?php echo e(route('super.admin.plans.prices.store', $plan) . '?_tab=prices'); ?>" style="max-width:520px">
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
                                    <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer;width:100%">
                                        <i class="bi bi-plus-lg"></i> <?php echo e(__('general.add')); ?>

                                    </button>
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
                    </div>
                </div>
            <?php else: ?>
                <div class="section-card">
                    <div class="section-card-body">
                        <div class="empty-state py-4">
                            <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted);width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                                <i class="bi bi-currency-dollar" style="font-size:22px"></i>
                            </div>
                            <h4 style="font-size:15px;font-weight:600;margin:0 0 4px"><?php echo e(__('super-admin.prices')); ?></h4>
                            <p style="font-size:13px;color:var(--text-muted);margin:0"><?php echo e(__('super-admin.save_plan_first')); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
    (function() {
        var tabs = document.querySelectorAll('.tab-btn');
        var panels = {
            details: document.getElementById('panel-details'),
            features: document.getElementById('panel-features'),
            prices: document.getElementById('panel-prices'),
        };
        var formActions = document.getElementById('form-actions');

        tabs.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var tab = this.dataset.tab;

                tabs.forEach(function(t) {
                    t.style.color = 'var(--text-muted)';
                    t.style.borderBottomColor = 'transparent';
                });
                this.style.color = 'var(--accent)';
                this.style.borderBottomColor = 'var(--accent)';

                Object.keys(panels).forEach(function(key) {
                    if (panels[key]) {
                        panels[key].style.display = key === tab ? 'block' : 'none';
                    }
                });

                if (formActions) {
                    formActions.style.display = (tab === 'details' || tab === 'features') ? 'flex' : 'none';
                }

                var url = new URL(window.location.href);
                url.searchParams.set('tab', tab);
                window.history.replaceState({}, '', url);
            });
        });

        document.querySelectorAll('.feature-checkbox').forEach(function(cb) {
            cb.addEventListener('change', function() {
                var id = this.dataset.featureId;
                var valueInput = document.querySelector('[data-feature-input="' + id + '"]');
                var row = this.closest('.feature-row');
                if (this.checked) {
                    row.style.opacity = '1';
                    if (valueInput) valueInput.disabled = false;
                } else {
                    row.style.opacity = '0.5';
                    if (valueInput) valueInput.disabled = true;
                }
            });
            cb.dispatchEvent(new Event('change'));
        });
    })();

    function confirmDeletePrice(id) {
        var form = document.getElementById('delete-price-' + id);
        if (!form) return;
        showConfirmModal(
            '<?php echo e(__('general.confirm')); ?>',
            '<?php echo e(__('super-admin.confirm_delete_price')); ?>',
            function(confirmed) { if (confirmed) form.submit(); },
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
<?php endif; ?><?php /**PATH C:\laragon\www\Finance-Manager\resources\views/super-admin/plans-form.blade.php ENDPATH**/ ?>