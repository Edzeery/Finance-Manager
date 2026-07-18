<?php

use App\Models\User;
use App\Services\RedirectService;
use App\Services\TwoFactorAuthenticationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

?>

<div class="auth-card animate-fade-in">
    <div class="settings-card col-md-6 mx-auto" style="border:none;box-shadow:none;padding:0;">
        <div class="auth-logo">
            <div class="logo-icon"><i class="bi bi-shield-lock"></i></div>
            <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
            <span class="logo-sub"><?php echo e(__('general.two_factor_auth')); ?></span>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($methods) > 1): ?>
            <div class="d-flex justify-content-center gap-2 mb-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button wire:click="selectMethod('<?php echo e($method); ?>')" type="button"
                        class="btn btn-sm px-3 py-2 d-flex align-items-center gap-1"
                        style="border:2px solid <?php echo e($authMethod === $method ? 'var(--accent)' : 'var(--border)'); ?>;background:<?php echo e($authMethod === $method ? 'var(--accent-subtle, rgba(21,183,108,0.05))' : 'transparent'); ?>;color:<?php echo e($authMethod === $method ? 'var(--accent)' : 'var(--text-muted)'); ?>;font-weight:<?php echo e($authMethod === $method ? '600' : '400'); ?>;transition:all 0.2s;border-radius:var(--radius-sm,8px);">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($method === 'app'): ?>
                            <i class="bi bi-phone"></i>
                        <?php else: ?>
                            <i class="bi bi-envelope"></i>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <span><?php echo e($method === 'app' ? __('general.authenticator_app') : __('general.email_method')); ?></span>
                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($authMethod === 'email'): ?>
            <form wire:submit="verify">
                <p class="text-muted text-center mb-4" style="font-size:14px">
                    <?php echo e(__('messages.enter_email_auth_code')); ?>

                </p>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($emailCodeSent): ?>
                    <div class="mb-4">
                        <label for="emailCode" class="form-label-custom"><?php echo e(__('general.verification_code')); ?></label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope input-icon-left"></i>
                            <input wire:model="emailCode" id="emailCode" type="text" inputmode="numeric"
                                autocomplete="one-time-code"
                                class="form-custom has-icon-left text-center tracking-wide <?php $__errorArgs = ['emailCode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                placeholder="000 000" maxlength="6" required autofocus
                                style="letter-spacing:.3em; font-size:1.25rem; font-weight:600;">
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['emailCode'];
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

                    <button type="submit" class="btn btn-accent btn-custom w-100 mb-3">
                        <div wire:loading wire:target="verify" class="spinner-border spinner-border-sm me-2"
                            role="status"></div>
                        <i class="bi bi-check-circle me-2" wire:loading.remove wire:target="verify"></i>
                        <?php echo e(__('general.verify')); ?>

                    </button>

                    <div class="text-center">
                        <button type="button" wire:click="resendEmailCode" class="btn-text-link">
                            <div wire:loading wire:target="resendEmailCode"
                                class="spinner-border spinner-border-sm me-1" role="status"
                                style="width:12px;height:12px;"></div>
                            <i class="bi bi-arrow-clockwise me-1" wire:loading.remove wire:target="resendEmailCode"></i>
                            <?php echo e(__('general.resend_code')); ?>

                        </button>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <div class="spinner-border text-accent mb-3" role="status" style="width:2rem;height:2rem;">
                        </div>
                        <p class="text-muted"><?php echo e(__('messages.sending_code')); ?></p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </form>
        <?php elseif($mode === 'code'): ?>
            <form wire:submit="verify">
                <p class="text-muted text-center mb-4" style="font-size:14px">
                    <?php echo e(__('messages.enter_auth_code')); ?>

                </p>

                <div class="mb-4">
                    <label for="code" class="form-label-custom"><?php echo e(__('general.authentication_code')); ?></label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-grid-3x2-gap input-icon-left"></i>
                        <input wire:model="code" id="code" type="text" inputmode="numeric"
                            autocomplete="one-time-code"
                            class="form-custom has-icon-left text-center tracking-wide <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="000 000" maxlength="6" required autofocus
                            style="letter-spacing:.3em; font-size:1.25rem; font-weight:600;">
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['code'];
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

                <button type="submit" class="btn btn-accent btn-custom w-100 mb-3">
                    <div wire:loading wire:target="verify" class="spinner-border spinner-border-sm me-2" role="status">
                    </div>
                    <i class="bi bi-check-circle me-2" wire:loading.remove wire:target="verify"></i>
                    <?php echo e(__('general.verify')); ?>

                </button>

                <div class="text-center">
                    <button type="button" wire:click="switchMode" class="btn-text-link">
                        <i class="bi bi-key me-1"></i><?php echo e(__('general.use_recovery_code')); ?>

                    </button>
                </div>
            </form>
        <?php else: ?>
            <form wire:submit="verify">
                <p class="text-muted text-center mb-4" style="font-size:14px">
                    <?php echo e(__('messages.enter_recovery_code')); ?>

                </p>

                <div class="mb-4">
                    <label for="recoveryCode" class="form-label-custom"><?php echo e(__('general.recovery_code')); ?></label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-key input-icon-left"></i>
                        <input wire:model="recoveryCode" id="recoveryCode" type="text" autocomplete="off"
                            class="form-custom has-icon-left text-center <?php $__errorArgs = ['recoveryCode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="XXXXXX-XXXXXX" required autofocus style="letter-spacing:.05em;">
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['recoveryCode'];
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

                <button type="submit" class="btn btn-accent btn-custom w-100 mb-3">
                    <div wire:loading wire:target="verify" class="spinner-border spinner-border-sm me-2"
                        role="status"></div>
                    <i class="bi bi-check-circle me-2" wire:loading.remove wire:target="verify"></i>
                    <?php echo e(__('general.verify')); ?>

                </button>

                <div class="text-center">
                    <button type="button" wire:click="switchMode" class="btn-text-link">
                        <i class="bi bi-phone me-1"></i><?php echo e(__('general.use_auth_code')); ?>

                    </button>
                </div>
            </form>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    </div>

    <div class="auth-divider"><?php echo e(__('general.or')); ?></div>

    <div class="auth-footer">
        <a href="<?php echo e(route('login')); ?>" wire:navigate class="link-muted">
            <i class="bi bi-arrow-left me-1"></i><?php echo e(__('general.back_to_login')); ?>

        </a>
    </div>

</div><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire\pages\auth\two-factor-challenge.blade.php ENDPATH**/ ?>