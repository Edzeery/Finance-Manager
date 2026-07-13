<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

?>

<div>
    <button type="button" class="btn btn-outline-danger btn-custom px-4"
            data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
        <i class="bi bi-trash me-1"></i><?php echo e(__('general.delete_account')); ?>

    </button>

    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background:var(--card-bg); border:1px solid var(--border); border-radius:12px">
                <div class="modal-body p-4">
                    <h5 class="fw-bold mb-2" style="color:var(--text)"><?php echo e(__('profile.delete_confirm')); ?></h5>
                    <p class="mb-4" style="font-size:13px; color:var(--text-muted)"><?php echo e(__('profile.delete_confirm_help')); ?></p>

                    <form wire:submit="deleteUser">
                        <div class="mb-3">
                            <?php if (isset($component)) { $__componentOriginalb37ff04c7d1d761340845e7d275eabcc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb37ff04c7d1d761340845e7d275eabcc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.password-input','data' => ['wire:model' => 'password','name' => 'password','placeholder' => ''.e(__('general.password')).'','autocomplete' => 'current-password','error' => 'password']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('password-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'password','name' => 'password','placeholder' => ''.e(__('general.password')).'','autocomplete' => 'current-password','error' => 'password']); ?>
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
                        <div class="d-flex gap-3 justify-content-end">
                            <button type="button" class="btn btn-outline-secondary btn-custom" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i><?php echo e(__('general.cancel')); ?></button>
                            <button type="submit" class="btn btn-danger btn-custom">
                                <i class="bi bi-trash me-1"></i><?php echo e(__('general.delete_account')); ?>

                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire/profile/delete-user-form.blade.php ENDPATH**/ ?>