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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__("super-admin.role_name_{$role->slug}")); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__("super-admin.role_name_{$role->slug}")); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e($role->description); ?> <?php $__env->endSlot(); ?>

    <form action="<?php echo e(route('super.admin.roles.update', $role)); ?>" method="POST">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="section-card">
                    <div class="section-card-header">
                        <h5><i class="bi bi-info-circle"></i><?php echo e(__('super-admin.role_details')); ?></h5>
                    </div>
                    <div class="section-card-body">
                        <div class="form-floating-group">
                            <input type="text" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(__('general.name')); ?>" value="<?php echo e(old('name', $role->name)); ?>" required maxlength="255">
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
                        <div class="form-floating-group">
                            <textarea name="description" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(__('general.description')); ?>" rows="3" maxlength="500" style="height:auto;min-height:80px;padding-top:20px"><?php echo e(old('description', $role->description)); ?></textarea>
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
                        <div class="mb-3">
                            <span style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:4px"><?php echo e(__('super-admin.role_slug')); ?></span>
                            <code style="font-size:13px;background:var(--bg-subtle);padding:4px 10px;border-radius:6px;display:inline-block"><?php echo e($role->slug); ?></code>
                        </div>
                        <button type="submit" class="btn" style="width:100%;padding:9px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:6px">
                            <i class="bi bi-check-lg"></i><?php echo e(__('general.save')); ?>

                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="section-card">
                    <div class="section-card-header">
                        <h5><i class="bi bi-shield-check"></i><?php echo e(__('super-admin.permissions')); ?>

                            <span class="badge" style="font-size:10px;background:var(--bg-subtle);color:var(--text-muted);padding:2px 10px;border-radius:6px;font-weight:500"><?php echo e($permissions->flatten()->count()); ?> <?php echo e(__('super-admin.total')); ?></span>
                        </h5>
                    </div>
                    <div class="section-card-body">
                        <div class="mb-3 d-flex flex-wrap gap-2">
                            <button type="button" class="btn" style="padding:5px 12px;font-size:11px;border-radius:var(--radius-xs);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer" @click="toggleAllPermissions(true)"><?php echo e(__('general.select_all')); ?></button>
                            <button type="button" class="btn" style="padding:5px 12px;font-size:11px;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--text);cursor:pointer" @click="toggleAllPermissions(false)"><?php echo e(__('general.deselect_all')); ?></button>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $groupPerms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mb-4">
                                <h6 style="font-size:13px;font-weight:700;color:var(--accent);text-transform:capitalize;margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px">
                                    <i class="bi bi-folder2-open" style="font-size:14px"></i>
                                    <?php echo e(__("super-admin.permission_group_{$group}")); ?>

                                    <span class="badge" style="font-size:10px;background:var(--bg-subtle);color:var(--text-muted);padding:1px 8px;border-radius:4px;font-weight:500"><?php echo e(count($groupPerms)); ?></span>
                                </h6>
                                <div class="permission-grid">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $groupPerms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="permission-card <?php echo e(in_array($perm->id, $rolePermissions) ? 'has' : ''); ?> <?php echo e($role->slug === 'super_admin' ? 'disabled' : ''); ?>">
                                            <input type="checkbox" name="permissions[]" value="<?php echo e($perm->id); ?>"
                                                <?php echo e(in_array($perm->id, $rolePermissions) ? 'checked' : ''); ?>

                                                <?php echo e($role->slug === 'super_admin' ? 'disabled' : ''); ?>

                                                class="perm-check"
                                                @change="$el.closest('.permission-card').classList.toggle('has', $el.checked)">
                                            <div>
                                                <span class="perm-label"><?php echo e(__("super-admin.perm_" . str_replace('.', '_', $perm->slug))); ?></span>
                                                <span class="perm-desc"><?php echo e($perm->description); ?></span>
                                            </div>
                                            <span class="perm-indicator <?php echo e(in_array($perm->id, $rolePermissions) ? 'granted' : 'denied'); ?>"></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($role->slug === 'super_admin'): ?>
                            <div class="d-flex align-items-center gap-2" style="font-size:13px;background:var(--info-light);color:var(--info);border-radius:var(--radius-sm);padding:10px 14px">
                                <i class="bi bi-info-circle"></i>
                                <?php echo e(__('super-admin.super_admin_permissions_locked')); ?>

                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php $__env->startPush('scripts'); ?>
    <script>
    function toggleAllPermissions(checked) {
        document.querySelectorAll('.perm-check:not(:disabled)').forEach(cb => {
            cb.checked = checked;
            cb.closest('.permission-card')?.classList.toggle('has', checked);
        });
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\super-admin\role-edit.blade.php ENDPATH**/ ?>