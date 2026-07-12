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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('notifications.page_title') ?? __('general.notifications')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('notifications.page_title') ?? __('general.notifications')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('notifications.page_description') ?? ''); ?> <?php $__env->endSlot(); ?>

    <div class="card-custom">
        <div class="card-body p-0">
            <?php $locale = app()->getLocale(); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="d-flex align-items-start gap-3 px-4 py-3 <?php echo e(!$notification->is_read ? '' : ''); ?>" style="<?php echo e(!$notification->is_read ? 'background:rgba(21,183,108,0.03)' : ''); ?>; border-bottom:1px solid var(--border); transition:background 0.2s">
                    <?php
                        $notifIcon = match($notification->type) {
                            'budget_exceeded', 'budget_nearing_limit' => ['bg' => 'rgba(239,68,68,0.1)', 'color' => 'var(--danger)', 'icon' => 'bi-exclamation-triangle'],
                            'debt_reminder' => ['bg' => 'rgba(245,158,11,0.1)', 'color' => 'var(--warning)', 'icon' => 'bi-credit-card-2-front'],
                            'goal_achieved', 'goal_milestone' => ['bg' => 'rgba(34,197,94,0.1)', 'color' => 'var(--success)', 'icon' => 'bi-flag'],
                            'goal_deadline' => ['bg' => 'rgba(59,130,246,0.1)', 'color' => 'var(--info)', 'icon' => 'bi-clock'],
                            'zakat_reminder' => ['bg' => 'rgba(139,92,246,0.1)', 'color' => 'var(--sa-indigo)', 'icon' => 'bi-heart'],
                            'role_changed' => ['bg' => 'rgba(245,158,11,0.1)', 'color' => 'var(--warning)', 'icon' => 'bi-shield-check'],
                            default => ['bg' => 'rgba(59,130,246,0.1)', 'color' => 'var(--info)', 'icon' => 'bi-info-circle'],
                        };
                    ?>
                    <div style="flex-shrink:0; width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:<?php echo e($notifIcon['bg']); ?>; color:<?php echo e($notifIcon['color']); ?>">
                        <i class="bi <?php echo e($notifIcon['icon']); ?>"></i>
                    </div>
                    <div style="flex:1; min-width:0">
                        <div style="font-size:14px; font-weight:500; color:var(--text)">
                            <?php echo e($notification->{'title_' . $locale}); ?>

                        </div>
                        <div style="font-size:13px; color:var(--text-muted); margin-top:2px">
                            <?php echo e($notification->{'message_' . $locale}); ?>

                        </div>
                        <div style="font-size:11px; color:var(--text-muted); margin-top:4px">
                            <?php echo e($notification->created_at->diffForHumans()); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notification->read_at): ?>
                                · <?php echo e(__('notifications.read_at') ?? 'Read'); ?> <?php echo e($notification->read_at->diffForHumans()); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$notification->is_read): ?>
                        <form method="POST" action="<?php echo e(route('notifications.read', $notification)); ?>" style="flex-shrink:0">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-sm p-1" style="color:var(--accent); background:none; border:none; font-size:18px; line-height:1" title="<?php echo e(__('notifications.mark_read') ?? 'Mark as read'); ?>">
                                <i class="bi bi-check-circle"></i>
                            </button>
                        </form>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="py-4">
                    <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'bi bi-bell-slash','title' => __('general.no_notifications')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi bi-bell-slash','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('general.no_notifications'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notifications->hasPages()): ?>
        <div class="mt-3">
            <?php echo e($notifications->appends(request()->query())->links()); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\notifications\index.blade.php ENDPATH**/ ?>