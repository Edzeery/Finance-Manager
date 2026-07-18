<!DOCTYPE html>
<html dir="auto">
<head>
    <meta charset="utf-8">
    <title><?php echo e($invoice->number); ?></title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; padding: 20px; }
        h2 { margin-bottom: 4px; }
        .text-muted { color: #888; font-size: 11px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 4px; font-size: 11px; }
        .bg-success { background: #d4edda; color: #155724; }
        .bg-warning { background: #fff3cd; color: #856404; }
        .bg-danger { background: #f8d7da; color: #721c24; }
        .bg-secondary { background: #e2e3e5; color: #383d41; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f5f5f5; font-weight: 600; font-size: 11px; }
        .text-end { text-align: right; }
        .fw-700 { font-weight: 700; }
        .bg-subtle { background: #f9f9f9; }
        .text-danger { color: #dc3545; }
        .text-info { color: #17a2b8; }
        .border-dashed { border-top: 1px dashed #ccc; }
        .info-card { display: inline-block; width: 30%; vertical-align: top; margin-bottom: 12px; }
        .info-label { font-size: 10px; color: #888; }
        .info-value { font-weight: 600; font-size: 13px; }
    </style>
</head>
<body>
    <div style="text-align:center;margin-bottom:20px">
        <h2><?php echo e(config('app.name')); ?></h2>
        <p class="text-muted"><?php echo e($invoice->number); ?></p>
        <p class="text-muted"><?php echo e($invoice->created_at->format('F d, Y')); ?></p>
        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'invoice','status' => $invoice->status->value,'set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'invoice','status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoice->status->value),'set' => 'bi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
    </div>

    <div style="margin-bottom:16px">
        <div class="info-card">
            <div class="info-label"><?php echo e(__('settings.invoice_plan')); ?></div>
            <div class="info-value"><?php echo e($invoice->subscription?->plan?->name ?? '—'); ?></div>
        </div>
        <div class="info-card">
            <div class="info-label"><?php echo e(__('settings.invoice_period')); ?></div>
            <div class="info-value">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoice->period_start && $invoice->period_end): ?>
                    <?php echo e($invoice->period_start->format('M Y')); ?> — <?php echo e($invoice->period_end->format('M Y')); ?>

                <?php else: ?>
                    —
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <div class="info-card">
            <div class="info-label"><?php echo e(__('settings.payment_method')); ?></div>
            <div class="info-value"><?php echo e($invoice->subscription?->payment_method ?? '—'); ?></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th><?php echo e(__('settings.description')); ?></th>
                <th class="text-end"><?php echo e(__('settings.amount')); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo e(__('settings.subtotal')); ?> (<?php echo e($invoice->subscription?->plan?->name ?? ''); ?>)</td>
                <td class="text-end fw-500"><?php echo e($displayPrice($invoice->subtotal, $invoice->currency)); ?></td>
            </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $invoice->discount > 0): ?>
            <tr>
                <td style="color:#dc3545">
                    <?php echo e(__('settings.discount')); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoice->coupon): ?>
                        <span style="font-size:10px;color:#888">(<?php echo e($invoice->coupon->code); ?>)</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </td>
                <td class="text-end fw-500" style="color:#dc3545">-<?php echo e($displayPrice($invoice->discount, $invoice->currency)); ?></td>
            </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $invoice->proration_credit > 0): ?>
            <tr>
                <td style="color:#17a2b8"><?php echo e(__('settings.proration_credit') ?? 'رصيد براتا'); ?></td>
                <td class="text-end fw-500" style="color:#17a2b8">-<?php echo e($displayPrice($invoice->proration_credit, $invoice->currency)); ?></td>
            </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $invoice->gateway_fee > 0): ?>
            <tr>
                <td><?php echo e(__('settings.gateway_fee') ?? 'رسم بوابة دفع'); ?></td>
                <td class="text-end fw-500"><?php echo e($displayPrice($invoice->gateway_fee, $invoice->currency)); ?></td>
            </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $invoice->tax_added > 0): ?>
            <tr>
                <td><?php echo e(__('settings.tax')); ?></td>
                <td class="text-end fw-500"><?php echo e($displayPrice($invoice->tax_added, $invoice->currency)); ?></td>
            </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <tr style="background:#f9f9f9">
                <td style="padding:10px 12px;border-top:2px solid #333;font-weight:700"><?php echo e(__('settings.total_due')); ?></td>
                <td style="padding:10px 12px;border-top:2px solid #333;text-align:right;font-weight:700;font-size:14px"><?php echo e($displayPrice($invoice->total, $invoice->currency)); ?></td>
            </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $invoice->tax_disclosed > 0): ?>
            <tr>
                <td style="padding:6px 12px;border-top:1px dashed #ccc;font-size:10px;color:#888">
                    <?php echo e(__('settings.tax_disclosed_label') ?? 'ضريبة/زكاة إفصاح (غير مضافة للمبلغ)'); ?>

                </td>
                <td style="padding:6px 12px;border-top:1px dashed #ccc;text-align:right;font-size:10px;color:#888"><?php echo e($displayPrice($invoice->tax_disclosed, $invoice->currency)); ?></td>
            </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>

    <p style="text-align:center;color:#888;font-size:10px;margin-top:24px">
        <?php echo e(config('app.name')); ?> — <?php echo e($invoice->created_at->format('Y')); ?>

    </p>
</body>
</html>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\account\invoices-pdf.blade.php ENDPATH**/ ?>