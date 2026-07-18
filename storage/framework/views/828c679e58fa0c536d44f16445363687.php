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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('profile.title')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('general.profile')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('profile.page_description')); ?> <?php $__env->endSlot(); ?>

    <?php
        $user = Auth::user();
        $initials = implode('', array_map(fn($n) => $n[0] ?? '', array_filter(explode(' ', $user->name))));
    ?>

    <div class="profile-grid">
        <div class="profile-sidebar">
            <div class="profile-card">
                <div class="profile-avatar">
                    <div class="avatar-circle"><?php echo e($initials); ?></div>
                    <div class="avatar-online"></div>
                </div>
                <h4 class="profile-name mt-4"><?php echo e($user->name); ?></h4>
                <p class="profile-email"><?php echo e($user->email); ?></p>
                <span class="profile-joined"><?php echo e(__('profile.member_since')); ?> <?php echo e($user->created_at->translatedFormat('F Y')); ?></span>
                <div class="mt-3 text-center">
                    <span class="badge" style="background:var(--accent);color:#0F172A;font-size:12px;padding:4px 12px;border-radius:20px">
                        <i class="bi bi-shield-fill-check me-1"></i><?php echo e(__('super-admin.super_admin')); ?>

                    </span>
                </div>
            </div>

            <div class="profile-card">
                <div class="profile-card-header">
                    <i class="bi bi-link-45deg"></i>
                    <span><?php echo e(__('profile.quick_links')); ?></span>
                </div>
                <nav class="profile-nav">
                    <a href="<?php echo e(route('super.admin.dashboard')); ?>" wire:navigate class="profile-nav-item">
                        <i class="bi bi-shield-shaded"></i>
                        <span><?php echo e(__('super-admin.dashboard')); ?></span>
                    </a>
                    <a href="<?php echo e(route('super.admin.settings.index')); ?>" wire:navigate class="profile-nav-item">
                        <i class="bi bi-gear-fill"></i>
                        <span><?php echo e(__('super-admin.settings')); ?></span>
                    </a>
                    <a href="<?php echo e(route('super.admin.activity-log')); ?>" wire:navigate class="profile-nav-item">
                        <i class="bi bi-clock-history"></i>
                        <span><?php echo e(__('super-admin.activity_log')); ?></span>
                    </a>
                </nav>
            </div>
        </div>

        <div class="profile-main">
            <div class="profile-card">
                <div class="profile-card-header">
                    <i class="bi bi-person-badge"></i>
                    <span><?php echo e(__('general.account_details')); ?></span>
                </div>
                <div class="profile-info-grid">
                    <div class="info-item">
                        <span class="info-label"><?php echo e(__('general.name')); ?></span>
                        <span class="info-value"><?php echo e($user->name); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><?php echo e(__('general.email')); ?></span>
                        <span class="info-value"><?php echo e($user->email); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><?php echo e(__('general.roles')); ?></span>
                        <span class="info-value">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => 'info','set' => 'bi','size' => 'xs','class' => 'me-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'info','set' => 'bi','size' => 'xs','class' => 'me-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><?php echo e(__('profile.member_since')); ?></span>
                        <span class="info-value"><?php echo e($user->created_at->format('Y/m/d')); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\super-admin\account\profile.blade.php ENDPATH**/ ?>