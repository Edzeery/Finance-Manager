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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('goal.edit')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('goal.edit')); ?> <?php $__env->endSlot(); ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-body p-4">
                    <form action="<?php echo e(route('goal.update', $goal)); ?>" method="POST">
                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label-custom"><?php echo e(__('goal.single')); ?> (<?php echo e(__('general.ar')); ?>) <span class="text-danger">*</span></label>
                                <input type="text" name="name_ar" value="<?php echo e(old('name_ar', $goal->name_ar)); ?>" class="form-custom <?php $__errorArgs = ['name_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger mt-1" style="font-size:13px"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom"><?php echo e(__('goal.single')); ?> (<?php echo e(__('general.fr')); ?>)</label>
                                <input type="text" name="name_fr" value="<?php echo e(old('name_fr', $goal->name_fr)); ?>" class="form-custom">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom"><?php echo e(__('goal.single')); ?> (<?php echo e(__('general.en')); ?>)</label>
                                <input type="text" name="name_en" value="<?php echo e(old('name_en', $goal->name_en)); ?>" class="form-custom">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom"><?php echo e(__('goal.target_amount')); ?> <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="target_amount" value="<?php echo e(old('target_amount', $goal->target_amount)); ?>" class="form-custom" required>
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px"><?php echo e(config('finance.currency_symbol')); ?></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom"><?php echo e(__('goal.current_amount')); ?></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="current_amount" value="<?php echo e(old('current_amount', $goal->current_amount)); ?>" class="form-custom">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px"><?php echo e(config('finance.currency_symbol')); ?></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom"><?php echo e(__('goal.target_date')); ?></label>
                                <input type="date" name="target_date" value="<?php echo e(old('target_date', $goal->target_date?->format('Y-m-d'))); ?>" class="form-custom">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom"><?php echo e(__('goal.status')); ?> <span class="text-danger">*</span></label>
                                <select name="status" class="form-custom <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="<?php echo e(\App\Enums\GoalStatus::InProgress->value); ?>" <?php echo e((old('status') ?? $goal->status->value) === \App\Enums\GoalStatus::InProgress->value ? 'selected' : ''); ?>><?php echo e(__('goal.in_progress')); ?></option>
                            <option value="<?php echo e(\App\Enums\GoalStatus::Completed->value); ?>" <?php echo e((old('status') ?? $goal->status->value) === \App\Enums\GoalStatus::Completed->value ? 'selected' : ''); ?>><?php echo e(__('goal.completed')); ?></option>
                            <option value="<?php echo e(\App\Enums\GoalStatus::Cancelled->value); ?>" <?php echo e((old('status') ?? $goal->status->value) === \App\Enums\GoalStatus::Cancelled->value ? 'selected' : ''); ?>><?php echo e(__('goal.cancelled')); ?></option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom"><?php echo e(__('goal.icon')); ?></label>
                                <select name="icon" class="form-custom">
                                    <option value="bi-flag" <?php echo e(old('icon', $goal->icon) === 'bi-flag' ? 'selected' : ''); ?>><i class="bi bi-flag"></i> <?php echo e(__('goal.icon_flag')); ?></option>
                                    <option value="bi-house" <?php echo e(old('icon', $goal->icon) === 'bi-house' ? 'selected' : ''); ?>><i class="bi bi-house"></i> <?php echo e(__('goal.icon_house')); ?></option>
                                    <option value="bi-car" <?php echo e(old('icon', $goal->icon) === 'bi-car' ? 'selected' : ''); ?>><i class="bi bi-car"></i> <?php echo e(__('goal.icon_car')); ?></option>
                                    <option value="bi-book" <?php echo e(old('icon', $goal->icon) === 'bi-book' ? 'selected' : ''); ?>><i class="bi bi-book"></i> <?php echo e(__('goal.icon_book')); ?></option>
                                    <option value="bi-heart" <?php echo e(old('icon', $goal->icon) === 'bi-heart' ? 'selected' : ''); ?>><i class="bi bi-heart"></i> <?php echo e(__('goal.icon_heart')); ?></option>
                                    <option value="bi-gem" <?php echo e(old('icon', $goal->icon) === 'bi-gem' ? 'selected' : ''); ?>><i class="bi bi-gem"></i> <?php echo e(__('goal.icon_gem')); ?></option>
                                    <option value="bi-globe" <?php echo e(old('icon', $goal->icon) === 'bi-globe' ? 'selected' : ''); ?>><i class="bi bi-globe"></i> <?php echo e(__('goal.icon_globe')); ?></option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom"><?php echo e(__('goal.color')); ?></label>
                                <div class="d-flex gap-2 flex-wrap">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['#3B82F6', '#22C55E', '#EF4444', '#F59E0B', '#8B5CF6', '#EC4899', '#06B6D4', '#FFC107', '#64748B']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label style="width:32px; height:32px; border-radius:50%; background:<?php echo e($c); ?>; cursor:pointer; border:2px solid <?php echo e(old('color', $goal->color) === $c ? 'var(--text)' : 'transparent'); ?>">
                                            <input type="radio" name="color" value="<?php echo e($c); ?>" <?php echo e(old('color', $goal->color) === $c ? 'checked' : ''); ?> style="display:none" @change="document.querySelectorAll('[name=color]').forEach(r=>r.closest('label').style.borderColor='transparent'); $el.closest('label').style.borderColor='var(--text)'">
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom"><?php echo e(__('goal.notes')); ?></label>
                                <textarea name="notes" class="form-custom" rows="3" maxlength="1000"><?php echo e(old('notes', $goal->notes)); ?></textarea>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4 pt-3" style="border-top:1px solid var(--border)">
                            <button type="submit" class="btn btn-accent btn-custom">
                                <i class="bi bi-check-lg me-1"></i><?php echo e(__('general.save')); ?>

                            </button>
                            <a href="<?php echo e(route('goal.index')); ?>" class="btn btn-outline-secondary btn-custom">
                                <i class="bi bi-x-lg me-1"></i><?php echo e(__('general.cancel')); ?>

                            </a>
                        </div>
                    </form>
                </div>
            </div>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/goal/edit.blade.php ENDPATH**/ ?>