<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

?>

<div>
    <form wire:submit="updatePassword">
        <div class="mb-3">
            <label class="form-label-custom"><?php echo e(__('general.current_password')); ?></label>
            <?php if (isset($component)) { $__componentOriginalb37ff04c7d1d761340845e7d275eabcc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb37ff04c7d1d761340845e7d275eabcc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.password-input','data' => ['name' => 'current_password','wire:model' => 'current_password','autocomplete' => 'current-password','error' => 'current_password']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('password-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'current_password','wire:model' => 'current_password','autocomplete' => 'current-password','error' => 'current_password']); ?>
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
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger mt-1" style="font-size:12px"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label-custom"><?php echo e(__('general.new_password')); ?></label>
            <?php if (isset($component)) { $__componentOriginalb37ff04c7d1d761340845e7d275eabcc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb37ff04c7d1d761340845e7d275eabcc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.password-input','data' => ['name' => 'password','wire:model' => 'password','autocomplete' => 'new-password','error' => 'password']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('password-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'password','wire:model' => 'password','autocomplete' => 'new-password','error' => 'password']); ?>
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
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger mt-1" style="font-size:12px"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label-custom"><?php echo e(__('general.confirm_password')); ?></label>
            <?php if (isset($component)) { $__componentOriginalb37ff04c7d1d761340845e7d275eabcc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb37ff04c7d1d761340845e7d275eabcc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.password-input','data' => ['name' => 'password_confirmation','wire:model' => 'password_confirmation','autocomplete' => 'new-password']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('password-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'password_confirmation','wire:model' => 'password_confirmation','autocomplete' => 'new-password']); ?>
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
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-accent btn-custom"><i class="bi bi-check-lg me-1"></i><?php echo e(__('general.save')); ?></button>
            <div wire:loading wire:target="updatePassword" class="spinner-border spinner-border-sm" role="status" style="color:var(--accent)"></div>
            <span wire:loading.remove wire:target="updatePassword" wire:transition
                  x-data="{ show: false }"
                  x-on:password-updated.window="show = true; setTimeout(() => show = false, 2000)"
                  x-show="show" style="display:none; font-size:13px; color:var(--success)">
                <i class="bi bi-check-circle me-1"></i><?php echo e(__('general.saved')); ?>

            </span>
        </div>
    </form>
</div><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire/profile/update-password-form.blade.php ENDPATH**/ ?>