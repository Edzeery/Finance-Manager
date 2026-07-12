
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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('super-admin.payments')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('super-admin.payments')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('super-admin.payments_desc')); ?> <?php $__env->endSlot(); ?>

    <?php
        $methodOptions = [];
        foreach ($gatewayKeys as $m) {
            $methodOptions[$m] = __("super-admin.{$m}");
        }
        $statusOptions = collect(\App\Enums\PaymentStatus::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
        $canVerify = auth()->user()->hasPermission('payment.verify');
        $canRefund = auth()->user()->hasPermission('payment.refund');
        $canViewRaw = auth()->user()->hasPermission('payment.view_raw');
    ?>

    <div class="data-grid">
        <div class="data-grid-toolbar">
            <div class="data-grid-toolbar-left">
                <form method="GET" action="<?php echo e(route('super.admin.payments.index')); ?>" class="d-flex flex-wrap align-items-center gap-2">
                    <?php if (isset($component)) { $__componentOriginal33e4867731ced0462908f8cc78d5ea1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-filter','data' => ['name' => 'search','placeholder' => ''.e(__('general.search')).'...','value' => ''.e(request('search')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','placeholder' => ''.e(__('general.search')).'...','value' => ''.e(request('search')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal33e4867731ced0462908f8cc78d5ea1b)): ?>
<?php $attributes = $__attributesOriginal33e4867731ced0462908f8cc78d5ea1b; ?>
<?php unset($__attributesOriginal33e4867731ced0462908f8cc78d5ea1b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal33e4867731ced0462908f8cc78d5ea1b)): ?>
<?php $component = $__componentOriginal33e4867731ced0462908f8cc78d5ea1b; ?>
<?php unset($__componentOriginal33e4867731ced0462908f8cc78d5ea1b); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-filter','data' => ['name' => 'status','options' => $statusOptions,'placeholder' => ''.e(__('general.all_status')).'','minWidth' => '120px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'status','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statusOptions),'placeholder' => ''.e(__('general.all_status')).'','min-width' => '120px']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c)): ?>
<?php $attributes = $__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c; ?>
<?php unset($__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c)): ?>
<?php $component = $__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c; ?>
<?php unset($__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-filter','data' => ['name' => 'refunded','options' => ['yes' => __('super-admin.refunded'), 'no' => __('general.not_refunded')],'placeholder' => ''.e(__('general.all_refunded')).'','minWidth' => '120px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'refunded','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['yes' => __('super-admin.refunded'), 'no' => __('general.not_refunded')]),'placeholder' => ''.e(__('general.all_refunded')).'','min-width' => '120px']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c)): ?>
<?php $attributes = $__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c; ?>
<?php unset($__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c)): ?>
<?php $component = $__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c; ?>
<?php unset($__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-filter','data' => ['name' => 'method','options' => $methodOptions,'placeholder' => ''.e(__('general.all_methods')).'','minWidth' => '120px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'method','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($methodOptions),'placeholder' => ''.e(__('general.all_methods')).'','min-width' => '120px']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c)): ?>
<?php $attributes = $__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c; ?>
<?php unset($__attributesOriginald85bdb4a50efcfe767a3055ac2ae2b9c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c)): ?>
<?php $component = $__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c; ?>
<?php unset($__componentOriginald85bdb4a50efcfe767a3055ac2ae2b9c); ?>
<?php endif; ?>
                    <input type="date" name="date_from" class="form-control grid-filter-sm"
                        style="width:auto;min-width:130px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)"
                        value="<?php echo e(request('date_from')); ?>">
                    <input type="date" name="date_to" class="form-control grid-filter-sm"
                        style="width:auto;min-width:130px;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text)"
                        value="<?php echo e(request('date_to')); ?>">
                    <button type="submit" class="btn" style="padding:7px 14px;font-size:13px;border-radius:var(--radius-sm);background:var(--accent);color:#0F172A;font-weight:600;border:none;cursor:pointer"><?php echo e(__('general.filter')); ?></button>
                    <?php if (isset($component)) { $__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.clear-filters','data' => ['filters' => ['search','status','refunded','method','date_from','date_to'],'route' => route('super.admin.payments.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('clear-filters'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['filters' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['search','status','refunded','method','date_from','date_to']),'route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('super.admin.payments.index'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113)): ?>
<?php $attributes = $__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113; ?>
<?php unset($__attributesOriginal3713bcf4ead06ff5169b19e8fd8f7113); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113)): ?>
<?php $component = $__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113; ?>
<?php unset($__componentOriginal3713bcf4ead06ff5169b19e8fd8f7113); ?>
<?php endif; ?>
                </form>
            </div>
            <div class="data-grid-toolbar-right">
                <?php if (isset($component)) { $__componentOriginal350cc130478c4b4aced77f6fd760100d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal350cc130478c4b4aced77f6fd760100d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.per-page','data' => ['current' => (int) request('per_page', 15),'route' => route('super.admin.payments.index'),'preserve' => ['search','status','refunded','method','date_from','date_to'],'options' => [10, 15, 25, 50]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('per-page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((int) request('per_page', 15)),'route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('super.admin.payments.index')),'preserve' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['search','status','refunded','method','date_from','date_to']),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([10, 15, 25, 50])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal350cc130478c4b4aced77f6fd760100d)): ?>
<?php $attributes = $__attributesOriginal350cc130478c4b4aced77f6fd760100d; ?>
<?php unset($__attributesOriginal350cc130478c4b4aced77f6fd760100d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal350cc130478c4b4aced77f6fd760100d)): ?>
<?php $component = $__componentOriginal350cc130478c4b4aced77f6fd760100d; ?>
<?php unset($__componentOriginal350cc130478c4b4aced77f6fd760100d); ?>
<?php endif; ?>
            </div>
        </div>

        <div class="data-grid-body">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payments->count()): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th><?php echo e(__('super-admin.workspace')); ?></th>
                            <th><?php echo e(__('super-admin.payer')); ?></th>
                            <th><?php echo e(__('settings.plan')); ?></th>
                            <th><?php echo e(__('general.amount')); ?></th>
                            <th><?php echo e(__('super-admin.method')); ?></th>
                            <th><?php echo e(__('super-admin.reference')); ?></th>
                            <th><?php echo e(__('super-admin.payment_id')); ?></th>
                            <th><?php echo e(__('super-admin.verification')); ?></th>
                            <th><?php echo e(__('general.status')); ?></th>
                            <th><?php echo e(__('general.date')); ?></th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canVerify || $canRefund || $canViewRaw): ?>
                                <th class="col-actions"></th>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $isWebhook = in_array($payment->method, $webhookMethods);
                                $hasFeeOrTax = ($payment->gateway_fee ?? 0) > 0 || ($payment->tax_added ?? 0) > 0 || ($payment->discount_amount ?? 0) > 0;
                            ?>
                            <tr>
                                <td><?php echo e($payment->workspace?->name ?? '—'); ?></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->user): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width:26px;height:26px;border-radius:50%;background:var(--accent);color:#0F172A;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0">
                                                <?php echo e(strtoupper(substr($payment->user->name, 0, 1))); ?>

                                            </div>
                                            <span style="font-size:13px"><?php echo e($payment->user->name); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="cell-muted">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->subscription?->plan): ?>
                                        <span class="badge" style="font-size:11px;background:var(--bg-subtle);color:var(--text);padding:2px 10px;border-radius:6px"><?php echo e($payment->subscription->plan->name); ?></span>
                                    <?php else: ?>
                                        <span class="cell-muted">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo e(number_format($payment->amount, 2)); ?></strong>
                                    <span style="font-size:12px;color:var(--text-muted)"><?php echo e($payment->currency); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasFeeOrTax): ?>
                                        <i class="bi bi-info-circle" style="font-size:12px;color:var(--text-muted);cursor:help"
                                            title="<?php echo e(__('super-admin.original')); ?>: <?php echo e(number_format($payment->original_amount ?? $payment->amount, 2)); ?> <?php echo e($payment->currency); ?>

