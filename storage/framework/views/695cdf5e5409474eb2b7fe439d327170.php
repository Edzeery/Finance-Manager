<?php

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Payment;
use App\Services\CurrencyHelper;
use App\Services\OnboardingService;
use App\Services\PaymentService;
use App\Services\Payments\GatewayManager;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

?>

<div>
    <div class="auth-card animate-fade-in">
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($view === 'waiting'): ?>
            <div class="text-center">
                <div class="auth-logo">
                    <div class="logo-icon">FM</div>
                    <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
                </div>
                <div class="my-4">
                    <div class="spinner-border text-accent my-4" role="spinner"></div>
                </div>
                <p><?php echo e(__('onboarding.processing_payment')); ?></p>
                <p class="small text-muted"><?php echo e(__('onboarding.payment_processing_hint')); ?></p>
            </div>

        
        <?php elseif($view === 'completed'): ?>
            <?php echo $__env->renderWhen($payment, 'livewire.pages.onboarding.partials.payment-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>
            <div class="text-center mt-3">
                <div class="text-success" style="font-size:4rem"><i class="bi bi-check-circle-fill"></i></div>
                <p class="fw-bold"><?php echo e(__('onboarding.payment_success')); ?></p>
                <div class="mt-2">
                    <button wire:click="proceed" class="btn btn-accent btn-custom">
                        <i class="bi bi-arrow-right me-1"></i><?php echo e(__('onboarding.continue')); ?>

                    </button>
                </div>
            </div>

        
        <?php elseif($view === 'failed'): ?>
            <div class="auth-logo">
                <div class="logo-icon">FM</div>
                <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
            </div>
            <div class="text-center mb-3">
                <div class="text-danger" style="font-size:3rem"><i class="bi bi-x-circle-fill"></i></div>
            </div>
            <div class="alert alert-danger py-2 small"><?php echo e($errorMessage ?? __('onboarding.payment_failed')); ?></div>
            <?php echo $__env->renderWhen($payment, 'livewire.pages.onboarding.partials.payment-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>
            <div class="d-grid gap-2 mt-3">
                <button wire:click="retry" class="btn btn-accent btn-custom">
                    <i class="bi bi-arrow-repeat me-1"></i><?php echo e(__('onboarding.retry_payment')); ?>

                </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment && OnboardingService::isManual($payment->method)): ?>
                <button wire:click="manualProof" class="btn btn-outline-accent btn-custom">
                    <i class="bi bi-upload me-1"></i><?php echo e(__('onboarding.upload_manual_proof')); ?>

                </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button wire:click="switchGateway" class="btn btn-outline-accent btn-custom">
                    <i class="bi bi-arrow-left-right me-1"></i><?php echo e(__('onboarding.switch_gateway')); ?>

                </button>
            </div>
            <?php echo $__env->make('livewire.pages.onboarding.partials.auth-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php elseif($view === 'canceled'): ?>
            <div class="auth-logo">
                <div class="logo-icon">FM</div>
                <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
            </div>
            <div class="text-center mb-3">
                <div class="text-warning" style="font-size:3rem"><i class="bi bi-x-lg"></i></div>
            </div>
            <div class="alert alert-warning py-2 small text-center"><?php echo e(__('onboarding.payment_cancelled_desc')); ?></div>

            
            <div class="info-section mb-3">
                <div class="info-section-header">
                    <i class="bi bi-clock-history me-1"></i>
                    <?php echo e(__('onboarding.timeline') ?? 'Timeline'); ?>

                </div>
                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('onboarding.timeline_initiated')); ?></span>
                        <span class="info-value"><?php echo e($payment->created_at->format('d M Y, H:i')); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('onboarding.timeline_cancelled')); ?></span>
                        <span class="info-value"><?php echo e(($payment->canceled_at ? \Carbon\Carbon::parse($payment->canceled_at)->format('d M Y, H:i') : now()->format('d M Y, H:i'))); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('onboarding.timeline_retry')); ?></span>
                        <span class="info-value text-accent fw-bold"><?php echo e(__('onboarding.timeline_retry_action') ?? __('onboarding.retry_payment')); ?></span>
                    </div>
                </div>
            </div>

            <?php echo $__env->renderWhen($payment, 'livewire.pages.onboarding.partials.payment-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>
            <div class="d-grid gap-2 mt-3">
                <button wire:click="retry" class="btn btn-accent btn-custom">
                    <i class="bi bi-arrow-repeat me-1"></i><?php echo e(__('onboarding.retry_payment')); ?>

                </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment && OnboardingService::isManual($payment->method)): ?>
                <button wire:click="manualProof" class="btn btn-outline-accent btn-custom">
                    <i class="bi bi-upload me-1"></i><?php echo e(__('onboarding.upload_manual_proof')); ?>

                </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button wire:click="switchGateway" class="btn btn-outline-accent btn-custom">
                    <i class="bi bi-arrow-left-right me-1"></i><?php echo e(__('onboarding.switch_gateway')); ?>

                </button>
            </div>
            <?php echo $__env->make('livewire.pages.onboarding.partials.auth-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php elseif($view === 'pending_manual'): ?>
            <?php echo $__env->renderWhen($payment, 'livewire.pages.onboarding.partials.payment-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>
            <?php echo $__env->make('livewire.pages.onboarding.partials.auth-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php elseif($view === 'error'): ?>
            <div class="auth-logo">
                <div class="logo-icon">FM</div>
                <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
            </div>
            <div class="text-center mb-3">
                <div class="text-muted" style="font-size:3rem"><i class="bi bi-exclamation-triangle"></i></div>
            </div>
            <div class="alert alert-danger py-2 small"><?php echo e($errorMessage ?? __('onboarding.no_pending_payment')); ?></div>
            <?php echo $__env->renderWhen($payment, 'livewire.pages.onboarding.partials.payment-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>
            <div class="d-grid gap-2 mt-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment): ?>
                <button wire:click="switchGateway" class="btn btn-outline-accent btn-custom">
                    <i class="bi bi-arrow-left-right me-1"></i><?php echo e(__('onboarding.switch_gateway')); ?>

                </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php echo $__env->make('livewire.pages.onboarding.partials.auth-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire/pages/onboarding/payment-retry.blade.php ENDPATH**/ ?>