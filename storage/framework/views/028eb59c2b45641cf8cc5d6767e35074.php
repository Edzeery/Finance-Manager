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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('zakat.report')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('zakat.report')); ?> <?php $__env->endSlot(); ?>

    <?php
        $r = $zakatRecord;
    ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 d-flex align-items-center gap-2" style="font-size:inherit; font-weight:inherit">
                        <i class="bi bi-file-text"></i>
                        <span><?php echo e(__('zakat.report')); ?></span>
                    </h5>
                    <span style="font-size:13px; color:var(--text-muted)"><?php echo e($r->calculation_date->format('Y/m/d')); ?></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-custom">
                        <tbody>
                            <tr><td><?php echo e(__('zakat.gold_value')); ?></td><td class="text-end"><?php echo e(number_format($r->gold_value, 2)); ?></td></tr>
                            <tr><td><?php echo e(__('zakat.silver_value')); ?></td><td class="text-end"><?php echo e(number_format($r->silver_value, 2)); ?></td></tr>
                            <tr><td><?php echo e(__('zakat.cash_value')); ?></td><td class="text-end"><?php echo e(number_format($r->cash_value, 2)); ?></td></tr>
                            <tr><td><?php echo e(__('zakat.bank_value')); ?></td><td class="text-end"><?php echo e(number_format($r->bank_value, 2)); ?></td></tr>
                            <tr><td><?php echo e(__('zakat.ccp_value')); ?></td><td class="text-end"><?php echo e(number_format($r->ccp_value, 2)); ?></td></tr>
                            <tr><td><?php echo e(__('zakat.business_goods')); ?></td><td class="text-end"><?php echo e(number_format($r->business_goods_value, 2)); ?></td></tr>
                            <tr><td><?php echo e(__('zakat.stocks_value')); ?></td><td class="text-end"><?php echo e(number_format($r->stocks_value, 2)); ?></td></tr>
                            <tr><td><?php echo e(__('zakat.crypto_value')); ?></td><td class="text-end"><?php echo e(number_format($r->crypto_value, 2)); ?></td></tr>
                            <tr><td><?php echo e(__('zakat.real_estate_value')); ?></td><td class="text-end"><?php echo e(number_format($r->real_estate_value, 2)); ?></td></tr>
                            <tr><td><?php echo e(__('zakat.expected_receivables')); ?></td><td class="text-end"><?php echo e(number_format($r->expected_receivables, 2)); ?></td></tr>
                         </tbody>
                         <tfoot>
                             <tr><td class="fw-bold"><?php echo e(__('zakat.total_wealth')); ?></td><td class="text-end fw-bold"><?php echo e(number_format($r->total_wealth, 2)); ?></td></tr>
                             <tr><td class="fw-bold"><?php echo e(__('zakat.total_zakatable')); ?></td><td class="text-end fw-bold" style="color:<?php echo e($r->exceeds_nisab ? 'var(--success)' : 'var(--warning)'); ?>"><?php echo e(number_format($r->total_zakatable, 2)); ?></td></tr>
                         </tfoot>
                     </table>
                     </div>
                 </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-gem"></i>
                        <span><?php echo e(__('zakat.nisab')); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-size:13px; color:var(--text-muted)"><?php echo e(__('zakat.nisab_gold')); ?></span>
                        <span class="fw-bold"><?php echo e(number_format($r->nisab_gold, 2)); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-size:13px; color:var(--text-muted)"><?php echo e(__('zakat.nisab_silver')); ?></span>
                        <span class="fw-bold"><?php echo e(number_format($r->nisab_silver, 2)); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span><?php echo e(__('zakat.exceeds_nisab')); ?></span>
                        <span class="fw-bold" style="color:<?php echo e($r->exceeds_nisab ? 'var(--success)' : 'var(--danger)'); ?>">
                            <?php echo e($r->exceeds_nisab ? __('general.yes') : __('general.no')); ?>

                        </span>
                    </div>
                    <div class="d-flex justify-content-between" style="font-size:18px">
                        <span class="fw-bold"><?php echo e(__('zakat.zakat_amount')); ?></span>
                        <span class="fw-bold" style="color:var(--accent)"><?php echo e(number_format($r->zakat_amount, 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></span>
                    </div>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->notes): ?>
                <div class="card-custom">
                    <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-sticky"></i>
                        <span><?php echo e(__('zakat.notes')); ?></span>
                    </h5>
                </div>
                    <div class="card-body">
                        <p style="font-size:14px; margin:0"><?php echo e($r->notes); ?></p>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="mt-4 d-flex gap-2">
                <a href="<?php echo e(route('zakat.calculator')); ?>" class="btn btn-accent btn-custom">
                    <i class="bi bi-arrow-left me-1"></i><?php echo e(__('zakat.calculate')); ?>

                </a>
                <a href="<?php echo e(route('zakat.history')); ?>" class="btn btn-outline-secondary btn-custom" style="flex:1">
                    <i class="bi bi-clock-history me-1"></i><?php echo e(__('zakat.history')); ?>

                </a>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\zakat\report.blade.php ENDPATH**/ ?>