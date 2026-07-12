<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

?>

<div class="auth-card animate-fade-in">

    <div class="auth-logo">
        <div class="logo-icon">FM</div>
        <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
        <span class="logo-sub"><?php echo e(__('general.confirm_password')); ?></span>
    </div>

    <p class="text-muted text-center mb-4" style="font-size:14px">
        <?php echo e(__('general.confirm_password_desc')); ?>

    </p>

    <form wire:submit="confirmPassword">

        <div class="mb-4">
            <label for="password" class="form-label-custom"><?php echo e(__('general.password')); ?></label>
            <?php if (isset($component)) { $__componentOriginalb37ff04c7d1d761340845e7d275eabcc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb37ff04c7d1d761340845e7d275eabcc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.password-input','data' => ['wire:model' => 'password','id' => 'password','name' => 'password','required' => true,'autocomplete' => 'current-password','error' => 'password']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('password-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'password','id' => 'password','name' => 'password','required' => true,'autocomplete' => 'current-password','error' => 'password']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb37ff04c7d1d761340845e7d275eabcc)): ?>
<?php $attributes = $__attributesOriginalb37ff04c7d1d761340845e7d275eabcc; ?>
<?php unset($__attributesOriginalb37ff04c7d1d761340845e7d275eabcc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb37ff04c7d1d761340845e7d275eabcc)): ?>
<?php $component = $__componentOriginalb37ff04c7d1d761340845e7d275eabcc; ?>
<?php unset($__componentOriginalb37ff04c7d1d761340845e7d275eabcc); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
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
            <i class="bi bi-shield-check me-2"></i><?php echo e(__('general.confirm')); ?>

        </button>

    </form>

</div><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire/pages/auth/confirm-password.blade.php ENDPATH**/ ?>