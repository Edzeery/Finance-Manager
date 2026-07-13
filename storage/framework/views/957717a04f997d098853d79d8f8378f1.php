<div class="info-section mb-3">
    <div class="info-section-header">
        <i class="bi bi-credit-card-2-front me-1"></i>
        <?php echo e(__('onboarding.payment_info')); ?>

    </div>
    <div class="info-grid">
        <div class="info-row">
            <span class="info-label"><?php echo e(__('onboarding.payment_status')); ?></span>
            <span class="info-value">
                <span class="badge rounded-pill <?php echo e($this->statusBadgeClass($payment->status->value)); ?>"><?php echo e($payment->status->label()); ?></span>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label"><?php echo e(__('onboarding.payment_method_label')); ?></span>
            <span class="info-value"><?php echo e($this->methodLabel($payment->method)); ?></span>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->payment_method_type): ?>
        <div class="info-row">
            <span class="info-label"><?php echo e(__('onboarding.payment_method_label')); ?> Type</span>
            <span class="info-value text-uppercase"><?php echo e($payment->payment_method_type); ?></span>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="info-row">
            <span class="info-label"><?php echo e(__('onboarding.payment_amount')); ?></span>
            <span class="info-value fw-bold"><?php echo e($this->formatAmount((float) $payment->amount)); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?php echo e(__('onboarding.payment_date')); ?></span>
            <span class="info-value"><?php echo e($payment->created_at->format('d M Y, H:i')); ?></span>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->transaction_id): ?>
        <div class="info-row">
            <span class="info-label">Transaction ID</span>
            <span class="info-value text-muted small text-truncate" style="max-width:200px;direction:ltr;"><?php echo e($payment->transaction_id); ?></span>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($plan)): ?>
<div class="info-section mb-3">
    <div class="info-section-header">
        <i class="bi bi-box-seam me-1"></i>
        <?php echo e(__('onboarding.subscription_info')); ?>

    </div>
    <div class="info-grid">
        <div class="info-row">
            <span class="info-label"><?php echo e(__('onboarding.plan_name')); ?></span>
            <span class="info-value"><?php echo e($plan['name'] ?? '-'); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?php echo e(__('onboarding.price')); ?></span>
            <span class="info-value">
                <?php $price = $payment->original_amount ?? $payment->amount; ?>
                <?php echo e($this->formatAmount((float) $price)); ?>

            </span>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->discount_amount > 0): ?>
        <div class="info-row">
            <span class="info-label"><?php echo e(__('onboarding.coupon_discount')); ?></span>
            <span class="info-value text-success">-<?php echo e($this->formatAmount((float) $payment->discount_amount)); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label fw-bold"><?php echo e(__('onboarding.total')); ?></span>
            <span class="info-value fw-bold"><?php echo e($this->formatAmount((float) $payment->amount)); ?></span>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php $_hasFees = $payment && ($payment->gateway_fee > 0 || $payment->tax_added > 0 || $payment->tax_disclosed > 0); ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($_hasFees): ?>
<div class="info-section mb-3">
    <div class="info-section-header">
        <i class="bi bi-receipt me-1"></i>
        <?php echo e(__('onboarding.fee_breakdown')); ?>

    </div>
    <div class="info-grid">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->gateway_fee > 0): ?>
        <div class="info-row">
            <span class="info-label"><?php echo e(__('onboarding.gateway_fee')); ?></span>
            <span class="info-value">+<?php echo e($this->formatAmount((float) $payment->gateway_fee)); ?></span>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->tax_added > 0): ?>
        <div class="info-row">
            <span class="info-label"><?php echo e(__('onboarding.tax_added')); ?></span>
            <span class="info-value">+<?php echo e($this->formatAmount((float) $payment->tax_added)); ?></span>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->tax_disclosed > 0): ?>
        <div class="info-row">
            <span class="info-label"><?php echo e(__('onboarding.tax_disclosed')); ?></span>
            <span class="info-value"><?php echo e($this->formatAmount((float) $payment->tax_disclosed)); ?></span>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="info-row">
            <span class="info-label fw-bold"><?php echo e(__('onboarding.total')); ?></span>
            <span class="info-value fw-bold"><?php echo e($this->formatAmount((float) $payment->amount)); ?></span>
        </div>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($invoice)): ?>
<div class="info-section mb-3">
    <div class="info-section-header">
        <i class="bi bi-receipt me-1"></i>
        <?php echo e(__('onboarding.invoice_info')); ?>

    </div>
    <div class="info-grid">
        <div class="info-row">
            <span class="info-label"><?php echo e(__('onboarding.invoice_number')); ?></span>
            <span class="info-value"><?php echo e($invoice['number'] ?? '-'); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?php echo e(__('onboarding.invoice_status')); ?></span>
            <span class="info-value"><span class="badge rounded-pill <?php echo e($this->statusBadgeClass($invoice['status'] ?? 'draft')); ?>"><?php echo e(__('general.' . ($invoice['status'] ?? 'draft'))); ?></span></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?php echo e(__('onboarding.invoice_subtotal')); ?></span>
            <span class="info-value"><?php echo e($this->formatAmount((float) ($invoice['subtotal'] ?? 0))); ?></span>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($invoice['tax']) && $invoice['tax'] > 0): ?>
        <div class="info-row">
            <span class="info-label"><?php echo e(__('onboarding.invoice_tax')); ?></span>
            <span class="info-value"><?php echo e($this->formatAmount((float) $invoice['tax'])); ?></span>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($invoice['discount']) && $invoice['discount'] > 0): ?>
        <div class="info-row">
            <span class="info-label"><?php echo e(__('onboarding.invoice_discount')); ?></span>
            <span class="info-value text-success">-<?php echo e($this->formatAmount((float) $invoice['discount'])); ?></span>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="info-row">
            <span class="info-label fw-bold"><?php echo e(__('onboarding.invoice_total')); ?></span>
            <span class="info-value fw-bold"><?php echo e($this->formatAmount((float) ($invoice['total'] ?? 0))); ?></span>
        </div>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/livewire/pages/onboarding/partials/payment-details.blade.php ENDPATH**/ ?>