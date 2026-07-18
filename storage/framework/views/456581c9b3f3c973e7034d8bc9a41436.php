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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('asset.add')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('asset.add')); ?> <?php $__env->endSlot(); ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-body p-4">

                    
                    <div style="padding:14px 16px; border-radius:var(--radius); background:rgba(59,130,246,0.06); border:1px solid rgba(59,130,246,0.15); margin-bottom:24px">
                        <div style="font-size:13px; font-weight:600; color:var(--accent); margin-bottom:8px; display:flex; align-items:center; gap:6px">
                            <i class="bi bi-info-circle"></i>
                            <?php echo e(__('asset.important_notes')); ?>

                        </div>
                        <ul style="margin:0; padding:0 0 0 16px; font-size:12px; color:var(--text-muted); line-height:1.8">
                            <li><?php echo e(__('asset.important_notes_1')); ?></li>
                            <li><?php echo e(__('asset.important_notes_2')); ?></li>
                            <li><?php echo e(__('asset.important_notes_3')); ?></li>
                        </ul>
                    </div>

                    <form action="<?php echo e(route('asset.store')); ?>" method="POST" id="assetForm">
                        <?php echo csrf_field(); ?>

                        <div class="row g-3">

                            
                            <div class="col-md-6">
                                <label class="form-label-custom d-flex align-items-center gap-1">
                                    <span><?php echo e(__('asset.type')); ?> <span class="text-danger">*</span></span>
                                    <span style="cursor:help" title="<?php echo e(__('asset.type_help')); ?>">
                                        <i class="bi bi-question-circle" style="font-size:12px; color:var(--text-muted)"></i>
                                    </span>
                                </label>
                                <select name="type" id="assetType"
                                    class="form-custom <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required
                                    onchange="toggleAssetFields()">
                                    <option value=""><?php echo e(__('general.select')); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($t->value); ?>"
                                            <?php echo e(old('type') === $t->value ? 'selected' : ''); ?>>
                                            <?php echo e($t->label()); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                                <div id="typeBadge" class="mt-1" style="min-height:22px"></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger mt-1" style="font-size:13px"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div class="col-md-6">
                                <label class="form-label-custom d-flex align-items-center gap-1">
                                    <span><?php echo e(__('asset.name')); ?> <span class="text-danger">*</span></span>
                                </label>
                                <input type="text" name="name" value="<?php echo e(old('name')); ?>"
                                    class="form-custom <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required maxlength="255">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger mt-1" style="font-size:13px"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div class="col-md-6" id="field-bank_name" style="display:none">
                                <label class="form-label-custom d-flex align-items-center gap-1">
                                    <span><?php echo e(__('asset.bank_name')); ?></span>
                                    <span style="cursor:help" title="<?php echo e(__('asset.bank_name_help')); ?>">
                                        <i class="bi bi-question-circle" style="font-size:12px; color:var(--text-muted)"></i>
                                    </span>
                                </label>
                                <input type="text" name="bank_name" value="<?php echo e(old('bank_name')); ?>"
                                    class="form-custom" maxlength="255">
                            </div>

                            <div class="col-md-6" id="field-account_number" style="display:none">
                                <label class="form-label-custom d-flex align-items-center gap-1">
                                    <span><?php echo e(__('asset.account_number')); ?></span>
                                    <span style="cursor:help" title="<?php echo e(__('asset.account_number_help')); ?>">
                                        <i class="bi bi-question-circle" style="font-size:12px; color:var(--text-muted)"></i>
                                    </span>
                                </label>
                                <div class="input-group">
                                    <input type="text" name="account_number" value="<?php echo e(old('account_number')); ?>"
                                        class="form-custom" maxlength="255">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:12px">
                                        <i class="bi bi-shield-lock"></i> <?php echo e(__('asset.security_notice')); ?>

                                    </span>
                                </div>
                            </div>

                            
                            <div class="col-md-6" id="field-karat" style="display:none">
                                <label class="form-label-custom d-flex align-items-center gap-1">
                                    <span><?php echo e(__('asset.karat')); ?></span>
                                    <span style="cursor:help" title="<?php echo e(__('asset.karat_help')); ?>">
                                        <i class="bi bi-question-circle" style="font-size:12px; color:var(--text-muted)"></i>
                                    </span>
                                </label>
                                <select name="karat" id="karat" class="form-custom" onchange="updateKaratBadge()">
                                    <option value="">--</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $karatPurity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $purity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($k); ?>"
                                            <?php echo e(old('karat', 21) == $k ? 'selected' : ''); ?>>
                                            <?php echo e($k); ?> <?php echo e(__('zakat.karat_' . $k)); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                                <div id="karatBadge" class="mt-1" style="min-height:22px"></div>
                            </div>

                            <div class="col-md-6" id="field-weight_grams" style="display:none">
                                <label class="form-label-custom d-flex align-items-center gap-1">
                                    <span><?php echo e(__('asset.weight_grams')); ?> (g)</span>
                                    <span style="cursor:help" title="<?php echo e(__('asset.weight_help')); ?>">
                                        <i class="bi bi-question-circle" style="font-size:12px; color:var(--text-muted)"></i>
                                    </span>
                                </label>
                                <input type="number" step="0.0001" min="0" name="weight_grams" id="weight_grams"
                                    value="<?php echo e(old('weight_grams')); ?>"
                                    class="form-custom <?php $__errorArgs = ['weight_grams'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="0.0000"
                                    oninput="calculateTotal()">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['weight_grams'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger mt-1" style="font-size:13px"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="col-md-6" id="field-quantity" style="display:none">
                                <label class="form-label-custom d-flex align-items-center gap-1">
                                    <span><?php echo e(__('asset.quantity')); ?></span>
                                    <span style="cursor:help" title="<?php echo e(__('asset.quantity_help')); ?>">
                                        <i class="bi bi-question-circle" style="font-size:12px; color:var(--text-muted)"></i>
                                    </span>
                                </label>
                                <input type="number" step="0.0001" min="0" name="quantity" id="quantity"
                                    value="<?php echo e(old('quantity')); ?>"
                                    class="form-custom <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="0"
                                    oninput="calculateTotal()">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger mt-1" style="font-size:13px"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="col-md-6" id="field-unit_price" style="display:none">
                                <label class="form-label-custom d-flex align-items-center gap-1">
                                    <span><?php echo e(__('asset.unit_price')); ?></span>
                                    <span style="cursor:help" title="<?php echo e(__('asset.unit_price_help')); ?>">
                                        <i class="bi bi-question-circle" style="font-size:12px; color:var(--text-muted)"></i>
                                    </span>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="unit_price" id="unit_price"
                                        value="<?php echo e(old('unit_price')); ?>"
                                        class="form-custom <?php $__errorArgs = ['unit_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="0.00" oninput="calculateTotal()">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px"><?php echo e(config('finance.currency_symbol')); ?></span>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['unit_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger mt-1" style="font-size:13px"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div class="col-md-6" id="field-total_value">
                                <label class="form-label-custom d-flex align-items-center gap-1">
                                    <span><?php echo e(__('asset.total_value')); ?></span>
                                    <span style="cursor:help" title="<?php echo e(__('asset.total_value_help')); ?>">
                                        <i class="bi bi-question-circle" style="font-size:12px; color:var(--text-muted)"></i>
                                    </span>
                                    <span id="autoCalcBadge" style="display:none; font-size:11px; padding:1px 6px; border-radius:4px; background:rgba(34,197,94,0.1); color:#16a34a; font-weight:500">
                                        <i class="bi bi-calculator"></i> <?php echo e(__('asset.auto_calculated')); ?>

                                    </span>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="total_value"
                                        id="total_value" value="<?php echo e(old('total_value')); ?>"
                                        class="form-custom <?php $__errorArgs = ['total_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="0.00">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px"><?php echo e(config('finance.currency_symbol')); ?></span>
                                </div>
                                <div id="totalPreview" class="mt-1" style="font-size:12px; color:var(--text-muted); display:none"></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['total_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger mt-1" style="font-size:13px"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['name' => 'is_liquid','checked' => old('is_liquid', '1'),'label' => ''.e(__('asset.is_liquid')).'','hint' => ''.e(__('asset.liquid_help')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'is_liquid','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('is_liquid', '1')),'label' => ''.e(__('asset.is_liquid')).'','hint' => ''.e(__('asset.liquid_help')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal319c173192d983146c5bd67854bb9452)): ?>
<?php $attributes = $__attributesOriginal319c173192d983146c5bd67854bb9452; ?>
<?php unset($__attributesOriginal319c173192d983146c5bd67854bb9452); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal319c173192d983146c5bd67854bb9452)): ?>
<?php $component = $__componentOriginal319c173192d983146c5bd67854bb9452; ?>
<?php unset($__componentOriginal319c173192d983146c5bd67854bb9452); ?>
<?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['name' => 'is_zakatable','checked' => old('is_zakatable', '1'),'label' => ''.e(__('asset.is_zakatable')).'','hint' => ''.e(__('asset.zakatable_help')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'is_zakatable','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('is_zakatable', '1')),'label' => ''.e(__('asset.is_zakatable')).'','hint' => ''.e(__('asset.zakatable_help')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal319c173192d983146c5bd67854bb9452)): ?>
<?php $attributes = $__attributesOriginal319c173192d983146c5bd67854bb9452; ?>
<?php unset($__attributesOriginal319c173192d983146c5bd67854bb9452); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal319c173192d983146c5bd67854bb9452)): ?>
<?php $component = $__componentOriginal319c173192d983146c5bd67854bb9452; ?>
<?php unset($__componentOriginal319c173192d983146c5bd67854bb9452); ?>
<?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="col-12">
                                <label class="form-label-custom"><?php echo e(__('asset.description')); ?></label>
                                <textarea name="description" class="form-custom" rows="2" maxlength="1000"><?php echo e(old('description')); ?></textarea>
                            </div>

                            
                            <div class="col-12">
                                <label class="form-label-custom"><?php echo e(__('asset.notes')); ?></label>
                                <textarea name="notes" class="form-custom" rows="2" maxlength="1000"><?php echo e(old('notes')); ?></textarea>
                            </div>
                        </div>

                        
                        <div class="d-flex gap-3 mt-4 pt-3" style="border-top:1px solid var(--border)">
                            <button type="submit" class="btn btn-accent btn-custom">
                                <i class="bi bi-check-lg me-1"></i><?php echo e(__('general.save')); ?>

                            </button>
                            <a href="<?php echo e(route('asset.index')); ?>" class="btn btn-outline-secondary btn-custom">
                                <i class="bi bi-x-lg me-1"></i><?php echo e(__('general.cancel')); ?>

                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        const karatPurity = <?php echo json_encode($karatPurity, 15, 512) ?>;
        const currencySymbol = <?php echo json_encode(config('finance.currency_symbol'), 15, 512) ?>;
        const assetTypes = <?php echo json_encode($assetTypeMap, 15, 512) ?>;

        function toggleAssetFields() {
            var type = document.getElementById('assetType')?.value;
            var info = assetTypes[type] || null;

            document.getElementById('field-bank_name').style.display = (type === 'bank_account' || type === 'ccp') ? 'block' : 'none';
            document.getElementById('field-account_number').style.display = (type === 'bank_account' || type === 'ccp') ? 'block' : 'none';
            document.getElementById('field-karat').style.display = type === 'gold' ? 'block' : 'none';
            document.getElementById('field-weight_grams').style.display = (type === 'gold' || type === 'silver') ? 'block' : 'none';
            document.getElementById('field-quantity').style.display = (type === 'gold' || type === 'silver') ? 'block' : 'none';
            document.getElementById('field-unit_price').style.display = (type === 'gold' || type === 'silver') ? 'block' : 'none';

            var badge = document.getElementById('typeBadge');
            if (info && badge) {
                badge.innerHTML = '<span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;padding:3px 8px;border-radius:6px;background:' + info.color + '15;color:' + info.color + ';font-weight:500"><i class="bi bi-' + info.icon + '"></i>' +
                    info.label + (info.zakatable ? ' · <i class="bi bi-check-circle-fill" style="font-size:10px"></i>' : '') + '</span>';
            } else if (badge) {
                badge.innerHTML = '';
            }

            if (type !== 'gold' && type !== 'silver') {
                document.getElementById('autoCalcBadge').style.display = 'none';
            }

            updateKaratBadge();
            calculateTotal();
        }

        function updateKaratBadge() {
            var karat = document.getElementById('karat')?.value;
            var badge = document.getElementById('karatBadge');
            if (!karat || !karatPurity[karat] || !badge) {
                if (badge) badge.innerHTML = '';
                return;
            }
            var pct = (karatPurity[karat] * 100).toFixed(1);
            badge.innerHTML = '<span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;padding:3px 8px;border-radius:6px;background:rgba(255,193,7,0.1);color:#f59e0b;font-weight:500"><i class="bi bi-gem"></i>' + karat + 'K — ' + pct + '% <?php echo e(__("asset.purity")); ?></span>';
        }

        function calculateTotal() {
            var type = document.getElementById('assetType')?.value;
            var totalInput = document.getElementById('total_value');
            var autoBadge = document.getElementById('autoCalcBadge');
            var preview = document.getElementById('totalPreview');

            if (type !== 'gold' && type !== 'silver') {
                autoBadge.style.display = 'none';
                preview.style.display = 'none';
                return;
            }

            var quantity = parseFloat(document.getElementById('quantity')?.value) || 0;
            var unitPrice = parseFloat(document.getElementById('unit_price')?.value) || 0;
            var weight = parseFloat(document.getElementById('weight_grams')?.value) || 0;

            var calculated = 0;
            var formula = '';

            if (quantity > 0 && unitPrice > 0) {
                calculated = quantity * unitPrice;
                formula = quantity + ' × ' + unitPrice;
            } else if (weight > 0 && unitPrice > 0) {
                calculated = weight * unitPrice;
                formula = weight + 'g × ' + unitPrice;
            }

            if (calculated > 0) {
                totalInput.value = calculated.toFixed(2);
                autoBadge.style.display = 'inline';
                preview.style.display = 'block';
                preview.innerHTML = '<i class="bi bi-check-circle" style="color:#16a34a"></i> ' + formula + ' = <strong>' + currencySymbol + ' ' + calculated.toFixed(2) + '</strong>';
            } else {
                autoBadge.style.display = 'none';
                preview.style.display = 'none';
            }
        }

        toggleAssetFields();
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\asset\create.blade.php ENDPATH**/ ?>