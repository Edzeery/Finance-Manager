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

    <?php echo $__env->make('zakat._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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

                        <div class="alert alert-info d-flex align-items-center gap-2 mb-4" style="border-radius:8px; font-size:13px; background:rgba(59,130,246,0.08); border:1px solid rgba(59,130,246,0.2); color:var(--text)">
                            <i class="bi bi-currency-exchange"></i>
                            <span>
                                <?php echo e(__('zakat.currency_used', ['currency' => config('finance.currency')])); ?>

                                <a href="<?php echo e(route('account.settings')); ?>" style="text-decoration:underline; color:var(--accent)"><?php echo e(__('zakat.change_currency')); ?></a>
                            </span>
                        </div>

                        
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="font-size:14px; color:var(--accent)">
                                <i class="bi bi-gem"></i>
                                <?php echo e(__('zakat.prices')); ?>

                            </h6>
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label-custom"><?php echo e(__('zakat.silver_price')); ?> <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="silver_price" id="silver_price" value="<?php echo e(old('silver_price', $input['silver_price'] ?? config('zakat.prices.silver_per_gram', 0))); ?>" class="form-custom" required placeholder="0.00">
                                        <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px"><?php echo e(config('finance.currency_symbol')); ?>/g</span>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="d-flex align-items-center gap-2" style="margin-top:22px">
                                        <button type="button" onclick="fetchPrices()" id="fetchBtn" class="btn btn-outline-accent btn-custom" style="white-space:nowrap">
                                            <i class="bi bi-arrow-clockwise me-1" id="fetchIcon"></i><?php echo e(__('zakat.fetch_prices')); ?>

                                        </button>
                                        <small id="fetchStatus" class="text-muted d-none"></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="font-size:14px; color:#FFC107">
                                <i class="bi bi-gem"></i>
                                <?php echo e(__('zakat.gold_holdings')); ?>

                            </h6>

                            <div id="goldRows">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $goldItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="gold-row row g-2 mb-2 align-items-end" data-index="<?php echo e($idx); ?>">
                                    <div class="col-md-3">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idx === 0): ?>
                                        <label class="form-label-custom" style="font-size:12px"><?php echo e(__('zakat.gold_karat')); ?></label>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <select name="gold_items[<?php echo e($idx); ?>][karat]" class="form-custom gold-karat-select" onchange="onGoldKaratChange(this)">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $karatPurity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($k); ?>" <?php echo e(($item['karat'] ?? 21) == $k ? 'selected' : ''); ?>><?php echo e($k); ?>K</option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idx === 0): ?>
                                        <label class="form-label-custom" style="font-size:12px"><?php echo e(__('zakat.gold_weight')); ?> (g)</label>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <input type="number" step="0.0001" min="0" name="gold_items[<?php echo e($idx); ?>][weight]" value="<?php echo e($item['weight'] ?? ''); ?>" class="form-custom gold-weight" placeholder="0.0000" oninput="calcGoldTotal()">
                                    </div>
                                    <div class="col-md-3">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idx === 0): ?>
                                        <label class="form-label-custom" style="font-size:12px"><?php echo e(__('zakat.price_per_gram')); ?></label>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <div class="input-group">
                                            <input type="number" step="0.01" min="0" name="gold_items[<?php echo e($idx); ?>][price]" value="<?php echo e($item['price'] ?? ''); ?>" class="form-custom gold-price" placeholder="0.00" oninput="calcGoldTotal()">
                                            <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:12px"><?php echo e(config('finance.currency_symbol')); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idx === 0): ?>
                                        <label class="form-label-custom" style="font-size:12px"><?php echo e(__('zakat.gold_value')); ?></label>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <div class="form-custom gold-row-value" style="background:var(--bg-secondary); display:flex; align-items:center; font-weight:600; font-size:13px; min-height:38px; padding:0 10px">0</div>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end" style="padding-bottom:2px">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idx > 0): ?>
                                        <button type="button" onclick="removeGoldRow(this)" class="btn btn-outline-danger btn-sm" style="padding:4px 8px; font-size:12px" title="<?php echo e(__('general.remove')); ?>">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <button type="button" onclick="addGoldRow()" class="btn btn-outline-secondary btn-sm mt-2" style="font-size:12px">
                                <i class="bi bi-plus-lg me-1"></i><?php echo e(__('zakat.add_gold_row')); ?>

                            </button>

                            <div class="mt-3 p-3" style="border-radius:8px; background:rgba(255,193,7,0.06); border:1px solid rgba(255,193,7,0.15)">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span style="font-size:13px; font-weight:600; color:#FFC107">
                                        <i class="bi bi-gem me-1"></i><?php echo e(__('zakat.gold_total')); ?>

                                    </span>
                                    <span id="goldTotalDisplay" style="font-size:15px; font-weight:700; color:#FFC107">0 <?php echo e(config('finance.currency_symbol')); ?></span>
                                </div>
                                <small id="goldTotalWeight" style="color:var(--text-muted); font-size:11px"></small>
                            </div>
                            <input type="hidden" name="gold_total_weight" id="gold_total_weight">
                        </div>

                        
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="font-size:14px; color:#94A3B8">
                                <i class="bi bi-gem"></i>
                                <?php echo e(__('zakat.silver_value')); ?>

                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-custom"><?php echo e(__('zakat.silver_weight')); ?> (g)</label>
                                    <input type="number" step="0.0001" min="0" name="silver_weight" id="silver_weight" value="<?php echo e(old('silver_weight', $input['silver_weight'] ?? ($assets['silver']['weight'] ?? ''))); ?>" class="form-custom" placeholder="0.0000">
                                </div>
                            </div>
                        </div>

                        
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="font-size:14px; color:#22C55E">
                                <i class="bi bi-cash-stack"></i>
                                <?php echo e(__('zakat.cash_and_bank')); ?>

                            </h6>
                            <div class="row g-3">
                                <?php
                                    $cashFields = [
                                        'cash_value' => ['icon' => 'bi-cash', 'asset_type' => 'cash'],
                                        'bank_value' => ['icon' => 'bi-bank', 'asset_type' => 'bank_account'],
                                        'ccp_value' => ['icon' => 'bi-envelope', 'asset_type' => 'ccp'],
                                    ];
                                ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cashFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $autoValue = $assets[$config['asset_type']] ?? 0;
                                        $inputVal = old($field, $input[$field] ?? ($autoValue > 0 ? $autoValue : ''));
                                    ?>
                                    <div class="col-md-4">
                                        <label class="form-label-custom d-flex align-items-center gap-1">
                                            <i class="<?php echo e($config['icon']); ?>" style="font-size:14px"></i>
                                            <?php echo e(__("zakat.{$field}")); ?>

                                        </label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" min="0" name="<?php echo e($field); ?>" value="<?php echo e($inputVal); ?>" class="form-custom" placeholder="0.00">
                                            <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px"><?php echo e(config('finance.currency_symbol')); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="font-size:14px; color:#3B82F6">
                                <i class="bi bi-shop"></i>
                                <?php echo e(__('zakat.business_goods')); ?>

                            </h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label-custom"><?php echo e(__('zakat.business_goods_value')); ?></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="business_goods_value" value="<?php echo e(old('business_goods_value', $input['business_goods_value'] ?? '')); ?>" class="form-custom" placeholder="0.00">
                                        <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px"><?php echo e(config('finance.currency_symbol')); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="font-size:14px; color:#8B5CF6">
                                <i class="bi bi-graph-up-arrow"></i>
                                <?php echo e(__('zakat.investments')); ?>

                            </h6>
                            <div class="row g-3">
                                <?php
                                    $investFields = [
                                        'stocks_value' => ['icon' => 'bi-bar-chart', 'asset_type' => 'stocks'],
                                        'crypto_value' => ['icon' => 'bi-currency-bitcoin', 'asset_type' => 'crypto'],
                                    ];
                                ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $investFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $autoValue = $assets[$config['asset_type']] ?? 0;
                                        $inputVal = old($field, $input[$field] ?? ($autoValue > 0 ? $autoValue : ''));
                                    ?>
                                    <div class="col-md-6">
                                        <label class="form-label-custom d-flex align-items-center gap-1">
                                            <i class="<?php echo e($config['icon']); ?>" style="font-size:14px"></i>
                                            <?php echo e(__("zakat.{$field}")); ?>

                                        </label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" min="0" name="<?php echo e($field); ?>" value="<?php echo e($inputVal); ?>" class="form-custom" placeholder="0.00">
                                            <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px"><?php echo e(config('finance.currency_symbol')); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="font-size:14px; color:#F59E0B">
                                <i class="bi bi-box"></i>
                                <?php echo e(__('zakat.other_assets')); ?>

                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-custom"><?php echo e(__('zakat.real_estate_value')); ?></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="real_estate_value" value="<?php echo e(old('real_estate_value', $input['real_estate_value'] ?? '')); ?>" class="form-custom" placeholder="0.00">
                                        <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px"><?php echo e(config('finance.currency_symbol')); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom d-flex align-items-center gap-1">
                                        <span><?php echo e(__('zakat.expected_receivables')); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($owedDebtsTotal > 0): ?>
                                            <span style="font-size:11px; padding:2px 6px; border-radius:4px; background:rgba(20,184,166,0.1); color:#14b8a6; font-weight:500">
                                                <i class="bi bi-link-45deg"></i> <?php echo e(__('zakat.auto_from_debts')); ?>

                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="expected_receivables"
                                            value="<?php echo e(old('expected_receivables', $input['expected_receivables'] ?? ($owedDebtsTotal > 0 ? $owedDebtsTotal : ''))); ?>"
                                            class="form-custom" placeholder="0.00">
                                        <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px"><?php echo e(config('finance.currency_symbol')); ?></span>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($owedDebts->count() > 0): ?>
                                        <div class="mt-2" style="font-size:12px; color:var(--text-muted)">
                                            <i class="bi bi-info-circle"></i>
                                            <?php echo e($owedDebts->count()); ?> <?php echo e(__('zakat.active_receivables')); ?>:
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $owedDebts->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span style="color:var(--text)"><?php echo e($debt['counterparty_name']); ?> (<?php echo e(number_format($debt['remaining_amount'], 2)); ?>)</span><?php echo e($loop->last ? '' : '، '); ?>

                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($owedDebts->count() > 3): ?>
                                                +<?php echo e($owedDebts->count() - 3); ?>

                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
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
                    <div class="card-body text-center">
                        <h6 class="fw-bold" style="color:var(--text-muted)"><?php echo e(__('zakat.zakat_amount')); ?></h6>
                        <h2 class="fw-bold my-3" style="color:var(--accent)">
                            <?php echo e(number_format($result['totalZakat'], 2)); ?> <?php echo e(config('finance.currency_symbol')); ?>

                        </h2>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($result['exceedsNisab']): ?>
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
                            <span class="fw-bold"><?php echo e(number_format($result['nisabGold'], 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="font-size:13px; color:var(--text-muted)"><?php echo e(__('zakat.nisab_silver')); ?> (595g)</span>
                            <span class="fw-bold"><?php echo e(number_format($result['nisabSilver'], 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></span>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($owingDebts->count() > 0 || $owedDebts->count() > 0): ?>
                        <hr class="my-3">
                        <div style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px">
                            <i class="bi bi-diagram-3"></i> <?php echo e(__('zakat.debt_details')); ?>

                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($owingDebts->count() > 0): ?>
                            <div style="font-size:12px; font-weight:600; color:var(--danger); margin-bottom:6px">
                                <i class="bi bi-arrow-up-right"></i> <?php echo e(__('zakat.your_debts')); ?> (<?php echo e(__('debt.owing')); ?>)
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $owingDebts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="d-flex justify-content-between mb-1" style="font-size:12px">
                                    <span style="color:var(--text-muted)">
                                        <i class="bi bi-person" style="font-size:10px"></i> <?php echo e($debt['counterparty_name']); ?>

                                    </span>
                                    <span style="color:var(--danger)"><?php echo e(number_format($debt['remaining_amount'], 2)); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="d-flex justify-content-between mt-1 mb-3" style="font-size:12px; font-weight:600">
                                <span style="color:var(--danger)"><?php echo e(__('zakat.total_debts')); ?></span>
                                <span style="color:var(--danger)">- <?php echo e(number_format($result['totalDebts'], 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($owedDebts->count() > 0): ?>
                            <div style="font-size:12px; font-weight:600; color:#14b8a6; margin-bottom:6px">
                                <i class="bi bi-arrow-down-left"></i> <?php echo e(__('zakat.your_receivables')); ?> (<?php echo e(__('debt.owed')); ?>)
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $owedDebts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="d-flex justify-content-between mb-1" style="font-size:12px">
                                    <span style="color:var(--text-muted)">
                                        <i class="bi bi-person" style="font-size:10px"></i> <?php echo e($debt['counterparty_name']); ?>

                                    </span>
                                    <span style="color:#14b8a6"><?php echo e(number_format($debt['remaining_amount'], 2)); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="d-flex justify-content-between mt-1 mb-3" style="font-size:12px; font-weight:600">
                                <span style="color:#14b8a6"><?php echo e(__('zakat.expected_receivables')); ?></span>
                                <span style="color:#14b8a6">+ <?php echo e(number_format($owedDebtsTotal, 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></span>
                            </div>
                            <div style="font-size:11px; color:var(--text-muted); padding:6px 8px; background:rgba(20,184,166,0.06); border-radius:6px; border:1px solid rgba(20,184,166,0.15)">
                                <i class="bi bi-info-circle"></i> <?php echo e(__('zakat.receivables_not_zakatable')); ?>

                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="card-custom mb-4">
                    <div class="card-header-custom">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-wallet2"></i>
                            <span><?php echo e(__('zakat.total_wealth')); ?></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span style="font-size:13px; color:var(--text-muted)"><?php echo e(__('zakat.total_wealth')); ?></span>
                            <span class="fw-bold"><?php echo e(number_format($result['totalWealth'], 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="font-size:13px; color:var(--text-muted)"><?php echo e(__('zakat.total_zakatable')); ?></span>
                            <span class="fw-bold"><?php echo e(number_format($result['totalZakatable'], 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="font-size:13px; color:var(--text-muted)"><?php echo e(__('zakat.net_zakatable')); ?></span>
                            <span class="fw-bold" style="color:<?php echo e($result['exceedsNisab'] ? 'var(--success)' : 'var(--warning)'); ?>">
                                <?php echo e(number_format($result['netZakatable'], 2)); ?> <?php echo e(config('finance.currency_symbol')); ?>

                            </span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($result['totalDebts'] > 0): ?>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="font-size:13px; color:var(--text-muted)"><?php echo e(__('zakat.total_zakat_gross')); ?></span>
                            <span class="fw-bold"><?php echo e(number_format($result['totalZakatGross'], 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="font-size:13px; color:var(--text-muted)">- <?php echo e(__('zakat.total_debts')); ?></span>
                            <span class="fw-bold" style="color:var(--danger)">- <?php echo e(number_format($result['totalDebts'], 2)); ?> <?php echo e(config('finance.currency_symbol')); ?></span>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <details class="card-custom mb-4" open>
                    <summary class="card-header-custom" style="cursor:pointer">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-heart-fill"></i>
                            <span><?php echo e(__('zakat.zakat_breakdown')); ?></span>
                        </h5>
                    </summary>
                    <div class="card-body">
                        <?php
                            $breakdown = [
                                ['label' => 'zakat.cash_and_bank', 'amount' => $result['cashZakat'], 'color' => '#22C55E'],
                                ['label' => 'zakat.gold_value', 'amount' => $result['goldZakat'], 'color' => '#FFC107'],
                                ['label' => 'zakat.silver_value', 'amount' => $result['silverZakat'], 'color' => '#94A3B8'],
                                ['label' => 'zakat.business_goods', 'amount' => $result['businessZakat'], 'color' => '#3B82F6'],
                                ['label' => 'zakat.investments', 'amount' => $result['investmentsZakat'], 'color' => '#8B5CF6'],
                            ];
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $breakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item['amount'] > 0): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="d-flex align-items-center gap-2">
                                        <span style="width:8px; height:8px; border-radius:50%; background:<?php echo e($item['color']); ?>"></span>
                                        <span style="font-size:13px"><?php echo e(__($item['label'])); ?></span>
                                    </span>
                                    <span class="fw-bold"><?php echo e(number_format($item['amount'], 2)); ?></span>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($result['goldBreakdown']) && count($result['goldBreakdown']) > 1): ?>
                            <hr style="margin:8px 0">
                            <div style="font-size:11px; color:var(--text-muted); margin-bottom:4px"><?php echo e(__('zakat.gold_breakdown_detail')); ?>:</div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $result['goldBreakdown']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="d-flex justify-content-between" style="font-size:12px; color:var(--text-muted)">
                                    <span><?php echo e($gb['karat']); ?>K — <?php echo e($gb['weight']); ?>g × <?php echo e(number_format($gb['price'], 2)); ?></span>
                                    <span><?php echo e(number_format($gb['value'], 2)); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </details>
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

    <?php $__env->startPush('scripts'); ?>
    <script>
        const karatPurity = <?php echo json_encode($karatPurity, 15, 512) ?>;
        const currencySymbol = <?php echo json_encode(config('finance.currency_symbol'), 15, 512) ?>;
        let goldRowIndex = <?php echo e(count($goldItems)); ?>;

        function addGoldRow() {
            const container = document.getElementById('goldRows');
            const idx = goldRowIndex++;
            const defaultKarat = 21;
            const options = Object.keys(karatPurity).map(k =>
                '<option value="' + k + '"' + (k == defaultKarat ? ' selected' : '') + '>' + k + 'K</option>'
            ).join('');

            const html = '<div class="gold-row row g-2 mb-2 align-items-end" data-index="' + idx + '">' +
                '<div class="col-md-3"><select name="gold_items[' + idx + '][karat]" class="form-custom gold-karat-select" onchange="onGoldKaratChange(this)">' + options + '</select></div>' +
                '<div class="col-md-3"><input type="number" step="0.0001" min="0" name="gold_items[' + idx + '][weight]" class="form-custom gold-weight" placeholder="0.0000" oninput="calcGoldTotal()"></div>' +
                '<div class="col-md-3"><div class="input-group"><input type="number" step="0.01" min="0" name="gold_items[' + idx + '][price]" class="form-custom gold-price" placeholder="0.00" oninput="calcGoldTotal()"><span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:12px">' + currencySymbol + '</span></div></div>' +
                '<div class="col-md-2"><div class="form-custom gold-row-value" style="background:var(--bg-secondary); display:flex; align-items:center; font-weight:600; font-size:13px; min-height:38px; padding:0 10px">0</div></div>' +
                '<div class="col-md-1 d-flex align-items-end" style="padding-bottom:2px"><button type="button" onclick="removeGoldRow(this)" class="btn btn-outline-danger btn-sm" style="padding:4px 8px; font-size:12px"><i class="bi bi-x-lg"></i></button></div>' +
                '</div>';
            container.insertAdjacentHTML('beforeend', html);
        }

        function removeGoldRow(btn) {
            btn.closest('.gold-row').remove();
            calcGoldTotal();
        }

        function onGoldKaratChange(select) {
            calcGoldTotal();
        }

        function calcGoldTotal() {
            const rows = document.querySelectorAll('.gold-row');
            let totalValue = 0;
            let totalWeight = 0;

            rows.forEach(row => {
                const weight = parseFloat(row.querySelector('.gold-weight')?.value) || 0;
                const price = parseFloat(row.querySelector('.gold-price')?.value) || 0;
                const valueEl = row.querySelector('.gold-row-value');
                const value = weight * price;
                if (valueEl) valueEl.textContent = value > 0 ? parseFloat(value).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0';
                totalValue += value;
                totalWeight += weight;
            });

            document.getElementById('goldTotalDisplay').textContent = parseFloat(totalValue).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ' + currencySymbol;
            document.getElementById('goldTotalWeight').textContent = totalWeight > 0 ? totalWeight.toFixed(4) + 'g' : '';
            document.getElementById('gold_total_weight').value = totalWeight;
        }

        async function fetchPrices() {
            const btn = document.getElementById('fetchBtn');
            const icon = document.getElementById('fetchIcon');
            const status = document.getElementById('fetchStatus');
            const karatSelects = document.querySelectorAll('.gold-karat-select');
            const karats = [...new Set([...karatSelects].map(s => s.value))].join(',');

            btn.disabled = true;
            icon.classList.add('animate-spin');
            status.className = 'text-muted';
            status.textContent = '<?php echo e(__("zakat.auto_fetching")); ?>';
            status.classList.remove('d-none');

            try {
                const response = await fetch('<?php echo e(route("zakat.fetch-prices")); ?>?karats=' + karats);
                const data = await response.json();

                if (data.success) {
                    karatSelects.forEach(select => {
                        const karat = select.value;
                        const price = data.gold_prices?.[karat];
                        if (price !== null && price !== undefined) {
                            const row = select.closest('.gold-row');
                            const priceInput = row?.querySelector('.gold-price');
                            if (priceInput) priceInput.value = parseFloat(price).toFixed(2);
                        }
                    });
                    calcGoldTotal();

                    if (data.silver !== null) {
                        document.getElementById('silver_price').value = parseFloat(data.silver).toFixed(2);
                    }

                    status.className = 'text-success';
                    status.textContent = data.symbol + ' ' + data.currency + ' — ' + Object.keys(data.gold_prices || {}).length + ' karat(s)';
                } else {
                    status.className = 'text-danger';
                    status.textContent = data.message || '<?php echo e(__("zakat.fetch_prices_error")); ?>';
                }
            } catch (e) {
                status.className = 'text-danger';
                status.textContent = '<?php echo e(__("zakat.fetch_prices_error")); ?>';
            } finally {
                btn.disabled = false;
                icon.classList.remove('animate-spin');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            calcGoldTotal();
            fetchPrices();
        });
    </script>
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
<?php endif; ?>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\zakat\calculator.blade.php ENDPATH**/ ?>