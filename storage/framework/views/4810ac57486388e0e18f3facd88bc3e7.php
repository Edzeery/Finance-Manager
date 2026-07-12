<?php

use App\Models\Payment;
use App\Services\OnboardingService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;

?>

<div class="auth-card animate-fade-in">
    <div class="auth-logo">
        <div class="logo-icon">FM</div>
        <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
        <span class="logo-sub"><?php echo e(__('onboarding.setup_workspace')); ?></span>
    </div>

    <p class="text-muted small mb-4"><?php echo e(__('onboarding.setup_workspace_desc')); ?></p>

    <form wire:submit="complete">
        <div class="mb-4">
            <label for="workspaceName" class="form-label-custom"><?php echo e(__('onboarding.workspace_name')); ?></label>
            <input wire:model="workspaceName" id="workspaceName" type="text"
                class="form-custom <?php $__errorArgs = ['workspaceName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                placeholder="<?php echo e(__('onboarding.workspace_placeholder')); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['workspaceName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="text-danger mt-1 small"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <button type="submit" class="btn btn-accent btn-custom w-100 mb-2"
            wire:loading.attr="disabled" wire:target="complete">
            <div wire:loading wire:target="complete" class="spinner-border spinner-border-sm me-2" role="status"></div>
            <?php echo e(__('onboarding.finish')); ?>

        </button>

        <button type="button" class="btn btn-outline-accent btn-custom w-100" wire:click="skip">
            <?php echo e(__('onboarding.skip')); ?>

        </button>
    </form>
</div><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire\pages\onboarding\setup.blade.php ENDPATH**/ ?>