<?php echo e(__('super-admin.discount_amount')); ?>: -<?php echo e(number_format($payment->discount_amount ?? 0, 2)); ?>

<?php echo e(__('super-admin.gateway_fee')); ?>: <?php echo e(number_format($payment->gateway_fee ?? 0, 2)); ?>

<?php echo e(__('super-admin.tax_added')); ?>: <?php echo e(number_format($payment->tax_added ?? 0, 2)); ?> + <?php echo e(__('super-admin.tax_disclosed')); ?>: <?php echo e(number_format($payment->tax_disclosed ?? 0, 2)); ?>"></i>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-size:12px;color:var(--text-secondary);text-transform:capitalize"><?php echo e(__("super-admin.{$payment->method}")); ?></div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->payment_method_type): ?>
                                        <div style="font-size:11px;color:var(--text-muted)"><?php echo e($payment->payment_method_type); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->reference): ?>
                                        <span style="font-size:12px;direction:ltr;display:inline-block;font-family:monospace"><?php echo e($payment->reference); ?></span>
                                    <?php else: ?>
                                        <span class="cell-muted">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->uuid): ?>
                                        <code style="font-size:11px;direction:ltr;display:inline-block;font-family:monospace"><?php echo e($payment->uuid); ?></code>
                                    <?php else: ?>
                                        <span class="cell-muted">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php $v = $payment->verification; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($v): ?>
                                        <div class="d-flex align-items-center gap-1">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($v->receipt_path): ?>
                                                <a href="#"
                                                    @click="event.preventDefault();openReceipt('<?php echo e(route('receipts.show', $v)); ?>')"
                                                    style="display:inline-flex;align-items:center;gap:4px;font-size:11px;color:var(--info);text-decoration:none;cursor:pointer">
                                                    <img src="<?php echo e(route('receipts.show', $v)); ?>" alt="<?php echo e(__('super-admin.receipt_preview_alt')); ?>"
                                                        style="width:28px;height:28px;object-fit:cover;border-radius:4px;border:1px solid var(--border)">
                                                </a>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php
                                                $vColors = ['pending' => ['bg' => 'var(--warning-light)', 'color' => 'var(--warning)'], 'approved' => ['bg' => 'var(--success-light)', 'color' => 'var(--success)'], 'rejected' => ['bg' => 'var(--danger-light)', 'color' => 'var(--danger)']];
                                                $vc = $vColors[$v->status->value] ?? ['bg' => 'var(--border)', 'color' => 'var(--text-muted)'];
                                            ?>
                                            <span class="badge" style="font-size:9px;background:<?php echo e($vc['bg']); ?>;color:<?php echo e($vc['color']); ?>;padding:2px 8px;border-radius:6px;font-weight:600"><?php echo e($v->status->label()); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="cell-muted">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <?php
                                            $colors = [
                                                \App\Enums\PaymentStatus::CheckoutPaid->value => ['bg' => 'var(--success-light)', 'color' => 'var(--success)'],
                                                \App\Enums\PaymentStatus::CheckoutPending->value => ['bg' => 'var(--warning-light)', 'color' => 'var(--warning)'],
                                                \App\Enums\PaymentStatus::CheckoutFailed->value => ['bg' => 'var(--danger-light)', 'color' => 'var(--danger)'],
                                                \App\Enums\PaymentStatus::CheckoutCanceled->value => ['bg' => 'var(--danger-light)', 'color' => 'var(--danger)'],
                                                \App\Enums\PaymentStatus::CheckoutExpired->value => ['bg' => 'var(--info-light)', 'color' => 'var(--info)'],
                                            ];
                                            $c = $colors[$payment->status->value] ?? ['bg' => 'var(--border)', 'color' => 'var(--text-muted)'];
                                        ?>
                                        <span class="badge" style="font-size:10px;background:<?php echo e($c['bg']); ?>;color:<?php echo e($c['color']); ?>;padding:3px 10px;border-radius:6px;font-weight:600"><?php echo e($payment->status->label()); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->isRefunded()): ?>
                                            <span class="badge" style="font-size:9px;background:var(--info-light);color:var(--info);padding:2px 8px;border-radius:6px;font-weight:600"><?php echo e(__('super-admin.refunded')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->webhook_processed_at): ?>
                                            <i class="bi bi-cloud-check" style="font-size:10px;opacity:0.5" title="<?php echo e(__('super-admin.webhook_processed')); ?>"></i>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                                <td class="cell-muted"><?php echo e($payment->paid_at?->format('Y/m/d') ?? $payment->created_at->format('Y/m/d')); ?></td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canVerify || $canRefund || $canViewRaw): ?>
                                    <td class="col-actions">
                                        <div class="d-flex gap-1">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canVerify && $payment->isPending() && !$isWebhook): ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->verification && $payment->verification->receipt_path): ?>
                                                    <a href="<?php echo e(route('receipts.show', $payment->verification)); ?>" target="_blank" class="btn"
                                                        style="padding:5px 10px;font-size:11px;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--info);text-decoration:none" title="<?php echo e(__('super-admin.view_receipt')); ?>">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <button type="button" class="btn" style="padding:5px 10px;font-size:11px;border-radius:var(--radius-xs);border:none;background:var(--success-light);color:var(--success);font-weight:600;cursor:pointer" @click="approvePayment(<?php echo e($payment->id); ?>)">
                                                    <i class="bi bi-check-lg"></i> <?php echo e(__('super-admin.approve')); ?>

                                                </button>
                                                <button type="button" class="btn" style="padding:5px 10px;font-size:11px;border-radius:var(--radius-xs);border:none;background:var(--danger-light);color:var(--danger);font-weight:600;cursor:pointer" @click="rejectPayment(<?php echo e($payment->id); ?>)">
                                                    <i class="bi bi-x-lg"></i> <?php echo e(__('super-admin.reject')); ?>

                                                </button>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canRefund && $payment->isRefundable()): ?>
                                                <button type="button" class="btn" style="padding:5px 10px;font-size:11px;border-radius:var(--radius-xs);border:1px solid var(--border);background:transparent;color:var(--info);cursor:pointer" @click="refundPayment(<?php echo e($payment->id); ?>, <?php echo e($payment->amount); ?>, '<?php echo e($payment->currency); ?>')" title="<?php echo e(__('super-admin.refund')); ?>">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canViewRaw): ?>
                                                <button type="button" class="btn" style="padding:5px 8px;font-size:11px;border-radius:var(--radius-xs);border:none;background:transparent;color:var(--text-muted);cursor:pointer" @click="showDetails(<?php echo e($payment->id); ?>)" title="<?php echo e(__('super-admin.view_details')); ?>">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon" style="background:var(--bg-subtle);color:var(--text-muted)"><i class="bi bi-cash-coin"></i></div>
                    <h4><?php echo e(__('general.no_data')); ?></h4>
                    <p><?php echo e(__('messages.no_results')); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payments->count()): ?>
            <div class="data-grid-footer">
                <?php if (isset($component)) { $__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination-info','data' => ['items' => $payments]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination-info'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($payments)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105)): ?>
