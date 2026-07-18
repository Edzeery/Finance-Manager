<?php

use App\Models\User;
use App\Services\TwoFactorAuthenticationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Volt\Component;

?>

<div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $this->enabled): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $this->showingQrCode): ?>
            <p class="text-muted" style="font-size:14px;">
                <?php echo e(__('messages.add_2fa_security')); ?>

            </p>
            <button wire:click="showQrCode" class="btn btn-accent btn-custom mt-2">
                <?php echo e(__('general.setup_2fa')); ?>

            </button>
        <?php else: ?>
            <div class="text-center my-3">
                <p><?php echo e(__('messages.scan_qr_code')); ?></p>
                <img src="<?php echo e($this->qrCodeInline); ?>" alt="QR Code" style="max-width:200px;">
            </div>

            <div class="mb-3">
                <label class="form-label-custom"><?php echo e(__('general.enter_key_manually')); ?></label>
                <div class="d-flex align-items-center gap-2">
                    <code style="font-size:14px; padding:8px; background:var(--bg-card); border-radius:6px; word-break:break-all;"><?php echo e($this->secret); ?></code>
                </div>
            </div>

            <div class="mb-3">
                <label for="code" class="form-label-custom"><?php echo e(__('general.verify_code')); ?></label>
                <input wire:model="code" id="code" type="text" inputmode="numeric"
                       class="form-custom <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       placeholder="000000" maxlength="6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="text-danger mt-1" style="font-size:13px"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <button wire:click="confirm" class="btn btn-accent btn-custom">
                <div wire:loading wire:target="confirm" class="spinner-border spinner-border-sm me-2" role="status"></div>
                <?php echo e(__('general.confirm_enable')); ?>

            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php else: ?>
        <div class="alert alert-success d-flex align-items-center gap-2" style="font-size:14px;">
            <i class="bi bi-shield-check"></i>
            <?php echo e(__('messages.two_factor_enabled')); ?>

        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->showingRecoveryCodes): ?>
            <div class="mb-3">
                <p><strong><?php echo e(__('general.recovery_codes')); ?></strong></p>
                <p class="text-muted" style="font-size:13px;">
                    <?php echo e(__('messages.store_recovery_codes')); ?>

                </p>
                <div style="background:var(--bg-card); padding:12px; border-radius:6px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->recoveryCodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recoveryCode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="font-family:monospace; font-size:14px; padding:4px 0;"><?php echo e($recoveryCode); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <button wire:click="regenerateRecoveryCodes" class="btn btn-sm btn-outline-secondary mt-2">
                    <?php echo e(__('general.regenerate_recovery_codes')); ?>

                </button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <button wire:click="confirmDisable" class="btn btn-danger btn-custom mt-2">
            <?php echo e(__('general.disable')); ?>

        </button>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($confirming): ?>
            <div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
                <div style="background:var(--card-bg,#fff);border-radius:var(--radius-md,12px);max-width:400px;width:100%;padding:1.5rem;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
                    <h5 style="font-size:16px;font-weight:600;margin-bottom:8px;"><?php echo e(__('general.confirm')); ?></h5>
                    <p style="font-size:14px;color:var(--text-muted);margin-bottom:1.5rem;"><?php echo e(__('messages.confirm_disable_2fa')); ?></p>
                    <div style="display:flex;gap:8px;justify-content:flex-end;">
                        <button wire:click="cancelConfirmation" type="button" class="btn btn-outline-secondary btn-custom" style="font-size:13px;padding:7px 16px;">
                            <?php echo e(__('general.cancel')); ?>

                        </button>
                        <button wire:click="executeConfirmed" type="button" class="btn btn-danger btn-custom" style="font-size:13px;padding:7px 16px;">
                            <?php echo e(__('general.disable')); ?>

                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire\profile\two-factor-form.blade.php ENDPATH**/ ?>