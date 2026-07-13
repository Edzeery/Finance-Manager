<?php

use App\Models\User;
use App\Services\TwoFactorAuthenticationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

?>

<div>
    <div class="settings-card col-md-6 mx-auto">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                 style="width:64px;height:64px;background:var(--accent-subtle, rgba(21,183,108,0.1));">
                <i class="bi bi-shield-check" style="font-size:28px;color:var(--accent);"></i>
            </div>
            <h4 class="mb-1" style="font-weight:600;"><?php echo e(__('general.two_factor_authentication')); ?></h4>
            <p class="text-muted mb-0" style="font-size:14px;">
                <?php echo e(__('messages.add_extra_security_to_account')); ?>

            </p>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($setupMethod && (!$enabled || !$this->isMethodEnabled($setupMethod))): ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($setupMethod === 'app'): ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $showingQrCode): ?>
                    <div class="text-center py-3">
                        <p class="text-muted mb-3" style="font-size:14px;line-height:1.6;">
                            <?php echo e(__('messages.two_factor_setup_description')); ?>

                        </p>
                        <div class="d-flex flex-wrap justify-content-center gap-3 mb-3">
                            <div class="text-center p-3 rounded" style="min-width:120px;background:var(--bg-subtle);">
                                <div class="mb-1"><i class="bi bi-phone" style="font-size:22px;color:var(--accent);"></i></div>
                                <div style="font-size:12px;color:var(--text-muted);">1. <?php echo e(__('general.install_app')); ?></div>
                            </div>
                            <div class="text-center p-3 rounded" style="min-width:120px;background:var(--bg-subtle);">
                                <div class="mb-1"><i class="bi bi-qr-code" style="font-size:22px;color:var(--accent);"></i></div>
                                <div style="font-size:12px;color:var(--text-muted);">2. <?php echo e(__('general.scan_qr')); ?></div>
                            </div>
                            <div class="text-center p-3 rounded" style="min-width:120px;background:var(--bg-subtle);">
                                <div class="mb-1"><i class="bi bi-check-circle" style="font-size:22px;color:var(--accent);"></i></div>
                                <div style="font-size:12px;color:var(--text-muted);">3. <?php echo e(__('general.verify_code')); ?></div>
                            </div>
                        </div>
                        <button wire:click="showQrCode" class="btn btn-accent btn-custom px-4">
                            <i class="bi bi-qr-code me-2"></i>
                            <?php echo e(__('general.setup_2fa')); ?>

                        </button>
                    </div>
                <?php else: ?>
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                             style="width:40px;height:40px;background:var(--accent-subtle, rgba(21,183,108,0.1));">
                            <span style="font-size:16px;font-weight:700;color:var(--accent);">1</span>
                        </div>
                        <p class="mb-0" style="font-size:14px;font-weight:500;"><?php echo e(__('messages.scan_qr_code')); ?></p>
                        <p class="text-muted" style="font-size:13px;"><?php echo e(__('messages.use_auth_app_to_scan')); ?></p>
                    </div>
                    <div class="d-flex justify-content-center mb-4">
                        <div style="padding:16px;background:#fff;border-radius:12px;border:2px solid var(--border);display:inline-block;">
                            <img src="<?php echo e($this->qrCodeInline); ?>" alt="QR Code" style="width:180px;height:180px;display:block;">
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label-custom mb-0"><?php echo e(__('general.setup_key')); ?></label>
                            <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-0"
                                    @click="navigator.clipboard.writeText('<?php echo e($this->secret); ?>')"
                                    title="<?php echo e(__('general.copy')); ?>">
                                <i class="bi bi-clipboard" style="font-size:12px;"></i>
                            </button>
                        </div>
                        <div style="background:var(--bg-subtle);border-radius:8px;padding:10px 14px;font-family:monospace;font-size:13px;word-break:break-all;border:1px solid var(--border);">
                            <?php echo e($this->secret); ?>

                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                             style="width:40px;height:40px;background:var(--accent-subtle, rgba(21,183,108,0.1));">
                            <span style="font-size:16px;font-weight:700;color:var(--accent);">2</span>
                        </div>
                        <label for="code" class="form-label-custom"><?php echo e(__('general.enter_verification_code')); ?></label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-grid-3x2-gap input-icon-left"></i>
                            <input wire:model="code" id="code" type="text" inputmode="numeric"
                                   class="form-custom has-icon-left text-center tracking-wide <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                 autocomplete="one-time-code"  placeholder="000 000" maxlength="6" autofocus
                                   style="letter-spacing:.3em;font-size:1.15rem;font-weight:600;">
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
                    <button wire:click="confirm" class="btn btn-accent btn-custom w-100">
                        <div wire:loading wire:target="confirm" class="spinner-border spinner-border-sm me-2" role="status"></div>
                        <i class="bi bi-shield-check me-2" wire:loading.remove wire:target="confirm"></i>
                        <?php echo e(__('general.confirm_enable')); ?>

                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php elseif($setupMethod === 'email'): ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $emailCodeSent): ?>
                    <div class="text-center py-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                             style="width:56px;height:56px;background:var(--accent-subtle, rgba(21,183,108,0.1));">
                            <i class="bi bi-envelope" style="font-size:26px;color:var(--accent);"></i>
                        </div>
                        <p class="mb-2" style="font-size:14px;font-weight:500;"><?php echo e(__('general.email_verification')); ?></p>
                        <p class="text-muted mb-3" style="font-size:13px;">
                            <?php echo e(__('messages.email_2fa_description')); ?>

                        </p>
                        <button wire:click="sendEmailCode" class="btn btn-accent btn-custom px-4">
                            <div wire:loading wire:target="sendEmailCode" class="spinner-border spinner-border-sm me-2" role="status"></div>
                            <i class="bi bi-send me-2" wire:loading.remove wire:target="sendEmailCode"></i>
                            <?php echo e(__('general.send_code_to_email')); ?>

                        </button>
                    </div>
                <?php else: ?>
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                             style="width:40px;height:40px;background:var(--accent-subtle, rgba(21,183,108,0.1));">
                            <i class="bi bi-envelope" style="font-size:18px;color:var(--accent);"></i>
                        </div>
                        <p class="mb-0" style="font-size:14px;font-weight:500;"><?php echo e(__('general.enter_email_code')); ?></p>
                        <p class="text-muted" style="font-size:13px;">
                            <?php echo e(__('messages.email_code_sent')); ?>

                        </p>
                    </div>
                    <div class="mb-4">
                        <label for="emailCode" class="form-label-custom"><?php echo e(__('general.verification_code')); ?></label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-grid-3x2-gap input-icon-left"></i>
                            <input wire:model="emailCode" id="emailCode" type="text" inputmode="numeric"
                                   class="form-custom has-icon-left text-center tracking-wide <?php $__errorArgs = ['emailCode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                              autocomplete="one-time-code"     placeholder="000 000" maxlength="6" autofocus
                                   style="letter-spacing:.3em;font-size:1.15rem;font-weight:600;">
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
                    <button wire:click="confirmEmailCode" class="btn btn-accent btn-custom w-100 mb-2">
                        <div wire:loading wire:target="confirmEmailCode" class="spinner-border spinner-border-sm me-2" role="status"></div>
                        <i class="bi bi-shield-check me-2" wire:loading.remove wire:target="confirmEmailCode"></i>
                        <?php echo e(__('general.confirm_enable')); ?>

                    </button>
                    <div class="text-center">
                        <button wire:click="sendEmailCode" class="btn-text-link">
                            <i class="bi bi-arrow-clockwise me-1"></i><?php echo e(__('general.resend_code')); ?>

                        </button>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="text-center mt-3">
                <button wire:click="cancelSetup" class="btn-text-link">
                    <i class="bi bi-arrow-left me-1"></i><?php echo e(__('general.back')); ?>

                </button>
            </div>

        <?php else: ?>

            
            <div class="py-3">
                <p class="text-muted mb-4 text-center" style="font-size:14px;line-height:1.6;">
                    <?php echo e(__('messages.two_factor_setup_description')); ?>

                </p>

                <div class="d-flex flex-wrap justify-content-center gap-3 mb-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->availableMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $methodKey => $methodInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $methodEnabled = $this->isMethodEnabled($methodKey); ?>
                        <div class="d-flex flex-column align-items-center gap-1 px-4 py-3 rounded"
                             style="min-width:160px;border:2px solid <?php echo e($methodEnabled ? 'var(--accent)' : 'var(--border)'); ?>;background:<?php echo e($methodEnabled ? 'var(--accent-subtle, rgba(21,183,108,0.05))' : 'transparent'); ?>;">
                            <i class="bi <?php echo e($methodInfo['icon']); ?>" style="font-size:24px;color:<?php echo e($methodEnabled ? 'var(--accent)' : 'var(--text-muted)'); ?>;"></i>
                            <span style="font-size:13px;font-weight:600;"><?php echo e($methodInfo['label']); ?></span>
                            <span style="font-size:11px;font-weight:400;color:var(--text-muted);text-align:center;"><?php echo e($methodInfo['desc']); ?></span>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($methodEnabled): ?>
                                <span class="badge-success mt-1" style="font-size:11px;"><?php echo e(__('general.enabled')); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($enabled): ?>
                                    <button wire:click="confirmDisableMethod('<?php echo e($methodKey); ?>')"
                                            class="btn btn-sm btn-outline-danger mt-2 px-3">
                                        <i class="bi bi-shield-slash me-1"></i><?php echo e(__('general.disable')); ?>

                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php else: ?>
                                <button wire:click="startSetup('<?php echo e($methodKey); ?>')" class="btn btn-sm btn-accent mt-2 px-3">
                                    <i class="bi bi-plus-circle me-1"></i><?php echo e(__('general.enable')); ?>

                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->futureMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $future): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex flex-column align-items-center gap-1 px-4 py-3 rounded opacity-50"
                             style="min-width:160px;border:2px dashed var(--border);background:transparent;cursor:not-allowed;">
                            <i class="bi <?php echo e($future['icon']); ?>" style="font-size:24px;color:var(--text-muted);"></i>
                            <span style="font-size:13px;font-weight:600;color:var(--text-muted);"><?php echo e(__('general.phone_method')); ?></span>
                            <span style="font-size:11px;font-weight:400;color:var(--text-muted);text-align:center;"><?php echo e(__('general.coming_soon')); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($enabled && $showingRecoveryCodes && !empty($recoveryCodes)): ?>
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span style="font-size:14px;font-weight:600;"><?php echo e(__('general.recovery_codes')); ?></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-0"
                                @click="navigator.clipboard.writeText(`<?php echo e(collect($this->recoveryCodes)->implode("\n")); ?>`)"
                                title="<?php echo e(__('general.copy_all')); ?>">
                            <i class="bi bi-clipboard" style="font-size:12px;"></i>
                        </button>
                    </div>
                    <p class="text-muted mb-2" style="font-size:13px;"><?php echo e(__('messages.store_recovery_codes')); ?></p>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->recoveryCodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $recoveryCode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div style="font-family:monospace;font-size:13px;padding:8px 10px;background:var(--bg-subtle);border-radius:6px;border:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                                <span style="color:var(--text-muted);font-size:11px;min-width:18px;"><?php echo e($index + 1); ?>.</span>
                                <span><?php echo e($recoveryCode); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <button wire:click="regenerateRecoveryCodes" class="btn btn-sm btn-outline-secondary mt-2">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        <?php echo e(__('general.regenerate_recovery_codes')); ?>

                    </button>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($enabled): ?>
                <button wire:click="confirmDisable" class="btn btn-outline-danger w-100 mt-2">
                    <i class="bi bi-shield-slash me-2"></i>
                    <?php echo e(__('general.disable')); ?>

                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

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
    </div>
</div><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire/pages/auth/two-factor-setup.blade.php ENDPATH**/ ?>