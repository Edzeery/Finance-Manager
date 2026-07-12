<nav class="-mx-3 flex flex-1 justify-end">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
        <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-accent btn-custom" style="padding:6px 16px; font-size:13px">
            <i class="bi bi-grid-1x2-fill me-1"></i><?php echo e(__('general.dashboard')); ?>

        </a>
    <?php else: ?>
        <a href="<?php echo e(route('login')); ?>" class="btn btn-sm btn-outline-secondary btn-custom" style="color:var(--sidebar-text); border-color:rgba(255,255,255,0.2)">
            <i class="bi bi-box-arrow-in-right me-1"></i><?php echo e(__('general.login')); ?>

        </a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Route::has('register')): ?>
            <a href="<?php echo e(route('register')); ?>" class="btn btn-sm btn-accent btn-custom ms-2">
                <i class="bi bi-person-plus me-1"></i><?php echo e(__('general.register')); ?>

            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</nav><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire\welcome\navigation.blade.php ENDPATH**/ ?>