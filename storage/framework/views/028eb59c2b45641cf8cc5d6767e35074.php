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

    <?php echo $__env->make('zakat._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="row g-4">
        <div class="col-lg-8">
            
            <div class="card-custom mb-4">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 d-flex align-items-center gap-2" style="font-size:inherit; font-weight:inherit">
                        <i class="bi bi-gem"></i>
                        <span><?php echo e(__('zakat.gold_silver_holdings')); ?></span>
                    </h5>
                    <span style="font-size:13px; color:var(--text-muted)"><?php echo e($r->calculation_date->format('Y/m/d')); ?></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-custom">
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->gold_weight): ?>
                            <tr>
                                <td><?php echo e(__('zakat.gold_weight')); ?></td>
                                <td class="text-end"><?php echo e(number_format($r->gold_weight, 4)); ?>g × <?php echo e(number_format($r->gold_price_per_gram, 2)); ?></td>
                                <td class="text-end fw-bold" style="color:#FFC107"><?php echo e(number_format($r->gold_value, 2)); ?></td>
                            </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->silver_weight): ?>
                            <tr>
                                <td><?php echo e(__('zakat.silver_weight')); ?></td>
                                <td class="text-end"><?php echo e(number_format($r->silver_weight, 4)); ?>g × <?php echo e(number_format($r->silver_price_per_gram, 2)); ?></td>
                                <td class="text-end fw-bold" style="color:#94A3B8"><?php echo e(number_format($r->silver_value, 2)); ?></td>
                            </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$r->gold_weight && !$r->silver_weight): ?>
                            <tr>
                                <td><?php echo e(__('zakat.gold_value')); ?></td>
                                <td class="text-end" colspan="2"><?php echo e(number_format($r->gold_value, 2)); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo e(__('zakat.silver_value')); ?></td>
                                <td class="text-end" colspan="2"><?php echo e(number_format($r->silver_value, 2)); ?></td>
                            </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2" style="font-size:inherit; font-weight:inherit">
                        <i class="bi bi-cash-stack"></i>
                        <span><?php echo e(__('zakat.cash_and_bank')); ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-custom">
                        <tbody>
                            <tr><td><?php echo e(__('zakat.cash_value')); ?></td><td class="text-end"><?php echo e(number_format($r->cash_value, 2)); ?></td></tr>
                            <tr><td><?php echo e(__('zakat.bank_value')); ?></td><td class="text-end"><?php echo e(number_format($r->bank_value, 2)); ?></td></tr>
                            <tr><td><?php echo e(__('zakat.ccp_value')); ?></td><td class="text-end"><?php echo e(number_format($r->ccp_value, 2)); ?></td></tr>
                            <tr class="fw-bold"><td><?php echo e(__('zakat.cash_and_bank')); ?></td><td class="text-end" style="color:#22C55E"><?php echo e(number_format($r->cash_value + $r->bank_value + $r->ccp_value, 2)); ?></td></tr>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2" style="font-size:inherit; font-weight:inherit">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span><?php echo e(__('zakat.business_and_investments')); ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-custom">
                        <tbody>
                            <tr><td><?php echo e(__('zakat.business_goods')); ?></td><td class="text-end"><?php echo e(number_format($r->business_goods_value, 2)); ?></td></tr>
                            <tr><td><?php echo e(__('zakat.stocks_value')); ?></td><td class="text-end"><?php echo e(number_format($r->stocks_value, 2)); ?></td></tr>
                            <tr><td><?php echo e(__('zakat.crypto_value')); ?></td><td class="text-end"><?php echo e(number_format($r->crypto_value, 2)); ?></td></tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="fw-bold"><?php echo e(__('zakat.total_wealth')); ?></td>
                                <td class="text-end fw-bold"><?php echo e(number_format($r->total_wealth, 2)); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold"><?php echo e(__('zakat.total_zakatable')); ?></td>
                                <td class="text-end fw-bold" style="color:<?php echo e($r->exceeds_nisab ? 'var(--success)' : 'var(--warning)'); ?>"><?php echo e(number_format($r->total_zakatable, 2)); ?></td>
                            </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->total_debts > 0): ?>
                            <tr>
                                <td class="fw-bold"><?php echo e(__('zakat.total_debts')); ?></td>
                                <td class="text-end fw-bold" style="color:var(--danger)">- <?php echo e(number_format($r->total_debts, 2)); ?></td>
                            </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <tr>
                                <td class="fw-bold"><?php echo e(__('zakat.net_zakatable')); ?></td>
                                <td class="text-end fw-bold" style="color:var(--accent)"><?php echo e(number_format($r->net_zakatable ?? $r->total_zakatable, 2)); ?></td>
                            </tr>
                        </tfoot>
                     </table>
                     </div>
                 </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->real_estate_value > 0 || $r->expected_receivables > 0): ?>
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2" style="font-size:inherit; font-weight:inherit">
                        <i class="bi bi-box"></i>
                        <span><?php echo e(__('zakat.other_assets')); ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-custom">
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->real_estate_value > 0): ?>
                            <tr><td><?php echo e(__('zakat.real_estate_value')); ?></td><td class="text-end"><?php echo e(number_format($r->real_estate_value, 2)); ?></td></tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->expected_receivables > 0): ?>
                            <tr><td><?php echo e(__('zakat.expected_receivables')); ?></td><td class="text-end"><?php echo e(number_format($r->expected_receivables, 2)); ?></td></tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="col-lg-4">
            
            <div class="card-custom mb-4">
                <div class="card-body text-center">
                    <h6 class="fw-bold" style="color:var(--text-muted)"><?php echo e(__('zakat.zakat_amount')); ?></h6>
                    <h2 class="fw-bold my-3" style="color:var(--accent)">
                        <?php echo e(number_format($r->zakat_amount, 2)); ?> <?php echo e(config('finance.currency_symbol')); ?>

                    </h2>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->exceeds_nisab): ?>
                        <p style="color:var(--success); font-size:14px" class="mb-0">
                            <i class="bi bi-check-circle-fill me-1"></i>
                            <?php echo e(__('zakat.exceeds_nisab')); ?>: <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => 'yes','set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'yes','set' => 'bi']); ?>
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
                        </p>
                    <?php else: ?>
                        <p style="color:var(--text-muted); font-size:14px" class="mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            <?php echo e(__('zakat.exceeds_nisab')); ?>: <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'status-kit::components.status-badge','data' => ['domain' => 'general','status' => 'no','set' => 'bi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['domain' => 'general','status' => 'no','set' => 'bi']); ?>
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
                        </p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-gem"></i>
                        <span><?php echo e(__('zakat.nisab')); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-size:13px; color:var(--text-muted)"><?php echo e(__('zakat.nisab_gold')); ?> (85g)</span>
                        <span class="fw-bold"><?php echo e(number_format($r->nisab_gold, 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-size:13px; color:var(--text-muted)"><?php echo e(__('zakat.nisab_silver')); ?> (595g)</span>
                        <span class="fw-bold"><?php echo e(number_format($r->nisab_silver, 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></span>
                    </div>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->notes): ?>
                <div class="card-custom mb-4">
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