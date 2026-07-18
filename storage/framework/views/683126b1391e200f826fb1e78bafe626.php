<?php if (isset($component)) { $__componentOriginal11b520df80702cb1ab8718e178b6ffa6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal11b520df80702cb1ab8718e178b6ffa6 = $attributes; } ?>
<?php $component = App\View\Components\SuperAdminLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('super-admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SuperAdminLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('super-admin.invoices')); ?> <?php echo e($invoice->number); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('super-admin.invoice')); ?> <?php echo e($invoice->number); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('super-admin.invoices_desc')); ?> <?php $__env->endSlot(); ?>

    <div class="detail-grid">
        <div class="detail-main">
            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-file-text"></i><?php echo e(__('super-admin.invoice_details')); ?></h5>
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
                <div class="section-card-body">
                    <table class="info-table">
                        <tr>
                            <td class="info-label"><?php echo e(__('super-admin.invoice_workspace')); ?></td>
                            <td class="info-value"><?php echo e($invoice->workspace?->name ?? __('general.unknown')); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label"><?php echo e(__('super-admin.invoice_plan')); ?></td>
                            <td class="info-value"><?php echo e($invoice->subscription?->plan?->name ?? '—'); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label"><?php echo e(__('settings.invoice_period')); ?></td>
                            <td class="info-value"><?php echo e($invoice->period_start?->format('Y/m/d') ?? '—'); ?> &rarr; <?php echo e($invoice->period_end?->format('Y/m/d') ?? '—'); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label"><?php echo e(__('super-admin.payer')); ?></td>
                            <td class="info-value"><?php echo e($invoice->user?->name ?? '—'); ?> (<?php echo e($invoice->user?->email ?? '—'); ?>)</td>
                        </tr>
                        <tr>
                            <td class="info-label"><?php echo e(__('general.date')); ?></td>
                            <td class="info-value"><?php echo e($invoice->created_at->format('Y/m/d H:i')); ?></td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoice->paid_at): ?>
                        <tr>
                            <td class="info-label"><?php echo e(__('general.paid')); ?></td>
                            <td class="info-value"><?php echo e($invoice->paid_at->format('Y/m/d H:i')); ?></td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoice->due_at): ?>
                        <tr>
                            <td class="info-label"><?php echo e(__('settings.invoice_due')); ?></td>
                            <td class="info-value"><?php echo e($invoice->due_at->format('Y/m/d')); ?></td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </table>

                    <hr style="margin:20px 0;border-color:var(--border-light);border-style:solid">

                    <table class="info-table">
                        <tr>
                            <td class="info-label" style="color:var(--text-muted)"><?php echo e(__('settings.invoice_subscription')); ?></td>
                            <td class="info-value text-end"><?php echo e(number_format($invoice->subtotal, 2)); ?> <?php echo e($invoice->currency); ?></td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoice->discount > 0): ?>
                        <tr>
                            <td class="info-label" style="color:var(--text-muted)"><?php echo e(__('settings.invoice_discount')); ?></td>
                            <td class="info-value text-end" style="color:var(--danger)">-<?php echo e(number_format($invoice->discount, 2)); ?> <?php echo e($invoice->currency); ?></td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoice->proration_credit > 0): ?>
                        <tr>
                            <td class="info-label" style="color:var(--info)"><?php echo e(__('super-admin.proration_credit') ?? 'براتا'); ?></td>
                            <td class="info-value text-end" style="color:var(--info)">-<?php echo e(number_format($invoice->proration_credit, 2)); ?> <?php echo e($invoice->currency); ?></td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoice->gateway_fee > 0): ?>
                        <tr>
                            <td class="info-label" style="color:var(--text-muted)"><?php echo e(__('super-admin.gateway_fee') ?? 'رسم بوابة'); ?></td>
                            <td class="info-value text-end"><?php echo e(number_format($invoice->gateway_fee, 2)); ?> <?php echo e($invoice->currency); ?></td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoice->tax_added > 0): ?>
                        <tr>
                            <td class="info-label" style="color:var(--text-muted)"><?php echo e(__('settings.invoice_tax')); ?></td>
                            <td class="info-value text-end"><?php echo e(number_format($invoice->tax_added, 2)); ?> <?php echo e($invoice->currency); ?></td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoice->tax_disclosed > 0): ?>
                        <tr>
                            <td class="info-label" style="font-size:11px;color:var(--text-muted);font-style:italic">
                                <i class="bi bi-info-circle"></i> <?php echo e(__('super-admin.tax_disclosed') ?? 'ضريبة إفصاح (غير مضافة)'); ?>

                            </td>
                            <td class="info-value text-end" style="font-size:12px;color:var(--text-muted)"><?php echo e(number_format($invoice->tax_disclosed, 2)); ?> <?php echo e($invoice->currency); ?></td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <tr style="border-top:1px solid var(--border-light)">
                            <td class="info-label" style="font-weight:600;color:var(--text)"><?php echo e(__('settings.invoice_total')); ?></td>
                            <td class="info-value text-end" style="font-weight:700;font-size:18px;color:var(--text)"><?php echo e(number_format($invoice->total, 2)); ?> <?php echo e($invoice->currency); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="detail-sidebar">
            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-info-circle"></i><?php echo e(__('general.details')); ?></h5>
                </div>
                <div class="section-card-body">
                    <table class="info-table">
                        <tr>
                            <td class="info-label"><?php echo e(__('settings.currency')); ?></td>
                            <td class="info-value"><?php echo e($invoice->currency); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label"><?php echo e(__('settings.invoice_period')); ?></td>
                            <td class="info-value"><?php echo e($invoice->billing_period ?? '—'); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label"><?php echo e(__('settings.invoice_auto')); ?></td>
                            <td class="info-value"><?php echo e($invoice->subscription?->auto_renew ? __('general.yes') : __('general.no')); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="<?php echo e(route('super.admin.invoices.index')); ?>" class="btn" style="padding:8px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text);font-weight:500;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
            <i class="bi bi-arrow-left"></i><?php echo e(__('general.back')); ?>

        </a>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal11b520df80702cb1ab8718e178b6ffa6)): ?>
<?php $attributes = $__attributesOriginal11b520df80702cb1ab8718e178b6ffa6; ?>
<?php unset($__attributesOriginal11b520df80702cb1ab8718e178b6ffa6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal11b520df80702cb1ab8718e178b6ffa6)): ?>
<?php $component = $__componentOriginal11b520df80702cb1ab8718e178b6ffa6; ?>
<?php unset($__componentOriginal11b520df80702cb1ab8718e178b6ffa6); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\super-admin\invoice-show.blade.php ENDPATH**/ ?>