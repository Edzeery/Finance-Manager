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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('admin.notifications')); ?> <?php $__env->endSlot(); ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <p class="text-muted mb-0">
                <?php echo e(__('admin.notifications_description', ['count' => $notifications->total()])); ?>

            </p>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notifications->where('is_read', false)->count() > 0): ?>
            <form action="<?php echo e(route('super.admin.notifications.mark-all-read')); ?>" method="POST" class="d-inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-check-all me-1"></i><?php echo e(__('admin.mark_all_read')); ?>

                </button>
            </form>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="notification-item p-3 border-bottom <?php echo e($notification->is_read ? '' : 'bg-light'); ?>" data-id="<?php echo e($notification->id); ?>">
                    <div class="d-flex align-items-start gap-3">
                        <div class="notification-icon flex-shrink-0">
                            <?php
                                $iconMap = [
                                    'new_user' => 'bi-person-plus',
                                    'new_payment' => 'bi-cash-stack',
                                    'subscription_activated' => 'bi-stars',
                                    'backup_completed' => 'bi-cloud-check',
                                    'system_alert' => 'bi-exclamation-triangle',
                                ];
                                $icon = $iconMap[$notification->type] ?? 'bi-bell';
                                $colorMap = [
                                    'new_user' => 'primary',
                                    'new_payment' => 'success',
                                    'subscription_activated' => 'info',
                                    'backup_completed' => 'secondary',
                                    'system_alert' => 'warning',
                                ];
                                $color = $colorMap[$notification->type] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?php echo e($color); ?> rounded-circle p-2">
                                <i class="bi <?php echo e($icon); ?>"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="mb-1 <?php echo e($notification->is_read ? '' : 'fw-bold'); ?>">
                                    <?php echo e($notification->title_en); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$notification->is_read): ?>
                                        <span class="badge bg-primary ms-1"><?php echo e(__('admin.new')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </h6>
                                <small class="text-muted text-nowrap ms-2"><?php echo e($notification->created_at->diffForHumans()); ?></small>
                            </div>
                            <p class="mb-1 text-muted small"><?php echo e($notification->message_en); ?></p>
                            <div class="d-flex gap-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$notification->is_read): ?>
                                    <form action="<?php echo e(route('super.admin.notifications.read', $notification)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none">
                                            <i class="bi bi-check me-1"></i><?php echo e(__('admin.mark_read')); ?>

                                        </button>
                                    </form>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <form action="<?php echo e(route('super.admin.notifications.destroy', $notification)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none text-danger"
                                        onclick="return confirm('<?php echo e(__('admin.confirm_delete_notification')); ?>')">
                                        <i class="bi bi-trash me-1"></i><?php echo e(__('admin.delete')); ?>

                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash display-4 text-muted"></i>
                    <p class="mt-3 text-muted"><?php echo e(__('admin.no_notifications')); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <div class="mt-3">
        <?php echo e($notifications->links()); ?>

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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/super-admin/notifications/index.blade.php ENDPATH**/ ?>