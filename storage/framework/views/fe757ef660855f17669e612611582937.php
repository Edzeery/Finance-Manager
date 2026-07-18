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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('admin.notifications')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('admin.notifications')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('admin.notifications_description', ['count' => $notifications->total()])); ?> <?php $__env->endSlot(); ?>

    
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div class="d-flex gap-1">
            <a href="<?php echo e(route('super.admin.notifications.index', ['filter' => 'all', 'type' => request('type')])); ?>"
               class="btn btn-sm rounded-pill <?php echo e($filter === 'all' ? 'btn-accent' : 'btn-outline-secondary'); ?>">
                <?php echo e(__('general.all')); ?>

            </a>
            <a href="<?php echo e(route('super.admin.notifications.index', ['filter' => 'unread', 'type' => request('type')])); ?>"
               class="btn btn-sm rounded-pill <?php echo e($filter === 'unread' ? 'btn-accent' : 'btn-outline-secondary'); ?>">
                <?php echo e(__('admin.new')); ?>

            </a>
            <?php
                $typeFilters = [
                    'new_user' => __('super-admin.users'),
                    'new_payment' => __('super-admin.payments'),
                    'subscription_activated' => __('super-admin.subscriptions'),
                    'backup_completed' => __('super-admin.backups'),
                    'system_alert' => __('super-admin.system'),
                ];
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $typeFilters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $typeKey => $typeLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('super.admin.notifications.index', ['filter' => $filter, 'type' => $typeKey === ($type ?? '') ? null : $typeKey])); ?>"
                   class="btn btn-sm rounded-pill <?php echo e(($type ?? '') === $typeKey ? 'btn-accent' : 'btn-outline-secondary'); ?>">
                    <?php echo e($typeLabel); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="d-flex gap-2">
            <?php $unreadCount = $notifications->where('is_read', false)->count(); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
                <form action="<?php echo e(route('super.admin.notifications.mark-all-read')); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-check-all me-1"></i><?php echo e(__('admin.mark_all_read')); ?>

                    </button>
                </form>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="card">
        <div class="card-body p-0">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $notificationStatus = status('notification', $notification->type);
                    $title = locale_name($notification, 'title');
                    $message = locale_name($notification, 'message');
                ?>
                <div class="notification-item p-3 border-bottom notification-transition <?php echo e($notification->is_read ? '' : 'bg-light'); ?>" data-id="<?php echo e($notification->id); ?>">
                    <div class="d-flex align-items-start gap-3">
                        <div class="notification-icon flex-shrink-0">
                            <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center <?php echo e($notificationStatus->color()); ?>" style="width:36px;height:36px;font-size:14px">
                                <?php echo $notificationStatus->icon('bi'); ?>

                            </span>
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 <?php echo e($notification->is_read ? '' : 'fw-bold'); ?>">
                                        <a href="<?php echo e(route('super.admin.notifications.show', $notification)); ?>" class="text-decoration-none text-reset stretched-link">
                                            <?php echo e($title ?: $notification->title_en); ?>

                                        </a>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$notification->is_read): ?>
                                            <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => 'info','set' => 'bi','size' => 'xs','class' => 'ms-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'info','set' => 'bi','size' => 'xs','class' => 'ms-1']); ?>
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
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </h6>
                                    <p class="mb-0 text-muted small text-truncate" style="max-width:600px">
                                        <?php echo e($message ?: $notification->message_en); ?>

                                    </p>
                                </div>
                                <small class="text-muted text-nowrap ms-3 flex-shrink-0"><?php echo e($notification->created_at->diffForHumans()); ?></small>
                            </div>
                            <div class="d-flex gap-2 mt-2 position-relative" style="z-index:2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$notification->is_read): ?>
                                    <form action="<?php echo e(route('super.admin.notifications.read', $notification)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size:12px">
                                            <i class="bi bi-check me-1"></i><?php echo e(__('admin.mark_read')); ?>

                                        </button>
                                    </form>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notification->data): ?>
                                    <span class="text-muted small" style="font-size:12px">
                                        <i class="bi bi-info-circle me-1"></i>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($notification->type):
                                            case ('new_user'): ?>
                                                <?php echo e($notification->data['email'] ?? ''); ?>

                                                <?php break; ?>
                                            <?php case ('new_payment'): ?>
                                                <?php echo e(isset($notification->data['amount']) ? currency_format($notification->data['amount'], $notification->data['currency'] ?? null) : ''); ?>

                                                <?php break; ?>
                                            <?php case ('subscription_activated'): ?>
                                                <?php echo e($notification->data['plan'] ?? ''); ?>

                                                <?php break; ?>
                                            <?php case ('backup_completed'): ?>
                                                <?php echo e($notification->data['filename'] ?? ''); ?>

                                                <?php break; ?>
                                        <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <form id="delete-notification-<?php echo e($notification->id); ?>" action="<?php echo e(route('super.admin.notifications.destroy', $notification)); ?>" method="POST" class="d-inline ms-auto" style="display:none">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                </form>
                                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none text-danger"
                                    style="font-size:12px"
                                    @click="showConfirmModal(
                                        '<?php echo e(__('general.confirm')); ?>',
                                        '<?php echo e(__('admin.confirm_delete_notification')); ?>',
                                        function(c) { if(c) { document.getElementById('delete-notification-<?php echo e($notification->id); ?>').submit(); } },
                                        '<?php echo e(__('general.delete')); ?>',
                                        'btn-danger'
                                    )">
                                    <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'notification','status' => 'delete','set' => 'bi','class' => 'text-lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'notification','status' => 'delete','set' => 'bi','class' => 'text-lg']); ?>
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
                                </button>
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
        <?php echo e($notifications->withQueryString()->links()); ?>

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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\super-admin\notifications\index.blade.php ENDPATH**/ ?>