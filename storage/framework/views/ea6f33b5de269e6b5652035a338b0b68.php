<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php
        $displayPrice = function (float $amount, ?string $currency = null) use ($userCurrency) {
            $cur = $currency ?: $userCurrency;
            return number_format($amount, 2) . ' ' . \App\Services\CurrencyHelper::symbol($cur);
        };
    ?>
     <?php $__env->slot('title', null, []); ?> <?php echo e($invoice->number); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e($invoice->number); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('settings.invoice_details')); ?> <?php $__env->endSlot(); ?>

    <div class="settings-card" id="invoice-print-area">
        <div class="d-flex justify-content-between align-items-start mb-4 no-print">
            <div>
                <h4 class="mb-1" style="font-weight:700"><?php echo e($invoice->number); ?></h4>
                <p class="text-muted mb-0"><?php echo e($invoice->created_at->format('F d, Y')); ?></p>
            </div>
            <?php
                $badge = match($invoice->status->value) {
                    'paid' => 'success',
                    'draft' => 'warning',
                    'overdue' => 'danger',
                    'cancelled' => 'secondary',
                    default => 'primary',
                };
            ?>
            <span class="badge bg-<?php echo e($badge); ?>" style="font-size:13px;padding:6px 16px"><?php echo e($invoice->status->label()); ?></span>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div style="background:var(--bg-subtle);border-radius:8px;padding:16px">
                    <div class="text-muted-sm mb-1" style="font-size:12px"><?php echo e(__('settings.invoice_plan')); ?></div>
                    <div style="font-weight:600"><?php echo e($invoice->subscription?->plan?->name ?? '—'); ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background:var(--bg-subtle);border-radius:8px;padding:16px">
                    <div class="text-muted-sm mb-1" style="font-size:12px"><?php echo e(__('settings.invoice_period')); ?></div>
                    <div style="font-weight:600">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoice->period_start && $invoice->period_end): ?>
                            <?php echo e($invoice->period_start->format('M Y')); ?> — <?php echo e($invoice->period_end->format('M Y')); ?>

                        <?php else: ?>
                            —
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background:var(--bg-subtle);border-radius:8px;padding:16px">
                    <div class="text-muted-sm mb-1" style="font-size:12px"><?php echo e(__('settings.payment_method')); ?></div>
                    <div style="font-weight:600"><?php echo e($invoice->subscription?->payment_method ?? '—'); ?></div>
                </div>
            </div>
        </div>

        <div style="border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1rem">
            <table style="width:100%;border-collapse:collapse;font-size:14px">
                <thead>
                    <tr style="background:var(--bg-subtle)">
                        <th style="padding:12px 16px;text-align:start;font-weight:600;font-size:13px"><?php echo e(__('settings.description')); ?></th>
                        <th style="padding:12px 16px;text-align:end;font-weight:600;font-size:13px"><?php echo e(__('settings.amount')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:10px 16px;border-top:1px solid var(--border)"><?php echo e(__('settings.subtotal')); ?> (<?php echo e($invoice->subscription?->plan?->name ?? ''); ?>)</td>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);text-align:end;font-weight:500"><?php echo e($displayPrice($invoice->subtotal, $invoice->currency)); ?></td>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $invoice->discount > 0): ?>
                    <tr>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);color:var(--danger)">
                            <?php echo e(__('settings.discount')); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoice->coupon): ?>
                                <span style="font-size:12px;color:var(--text-muted)">(<?php echo e($invoice->coupon->code); ?>)</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);text-align:end;font-weight:500;color:var(--danger)">-<?php echo e($displayPrice($invoice->discount, $invoice->currency)); ?></td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $invoice->proration_credit > 0): ?>
                    <tr>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);color:var(--info)">
                            <?php echo e(__('settings.proration_credit') ?? 'رصيد براتا'); ?>

                        </td>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);text-align:end;font-weight:500;color:var(--info)">-<?php echo e($displayPrice($invoice->proration_credit, $invoice->currency)); ?></td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $invoice->gateway_fee > 0): ?>
                    <tr>
                        <td style="padding:10px 16px;border-top:1px solid var(--border)"><?php echo e(__('settings.gateway_fee') ?? 'رسم بوابة دفع'); ?></td>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);text-align:end;font-weight:500"><?php echo e($displayPrice($invoice->gateway_fee, $invoice->currency)); ?></td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $invoice->tax_added > 0): ?>
                    <tr>
                        <td style="padding:10px 16px;border-top:1px solid var(--border)"><?php echo e(__('settings.tax')); ?></td>
                        <td style="padding:10px 16px;border-top:1px solid var(--border);text-align:end;font-weight:500"><?php echo e($displayPrice($invoice->tax_added, $invoice->currency)); ?></td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <tr style="background:var(--bg-subtle)">
                        <td style="padding:12px 16px;border-top:2px solid var(--border);font-weight:700"><?php echo e(__('settings.total_due')); ?></td>
                        <td style="padding:12px 16px;border-top:2px solid var(--border);text-align:end;font-weight:700;font-size:16px"><?php echo e($displayPrice($invoice->total, $invoice->currency)); ?></td>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $invoice->tax_disclosed > 0): ?>
                    <tr>
                        <td style="padding:8px 16px;border-top:1px dashed var(--border);font-size:12px;color:var(--text-muted)">
                            <i class="bi bi-info-circle"></i>
                            <?php echo e(__('settings.tax_disclosed_label') ?? 'ضريبة/زكاة إفصاح (غير مضافة للمبلغ)'); ?>

                        </td>
                        <td style="padding:8px 16px;border-top:1px dashed var(--border);text-align:end;font-size:12px;color:var(--text-muted)"><?php echo e($displayPrice($invoice->tax_disclosed, $invoice->currency)); ?></td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex gap-2 mt-4 no-print">
            <a href="<?php echo e(route('account.invoices.index')); ?>" class="btn btn-outline-secondary btn-custom">
                <i class="bi bi-arrow-left me-1"></i><?php echo e(__('general.back')); ?>

            </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoice->isPaid()): ?>
            <a href="<?php echo e(route('account.invoices.pdf', $invoice)); ?>" class="btn btn-accent btn-custom">
                <i class="bi bi-file-earmark-pdf me-1"></i><?php echo e(__('general.download_pdf') ?? 'PDF'); ?>

            </a>
            <button type="button" class="btn btn-accent btn-custom" onclick="window.print()">
                <i class="bi bi-printer me-1"></i><?php echo e(__('general.print') ?? 'طباعة'); ?>

            </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php $__env->startPush('styles'); ?>
    <style>
    @media print {
        body { background: #fff !important; }
        .no-print { display: none !important; }
        #invoice-print-area {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .settings-card {
            background: #fff !important;
            border: none !important;
            box-shadow: none !important;
        }
        table { page-break-inside: avoid; }
    }
    </style>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\laragon\www\Finance-Manager\resources\views\account\invoices-show.blade.php ENDPATH**/ ?>