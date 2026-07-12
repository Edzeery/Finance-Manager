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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('zakat.title')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('zakat.title')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageDescription', null, []); ?> <?php echo e(__('zakat.calculate')); ?> <?php $__env->endSlot(); ?>


    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-calculator"></i>
                        <span><?php echo e(__('zakat.calculate')); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('zakat.calculate')); ?>" method="POST" id="zakatForm">
                        <?php echo csrf_field(); ?>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label-custom"><?php echo e(__('zakat.gold_price')); ?> <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="gold_price" id="gold_price" value="<?php echo e(old('gold_price', $input['gold_price'] ?? config('zakat.prices.gold_per_gram', 0))); ?>" class="form-custom" required placeholder="0.00">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px"><?php echo e(config('finance.currency_symbol')); ?>/g</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom"><?php echo e(__('zakat.silver_price')); ?> <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="silver_price" id="silver_price" value="<?php echo e(old('silver_price', $input['silver_price'] ?? config('zakat.prices.silver_per_gram', 0))); ?>" class="form-custom" required placeholder="0.00">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px"><?php echo e(config('finance.currency_symbol')); ?>/g</span>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3" style="font-size:14px"><?php echo e(__('zakat.assets')); ?></h6>
                        <div class="row g-3">
                            <?php
                                $assetFields = [
                                    'gold_value' => 'gold',
                                    'silver_value' => 'silver',
                                    'cash_value' => 'cash',
                                    'bank_value' => 'bank_account',
                                    'ccp_value' => 'ccp',
                                    'business_goods_value' => 'business_goods',
                                    'stocks_value' => 'stocks',
                                    'crypto_value' => 'crypto',
                                    'real_estate_value' => 'real_estate_value',
                                    'expected_receivables' => 'expected_receivables',
                                ];
                            ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $assetFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $assetType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $autoValue = $assets[$assetType] ?? 0;
                                    $inputVal = old($field, $result[$field] ?? $input[$field] ?? $autoValue);
                                ?>
                                <div class="col-md-6">
                                    <label class="form-label-custom"><?php echo e(__("zakat.{$field}")); ?></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="<?php echo e($field); ?>" value="<?php echo e($inputVal); ?>" class="form-custom" placeholder="0.00">
                                        <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px"><?php echo e(config('finance.currency_symbol')); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="form-label-custom"><?php echo e(__('zakat.notes')); ?></label>
                            <textarea name="notes" class="form-custom" rows="2" maxlength="1000"><?php echo e(old('notes', $input['notes'] ?? '')); ?></textarea>
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-accent btn-custom">
                                <i class="bi bi-calculator me-1"></i><?php echo e(__('zakat.calculate')); ?>

                            </button>
                            <button type="submit" name="save" value="1" class="btn btn-outline-accent btn-custom">
                                <i class="bi bi-save me-1"></i><?php echo e(__('zakat.save_record')); ?>

                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($result)): ?>
                <div class="card-custom mb-4">
                    <div class="card-header-custom">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-file-text"></i>
                            <span><?php echo e(__('zakat.report')); ?></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="result-row">
                            <span><?php echo e(__('zakat.nisab_gold')); ?></span>
                            <span class="fw-bold"><?php echo e(number_format($result['nisab_gold'], 2)); ?></span>
                        </div>
                        <div class="result-row">
                            <span><?php echo e(__('zakat.nisab_silver')); ?></span>
                            <span class="fw-bold"><?php echo e(number_format($result['nisab_silver'], 2)); ?></span>
                        </div>
                        <hr>
                        <div class="result-row">
                            <span><?php echo e(__('zakat.total_wealth')); ?></span>
                            <span class="fw-bold"><?php echo e(number_format($result['total_wealth'], 2)); ?></span>
                        </div>
                        <div class="result-row">
                            <span><?php echo e(__('zakat.total_zakatable')); ?></span>
                            <span class="fw-bold" style="color:<?php echo e($result['exceeds_nisab'] ? 'var(--success)' : 'var(--warning)'); ?>">
                                <?php echo e(number_format($result['total_zakatable'], 2)); ?>

                            </span>
                        </div>
                        <div class="result-row">
                            <span><?php echo e(__('zakat.exceeds_nisab')); ?></span>
                            <span class="fw-bold" style="color:<?php echo e($result['exceeds_nisab'] ? 'var(--success)' : 'var(--danger)'); ?>">
                                <?php echo e($result['exceeds_nisab'] ? __('general.yes') : __('general.no')); ?>

                            </span>
                        </div>
                        <hr>
                        <div class="result-row" style="font-size:18px">
                            <span><?php echo e(__('zakat.zakat_amount')); ?></span>
                            <span class="fw-bold" style="color:var(--accent)"><?php echo e(number_format($result['zakat_amount'], 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card-custom">
                    <div class="card-header-custom">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history"></i>
                            <span><?php echo e(__('zakat.history')); ?></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php echo $__env->make('components.empty-state', [
                            'icon' => 'bi-heart',
                            'title' => __('zakat.no_records'),
                            'message' => __('zakat.calculate_first'),
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/zakat/calculator.blade.php ENDPATH**/ ?>