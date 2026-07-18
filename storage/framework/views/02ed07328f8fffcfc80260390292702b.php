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
     <?php $__env->slot('pageTitle', null, []); ?> 
        <a href="<?php echo e(route('super.admin.notifications.index')); ?>" class="text-decoration-none text-reset">
            <i class="bi bi-arrow-left me-1"></i><?php echo e(__('admin.notifications')); ?>

        </a>
     <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(locale_name($notification, 'title') ?: $notification->title_en); ?> <?php $__env->endSlot(); ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <?php
                        $notificationStatus = status('notification', $notification->type);
                        $title = locale_name($notification, 'title');
                        $message = locale_name($notification, 'message');
                    ?>

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="badge rounded-circle p-3 d-inline-flex align-items-center justify-content-center <?php echo e($notificationStatus->color()); ?>" style="width:48px;height:48px;font-size:20px">
                            <?php echo $notificationStatus->icon('bi'); ?>

                        </span>
                        <div>
                            <h4 class="mb-1"><?php echo e($title ?: $notification->title_en); ?></h4>
                            <div class="d-flex gap-3 text-muted small">
                                <span><i class="bi bi-clock me-1"></i><?php echo e($notification->created_at->format('Y/m/d H:i')); ?></span>
                                <span><i class="bi bi-tag me-1"></i><?php echo e($notification->type); ?></span>
                                <span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notification->is_read): ?>
                                        <i class="bi bi-check-circle text-success me-1"></i><?php echo e(__('admin.mark_read')); ?>

                                    <?php else: ?>
                                        <i class="bi bi-circle text-warning me-1"></i><?php echo e(__('admin.new')); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 p-3 rounded" style="background:var(--bg-subtle);border:1px solid var(--border-light)">
                        <p class="mb-0" style="font-size:14px;line-height:1.7"><?php echo e($message ?: $notification->message_en); ?></p>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notification->data): ?>
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-1"></i><?php echo e(__('general.details')); ?></h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0" style="font-size:13px">
                                    <tbody>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $notification->data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <th style="width:140px;background:var(--bg-subtle)" class="text-muted fw-normal"><?php echo e($key); ?></th>
                                                <td><?php echo e(is_array($value) ? json_encode($value) : $value); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="d-flex gap-2">
                        <a href="<?php echo e(route('super.admin.notifications.index')); ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i><?php echo e(__('general.back')); ?>

                        </a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$notification->is_read): ?>
                            <form action="<?php echo e(route('super.admin.notifications.read', $notification)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-check me-1"></i><?php echo e(__('admin.mark_read')); ?>

                                </button>
                            </form>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <form action="<?php echo e(route('super.admin.notifications.destroy', $notification)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                onclick="return confirm('<?php echo e(__('admin.confirm_delete_notification')); ?>')">
                                <i class="bi bi-trash me-1"></i><?php echo e(__('admin.delete')); ?>

                            </button>
                        </form>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\super-admin\notifications\show.blade.php ENDPATH**/ ?>