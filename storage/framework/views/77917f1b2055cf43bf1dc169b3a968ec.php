<?php

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\CurrencyHelper;
use App\Services\OnboardingService;
use App\Services\Payments\GatewayManager;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

?>

<div>
<div class="auth-card animate-fade-in">
    <div class="auth-logo">
        <div class="logo-icon">FM</div>
        <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
        <span class="logo-sub"><?php echo e(__('onboarding.manual_payment_title')); ?></span>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pageError): ?>
        <div class="alert alert-danger py-2 small mb-3"><?php echo e($pageError); ?></div>
        <div class="auth-footer mt-3">
            <a href="<?php echo e(route('onboarding.plan')); ?>" wire:navigate><?php echo e(__('onboarding.back_to_plans')); ?></a>
        </div>

    <?php elseif($view === 'completed'): ?>
        <div class="text-center mb-4">
            <div class="mb-3" style="font-size:4rem"><i class="bi bi-check-circle-fill text-success"></i></div>
            <h5><?php echo e(__('onboarding.payment_success')); ?></h5>
            <p class="text-muted small"><?php echo e(__('onboarding.proof_approved_desc')); ?></p>
        </div>
        <?php echo $__env->renderWhen($payment, 'livewire.pages.onboarding.partials.payment-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>
        <div class="d-grid gap-2 mt-3">
            <button wire:click="proceed" class="btn btn-accent btn-custom">
                <i class="bi bi-arrow-right me-1"></i><?php echo e(__('onboarding.continue')); ?>

            </button>
        </div>

    <?php elseif($view === 'rejected'): ?>
        <?php $v = $payment->verification; ?>
        <div class="text-center mb-4">
            <div class="mb-3" style="font-size:3rem"><i class="bi bi-x-circle-fill text-danger"></i></div>
            <h5><?php echo e(__('onboarding.proof_rejected')); ?></h5>
            <p class="text-muted small"><?php echo e(__('onboarding.proof_rejected_desc')); ?></p>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($v): ?>
            <div class="info-section mb-3">
                <div class="info-section-header">
                    <i class="bi bi-receipt me-1"></i><?php echo e(__('onboarding.proof_details_title')); ?>

                </div>
                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('onboarding.verification_status')); ?></span>
                        <span class="info-value">
                            <span class="badge rounded-pill bg-danger text-white"><?php echo e(__('onboarding.status_rejected')); ?></span>
                        </span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($v->admin_notes): ?>
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('super-admin.reject_reason') ?? 'Admin Notes'); ?></span>
                        <span class="info-value" style="font-size:12px;max-width:180px;text-align:right"><?php echo e($v->admin_notes); ?></span>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php echo $__env->renderWhen($payment, 'livewire.pages.onboarding.partials.payment-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>
        <hr>
        <p class="small text-muted mb-2"><?php echo e(__('onboarding.retry_or_change_method')); ?></p>
        <div class="d-grid gap-2">
            <button wire:click="retry" class="btn btn-accent btn-custom">
                <i class="bi bi-arrow-repeat me-1"></i><?php echo e(__('onboarding.retry_payment')); ?>

            </button>
            <button wire:click="switchGateway" class="btn btn-outline-accent btn-custom">
                <i class="bi bi-arrow-left-right me-1"></i><?php echo e(__('onboarding.switch_gateway')); ?>

            </button>
        </div>

    <?php elseif($view === 'submitted'): ?>
        <?php $v = $payment->verification; ?>
        <div class="text-center mb-4">
            <i class="bi bi-clock-history text-warning" style="font-size:3rem;"></i>
            <h5 class="mt-3"><?php echo e(__('onboarding.proof_pending_review')); ?></h5>
            <p class="text-muted small"><?php echo e(__('onboarding.proof_pending_review_desc')); ?></p>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($v): ?>
            <div class="info-section mb-3">
                <div class="info-section-header">
                    <i class="bi bi-receipt me-1"></i>
                    <?php echo e(__('onboarding.proof_details_title')); ?>

                </div>
                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('onboarding.transaction_reference')); ?></span>
                        <span class="info-value" style="direction:ltr;font-family:monospace"><?php echo e($v->transaction_reference ?? '—'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('onboarding.verification_status')); ?></span>
                        <span class="info-value">
                            <span class="badge rounded-pill <?php echo e($this->statusBadgeClass($v->status->value)); ?>"><?php echo e($v->status->label()); ?></span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('onboarding.submitted_on')); ?></span>
                        <span class="info-value"><?php echo e($v->created_at->format('d M Y, H:i')); ?></span>
                    </div>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->receiptDataUrl): ?>
                <div class="info-section mb-3">
                    <div class="info-section-header">
                        <i class="bi bi-image me-1"></i>
                        <?php echo e(__('onboarding.receipt_preview')); ?>

                    </div>
                    <div class="text-center p-3">
                        <button type="button" class="btn btn-sm btn-outline-accent" @click="Livewire.dispatch('openReceiptModal')" style="border:1px solid var(--border);border-radius:8px;padding:6px 16px">
                            <i class="bi bi-eye me-1"></i><?php echo e(__('general.open_in_new_tab')); ?>

                        </button>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="text-muted small text-center mt-2">
            <i class="bi bi-arrow-repeat me-1"></i><?php echo e(__('onboarding.auto_checking')); ?>

        </div>
        <div wire:poll.5s="checkStatus" class="d-none"></div>

        <div class="d-grid gap-2 mt-3">
            <button type="button" wire:click="confirmCancel"
                class="btn btn-outline-danger btn-custom mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-x-circle me-1"></i><?php echo e(__('onboarding.cancel_change_method')); ?>

            </button>
        </div>
    <?php else: ?>
        <p class="text-muted small mb-3"><?php echo e(__('onboarding.manual_payment_instructions', ['method' => __('onboarding.method_' . $payment->method)])); ?></p>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($this->paymentDetails) > 0): ?>
            <div class="payment-details-box mb-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->paymentDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="payment-detail-row">
                        <span class="detail-label"><?php echo e($detail['label']); ?></span>
                        <span class="detail-value"><?php echo e($detail['value']); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->paymentInstructions): ?>
            <div class="alert alert-info py-2 small mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-info-circle me-1"></i><?php echo e($this->paymentInstructions); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errorMessage): ?>
            <div class="alert alert-danger py-2 small"><?php echo e($errorMessage); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <form wire:submit="submit">
            <div class="mb-3">
                <label class="form-label-custom"><?php echo e(__('onboarding.transaction_reference')); ?></label>
                <input type="text" wire:model="transactionReference" class="form-custom <?php $__errorArgs = ['transactionReference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    placeholder="<?php echo e(__('onboarding.transaction_reference_placeholder')); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['transactionReference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label-custom"><?php echo e(__('onboarding.upload_receipt')); ?></label>
                <div class="drop-zone <?php $__errorArgs = ['receipt'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> drop-zone-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                     x-data="{ dragging: false }"
                     x-on:dragover.prevent="dragging = true"
                     x-on:dragleave.prevent="dragging = false"
                     x-on:drop.prevent="dragging = false; $wire.upload('receipt', $event.dataTransfer.files[0])"
                     x-bind:class="{ 'drop-zone-active': dragging }"
                     wire:loading.class="drop-zone-loading" wire:target="receipt">
                    <div class="drop-zone-content" x-show="!$wire.receipt">
                        <i class="bi bi-cloud-upload" style="font-size:2rem;color:var(--text-muted,#888)"></i>
                        <p class="small text-muted mb-1"><?php echo e(__('onboarding.drag_drop_hint')); ?></p>
                        <p class="small text-muted"><?php echo e(__('onboarding.or')); ?></p>
                        <label class="btn btn-sm btn-outline-accent mt-1" style="cursor:pointer">
                            <?php echo e(__('onboarding.browse_files')); ?>

                            <input type="file" wire:model="receipt" accept=".jpg,.jpeg,.png,.pdf" hidden>
                        </label>
                    </div>
                    <div class="drop-zone-preview" x-show="$wire.receipt" style="display:none">
                        <div class="text-center p-3">
                            <i class="bi bi-file-check text-success" style="font-size:2rem"></i>
                            <p class="small mb-0 mt-2" x-text="$wire.receipt ? $wire.receipt.name : ''"></p>
                            <a href="#" class="small text-danger" x-on:click.prevent="$wire.receipt = null"><?php echo e(__('general.remove')); ?></a>
                        </div>
                    </div>
                    <div class="drop-zone-loading-indicator" wire:loading wire:target="receipt">
                        <div class="progress" style="height:4px;border-radius:4px;overflow:hidden;margin-top:8px">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" style="width:100%;height:4px"></div>
                        </div>
                        <p class="small text-muted mt-1 mb-0"><?php echo e(__('onboarding.uploading')); ?></p>
                    </div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['receipt'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div wire:loading.remove wire:target="receipt" class="mt-2">
                    <div x-data="{ preview: null }"
                         x-init="$watch('$wire.receipt', val => {
                             if (!val) { preview = null; return }
                             if (!val.type?.startsWith('image/')) return
                             const reader = new FileReader()
                             reader.onload = e => preview = e.target.result
                             reader.readAsDataURL(val)
                         })">
                        <template x-if="preview">
                            <div class="text-center mt-2">
                                <img :src="preview" class="receipt-preview-img"
                                     alt="<?php echo e(__('onboarding.receipt_preview')); ?>" style="max-height:200px">
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-accent btn-custom w-100" wire:loading.attr="disabled" wire:target="submit">
                <span wire:loading.remove wire:target="submit"><?php echo e(__('onboarding.submit_proof')); ?></span>
                <span wire:loading wire:target="submit"><?php echo e(__('onboarding.uploading')); ?></span>
            </button>
        </form>

        <div class="d-grid gap-2 mt-3">
            <button type="button" wire:click="confirmCancel"
                class="btn btn-outline-danger btn-custom mb-0 d-flex align-items-center gap-2 justify-center">
                <i class="bi bi-x-circle me-1"></i><?php echo e(__('onboarding.cancel_change_method')); ?>

            </button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($confirming): ?>
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
            <div style="background:var(--card-bg,#fff);border-radius:var(--radius-md,12px);max-width:400px;width:100%;padding:1.5rem;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
                <h5 style="font-size:16px;font-weight:600;margin-bottom:8px;"><?php echo e(__('general.confirm')); ?></h5>
                <p style="font-size:14px;color:var(--text-muted);margin-bottom:1.5rem;"><?php echo e(__('onboarding.cancel_confirm_desc')); ?></p>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button wire:click="cancelConfirmation" type="button" class="btn btn-outline-secondary btn-custom" style="font-size:13px;padding:7px 16px;">
                        <?php echo e(__('general.cancel')); ?>

                    </button>
                    <button wire:click="executeConfirmed" type="button" class="btn btn-danger btn-custom" style="font-size:13px;padding:7px 16px;">
                        <?php echo e(__('onboarding.cancel_change_method')); ?>

                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->receiptDataUrl): ?>
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true" wire:ignore>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background:var(--card-bg);border:1px solid var(--border)">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title"><?php echo e(__('onboarding.receipt_preview')); ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <img src="<?php echo e($this->receiptDataUrl); ?>"
                         alt="<?php echo e(__('onboarding.receipt_preview')); ?>"
                         style="max-width:100%;max-height:70vh;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,0.1)">
                </div>
            </div>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
    <?php $__env->startPush('scripts'); ?>
    <script>
        function initManualProof() {
            if (!window._manualReceiptListener) {
                Livewire.on('openReceiptModal', function () {
                    var modal = new bootstrap.Modal(document.getElementById('receiptModal'));
                    modal.show();
                });
                window._manualReceiptListener = true;
            }
        }
        initManualProof();
    </script>
    <?php $__env->stopPush(); ?><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire\pages\onboarding\manual-proof.blade.php ENDPATH**/ ?>