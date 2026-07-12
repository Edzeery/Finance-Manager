<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

?>

<div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status') === 'verification-link-sent'): ?>
        <div class="alert mb-3 animate-fade-in" style="background:rgba(34,197,94,0.12); color:var(--success); border:none; border-radius:8px; font-size:13px; padding:10px 14px">
            <i class="bi bi-check-circle me-1"></i><?php echo e(__('messages.verification_sent')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <form wire:submit="updateProfileInformation">
        <div class="mb-3">
            <label class="form-label-custom"><?php echo e(__('general.name')); ?></label>
            <input wire:model="name" type="text" class="form-custom <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required autofocus>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger mt-1" style="font-size:12px"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label-custom"><?php echo e(__('general.email')); ?></label>
            <input wire:model="email" type="email" class="form-custom <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required autocomplete="username">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger mt-1" style="font-size:12px"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail()): ?>
                <div class="mt-2">
                    <span style="font-size:13px; color:var(--text-muted)"><?php echo e(__('general.email_unverified')); ?></span>
                    <button wire:click.prevent="sendVerification" type="button" class="btn btn-sm p-0 ms-1" style="color:var(--accent); text-decoration:underline; font-size:13px; background:none; border:none; vertical-align:baseline">
                        <?php echo e(__('general.resend_verification')); ?>

                    </button>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-accent btn-custom"><i class="bi bi-check-lg me-1"></i><?php echo e(__('general.save')); ?></button>
            <div wire:loading wire:target="updateProfileInformation" class="spinner-border spinner-border-sm" role="status" style="color:var(--accent)"></div>
            <span wire:loading.remove wire:target="updateProfileInformation" wire:transition
                  x-data="{ show: false }"
                  x-on:profile-updated.window="show = true; setTimeout(() => show = false, 2000)"
                  x-show="show" style="display:none; font-size:13px; color:var(--success)">
                <i class="bi bi-check-circle me-1"></i><?php echo e(__('general.saved')); ?>

            </span>
        </div>
    </form>
</div><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire\profile\update-profile-information-form.blade.php ENDPATH**/ ?>