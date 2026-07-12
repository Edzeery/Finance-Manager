<?php

use App\Enums\PaymentStatus;
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
        <div class="auth-logo">
            <div class="logo-icon">FM</div>
            <span class="logo-text"><?php echo e(__('general.app_name')); ?></span>
            <span class="logo-sub"><?php echo e(__('onboarding.resume_payment')); ?></span>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errorMessage): ?>
            <div class="alert alert-danger py-2 small"><?php echo e($errorMessage); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment): ?>
            <div class="text-center mb-3">
                <div class="text-warning" style="font-size:3rem"><i class="bi bi-clock-history"></i></div>
                <p class="mt-2"><?php echo e(__('onboarding.payment_pending_desc')); ?></p>
            </div>

            <div class="info-section mb-3">
                <div class="info-section-header">
                    <i class="bi bi-credit-card-2-front me-1"></i>
                    <?php echo e(__('onboarding.payment_info')); ?>

                </div>
                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('onboarding.payment_method_label')); ?></span>
                        <span class="info-value"><?php echo e($this->methodLabel($payment->method)); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('onboarding.plan_name')); ?></span>
                        <span class="info-value"><?php echo e($plan['name'] ?? '-'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('onboarding.price')); ?></span>
                        <span class="info-value"><?php echo e($this->formatAmount((float) ($payment->original_amount ?: $payment->amount))); ?></span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->discount_amount > 0): ?>
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('onboarding.coupon_discount')); ?></span>
                        <span class="info-value text-success">-<?php echo e($this->formatAmount((float) $payment->discount_amount)); ?></span>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="info-row">
                        <span class="info-label"><?php echo e(__('onboarding.payment_date')); ?></span>
                        <span class="info-value"><?php echo e($payment->created_at->format('d M Y, H:i')); ?></span>
                    </div>
                </div>
            </div>

            <?php
                $hasFees = $payment->gateway_fee > 0 || $payment->tax_added > 0 || $payment->tax_disclosed > 0;
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasFees): ?>
            <div class="price-breakdown mb-3">
                <div class="price-row original">
                    <span><?php echo e(__('onboarding.plan_price')); ?></span>
                    <span><?php echo e($this->formatAmount((float) ($payment->original_amount ?: $payment->amount))); ?></span>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->discount_amount > 0): ?>
                <div class="price-row discount">
                    <span><?php echo e(__('onboarding.coupon_discount')); ?></span>
                    <span>-<?php echo e($this->formatAmount((float) $payment->discount_amount)); ?></span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->gateway_fee > 0): ?>
                <div class="price-row fee">
                    <span><?php echo e(__('onboarding.gateway_fee')); ?></span>
                    <span>+<?php echo e($this->formatAmount((float) $payment->gateway_fee)); ?></span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->tax_added > 0): ?>
                <div class="price-row fee">
                    <span><?php echo e(__('onboarding.tax_added')); ?></span>
                    <span>+<?php echo e($this->formatAmount((float) $payment->tax_added)); ?></span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->tax_disclosed > 0): ?>
                <div class="price-row">
                    <span><?php echo e(__('onboarding.tax_disclosed')); ?></span>
                    <span><?php echo e($this->formatAmount((float) $payment->tax_disclosed)); ?></span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="price-divider"></div>
                <div class="price-row total">
                    <span><?php echo e(__('onboarding.total')); ?></span>
                    <span><?php echo e($this->formatAmount((float) $payment->amount)); ?></span>
                </div>
            </div>
            <?php else: ?>
            <div class="info-row mb-3">
                <span class="info-label fw-bold"><?php echo e(__('onboarding.total')); ?></span>
                <span class="info-value fw-bold"><?php echo e($this->formatAmount((float) $payment->amount)); ?></span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="d-grid gap-2 mt-3">
                <button wire:click="continueWithGateway" class="btn btn-accent btn-custom">
                    <i class="bi bi-arrow-right me-1"></i><?php echo e(__('onboarding.continue_payment')); ?>

                </button>
                <button wire:click="switchGateway" class="btn btn-outline-accent btn-custom">
                    <i class="bi bi-arrow-left-right me-1"></i><?php echo e(__('onboarding.switch_gateway')); ?>

                </button>
                <button wire:click="cancelPayment" class="btn btn-outline-secondary btn-custom">
                    <i class="bi bi-x-lg me-1"></i><?php echo e(__('onboarding.cancel_payment')); ?>

                </button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php echo $__env->make('livewire.pages.onboarding.partials.auth-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\livewire\pages\onboarding\payment-resume.blade.php ENDPATH**/ ?>