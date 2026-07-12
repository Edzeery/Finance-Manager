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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('workspace.roles')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('workspace.roles')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('workspace.roles_desc')); ?> <?php $__env->endSlot(); ?>

    <div class="row g-3 stagger-fade-in">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-6 col-lg-4">
                <div class="role-card">
                    <div class="role-card-header">
                        <div>
                            <h3 class="role-card-name"><?php echo e(__("super-admin.role_name_{$role->slug}")); ?></h3>
                            <span class="role-card-slug"><?php echo e(__('general.'.$role->slug)); ?> </span>
                        </div>
                        <div style="text-align:end">
                            <div class="role-card-users"><?php echo e($role->users_count); ?></div>
                            <div class="role-card-users-label"><?php echo e(__('general.users')); ?></div>
                        </div>
                    </div>
                    <p class="role-card-desc"><?php echo e($role->description); ?></p>
                    <div class="role-card-perms">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $role->permissions->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <span class="role-card-perm-badge"><?php echo e(__("super-admin.perm_" . str_replace('.', '_', $perm->slug))); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <span style="font-size:12px;color:var(--text-muted)">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($role->permissions->count() > 5): ?>
                            <span class="badge" style="font-size:10px;background:var(--border);color:var(--text-muted);padding:2px 8px;border-radius:4px">+<?php echo e($role->permissions->count() - 5); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <a href="<?php echo e(route('settings.workspace.roles.show', $role)); ?>" class="btn" style="width:100%;padding:9px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:6px;cursor:pointer;margin-top:16px">
                        <i class="bi bi-eye"></i><?php echo e(__('general.view')); ?>

                    </a>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\settings\roles.blade.php ENDPATH**/ ?>