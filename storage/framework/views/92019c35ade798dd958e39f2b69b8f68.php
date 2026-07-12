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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__("super-admin.role_name_{$role->slug}")); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__("super-admin.role_name_{$role->slug}")); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e($role->description); ?> <?php $__env->endSlot(); ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-info-circle"></i><?php echo e(__('workspace.role_details')); ?></h5>
                </div>
                <div class="section-card-body">
                    <div class="mb-3">
                        <span style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:4px"><?php echo e(__('general.name')); ?></span>
                        <span style="font-size:14px;font-weight:500"><?php echo e(__("super-admin.role_name_{$role->slug}")); ?></span>
                    </div>
                    <div class="mb-3">
                        <span style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:4px"><?php echo e(__('super-admin.role_slug')); ?></span>
                        <code style="font-size:13px;background:var(--bg-subtle);padding:4px 10px;border-radius:6px;display:inline-block"><?php echo e($role->slug); ?></code>
                    </div>
                    <div class="mb-3">
                        <span style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:4px"><?php echo e(__('general.description')); ?></span>
                        <span style="font-size:13px;color:var(--text)"><?php echo e($role->description); ?></span>
                    </div>
                    <div class="mb-3">
                        <span style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:4px"><?php echo e(__('general.users')); ?></span>
                        <span style="font-size:14px;font-weight:500"><?php echo e($role->users_count); ?></span>
                    </div>
                    <a href="<?php echo e(route('settings.workspace.roles.index')); ?>" class="btn" style="width:100%;padding:9px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text);font-weight:500;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:6px;cursor:pointer">
                        <i class="bi bi-arrow-left"></i><?php echo e(__('general.back')); ?>

                    </a>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $groupPerms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="mb-4">
                            <h6 style="font-size:13px;font-weight:700;color:var(--accent);text-transform:capitalize;margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px">
                                <i class="bi bi-folder2-open" style="font-size:14px"></i>
                                <?php echo e(__("super-admin.permission_group_{$group}")); ?>

                                <span class="badge" style="font-size:10px;background:var(--bg-subtle);color:var(--text-muted);padding:1px 8px;border-radius:4px;font-weight:500"><?php echo e(count($groupPerms)); ?></span>
                            </h6>
                            <div class="permission-grid">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $groupPerms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="permission-card <?php echo e(in_array($perm->id, $rolePermissions) ? 'has' : ''); ?>" style="cursor:default">
                                        <input type="checkbox" <?php echo e(in_array($perm->id, $rolePermissions) ? 'checked' : ''); ?> disabled class="perm-check">
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\settings\role-show.blade.php ENDPATH**/ ?>