<?php $attributes = $__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105; ?>
<?php unset($__attributesOriginal1c9f93a4a55ac41fe1d7ae66799ce105); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105)): ?>
<?php $component = $__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105; ?>
<?php unset($__componentOriginal1c9f93a4a55ac41fe1d7ae66799ce105); ?>
<?php endif; ?>
                <div><?php echo e($payments->appends(request()->except('page'))->links()); ?></div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:14px 20px">
                    <h5 class="modal-title" style="font-size:15px;font-weight:600"><i class="bi bi-receipt me-2"></i><?php echo e(__('super-admin.payment_details')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px;text-align:center">
                    <img id="receiptFullImage" src="" alt="<?php echo e(__('super-admin.receipt_preview_alt')); ?>" style="max-width:100%;max-height:70vh;border-radius:8px;border:1px solid var(--border);box-shadow:0 4px 12px rgba(0,0,0,0.08)">
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><i class="bi bi-check-circle me-2" style="color:var(--success)"></i><?php echo e(__('super-admin.approve_payment')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="approveForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body" style="padding:20px">
                        <div class="mb-3">
                            <label class="form-label" style="font-size:13px;font-weight:500;margin-bottom:6px"><?php echo e(__('super-admin.transaction_reference')); ?></label>
                            <input type="text" name="transaction_reference" class="form-control"
                                style="font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;width:100%"
                                placeholder="<?php echo e(__('super-admin.transaction_ref_placeholder')); ?>">
                        </div>
                        <input type="hidden" name="notes" value="Approved by admin">
                    </div>
                    <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px">
                        <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal"><?php echo e(__('general.cancel')); ?></button>
                        <button type="submit" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--success);color:white;font-weight:600;border:none"><?php echo e(__('super-admin.approve')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><i class="bi bi-x-circle me-2" style="color:var(--danger)"></i><?php echo e(__('super-admin.reject_payment')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="rejectForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body" style="padding:20px">
                        <div class="mb-3">
                            <label class="form-label" style="font-size:13px;font-weight:500;margin-bottom:6px"><?php echo e(__('super-admin.reject_reason')); ?></label>
                            <textarea name="notes" class="form-control" rows="3" required
                                style="font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;width:100%"
                                placeholder="<?php echo e(__('super-admin.reject_reason_placeholder')); ?>"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px">
                        <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal"><?php echo e(__('general.cancel')); ?></button>
                        <button type="submit" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--danger);color:white;font-weight:600;border:none"><?php echo e(__('super-admin.reject')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="refundModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px">
                    <h5 class="modal-title" style="font-size:16px;font-weight:600"><i class="bi bi-arrow-counterclockwise me-2" style="color:var(--info)"></i><?php echo e(__('super-admin.refund_payment')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="refundForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body" style="padding:20px">
                        <div class="mb-3">
                            <label class="form-label" style="font-size:13px;font-weight:500;margin-bottom:6px"><?php echo e(__('super-admin.refund_amount')); ?> (<span id="refundCurrency">DZD</span>)</label>
                            <input type="number" name="refund_amount" id="refundAmount" class="form-control" step="0.01" min="0.01"
                                style="font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;width:100%"
                                placeholder="0.00">
                            <small id="refundAmountHelp" style="font-size:11px;color:var(--text-muted)"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-size:13px;font-weight:500;margin-bottom:6px"><?php echo e(__('super-admin.refund_reason')); ?></label>
                            <textarea name="refund_reason" class="form-control" rows="3" required
                                style="font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;width:100%"
                                placeholder="<?php echo e(__('super-admin.refund_reason_placeholder')); ?>"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px">
                        <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal"><?php echo e(__('general.cancel')); ?></button>
                        <button type="submit" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);background:var(--info);color:white;font-weight:600;border:none"><?php echo e(__('super-admin.refund')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:var(--radius-md);border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:14px 20px">
                    <h5 class="modal-title" style="font-size:15px;font-weight:600"><i class="bi bi-info-circle me-2"></i><?php echo e(__('super-admin.payment_raw_details')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px">
                    <div id="detailsContent" style="max-height:60vh;overflow-y:auto">
                        <pre><code id="detailsJson" style="font-size:12px;direction:ltr;text-align:left;white-space:pre-wrap;word-break:break-word"></code></pre>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:14px 20px">
                    <button type="button" class="btn" style="padding:7px 16px;font-size:13px;border-radius:var(--radius-sm);border:1px solid var(--border);background:transparent;color:var(--text)" data-bs-dismiss="modal"><?php echo e(__('general.close')); ?></button>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            window.paymentsData = <?php echo json_encode($paymentsData, 15, 512) ?>;

            function approvePayment(paymentId) {
                const form = document.getElementById('approveForm');
                form.action = '<?php echo e(route('super.admin.payments.approve', '__ID__')); ?>'.replace('__ID__', paymentId);
                const modal = new bootstrap.Modal(document.getElementById('approveModal'));
                modal.show();
            }
            function rejectPayment(paymentId) {
                const form = document.getElementById('rejectForm');
                form.action = '<?php echo e(route('super.admin.payments.reject', '__ID__')); ?>'.replace('__ID__', paymentId);
                const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
                modal.show();
            }
            function refundPayment(paymentId, amount, currency) {
                const form = document.getElementById('refundForm');
                form.action = '<?php echo e(route('super.admin.payments.refund', '__ID__')); ?>'.replace('__ID__', paymentId);
                document.getElementById('refundAmount').max = amount;
                document.getElementById('refundAmount').value = amount;
                document.getElementById('refundCurrency').textContent = currency;
                document.getElementById('refundAmountHelp').textContent = '<?php echo e(__('super-admin.refund_max_amount')); ?>: ' + amount.toFixed(2) + ' ' + currency;
                const modal = new bootstrap.Modal(document.getElementById('refundModal'));
                modal.show();
            }
            function showDetails(paymentId) {
                const data = window.paymentsData[paymentId];
                if (!data) return;
                const json = JSON.stringify({
                    reference: data.reference,
                    transaction_id: data.transaction_id,
                    chargily_checkout_id: data.chargily_checkout_id,
                    gateway_reference: data.gateway_reference,
                    metadata: data.metadata,
                    gateway_payload: data.gateway_payload,
                    webhook_payload: data.webhook_payload,
                }, null, 2);
                document.getElementById('detailsJson').textContent = json;
                const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
                modal.show();
            }
            function openReceipt(url) {
                document.getElementById('receiptFullImage').src = url;
                const modal = new bootstrap.Modal(document.getElementById('receiptModal'));
                modal.show();
            }
        </script>
    <?php $__env->stopPush(); ?>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\super-admin\payments.blade.php ENDPATH**/ ?>