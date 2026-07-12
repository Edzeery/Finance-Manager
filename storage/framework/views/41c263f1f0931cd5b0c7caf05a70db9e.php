<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

?>

<div class="auth-card animate-fade-in">

    <div class="auth-logo">
        <div class="logo-icon">FM</div>
        <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
        <span class="logo-sub"><?php echo e(__('general.forgot_password')); ?></span>
    </div>

    <p class="text-muted text-center mb-4" style="font-size:14px">
        <?php echo e(__('general.forgot_password_desc')); ?>

    </p>

    <?php if (isset($component)) { $__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.auth-session-status','data' => ['status' => session('status')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('auth-session-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(session('status'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5)): ?>
<?php $attributes = $__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5; ?>
<?php unset($__attributesOriginal7c1bf3a9346f208f66ee83b06b607fb5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5)): ?>
<?php $component = $__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5; ?>
<?php unset($__componentOriginal7c1bf3a9346f208f66ee83b06b607fb5); ?>
<?php endif; ?>

    <form wire:submit="sendPasswordResetLink">

        <div class="mb-4">
            <label for="email" class="form-label-custom"><?php echo e(__('general.email')); ?></label>
            <div class="input-icon-wrap">
                <i class="bi bi-envelope input-icon-left"></i>
                <input wire:model="email"
                       id="email" type="email" name="email"
                       required autofocus
                       class="form-custom has-icon-left <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       placeholder="email@example.com">
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="field-error"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <button type="submit" class="btn btn-accent btn-custom w-100">
            <div wire:loading wire:target="sendPasswordResetLink" class="spinner-border spinner-border-sm me-2" role="status"></div>
            <i class="bi bi-envelope me-2" wire:loading.remove wire:target="sendPasswordResetLink"></i>
            <?php echo e(__('general.send_reset_link')); ?>

        </button>

    </form>

    <div class="auth-footer">
        <a href="<?php echo e(route('login')); ?>" wire:navigate class="link-muted">
            <i class="bi bi-arrow-left me-1"></i><?php echo e(__('general.back_to_login')); ?>

        </a>
    </div>

</div><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire\pages\auth\forgot-password.blade.php ENDPATH**/ ?>