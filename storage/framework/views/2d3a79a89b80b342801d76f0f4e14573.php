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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('budget.edit')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('budget.edit')); ?> <?php $__env->endSlot(); ?>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-custom">
                <div class="card-body p-4">
                    <form action="<?php echo e(route('budget.update', $budget)); ?>" method="POST">
                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label-custom"><?php echo e(__('budget.single')); ?> (<?php echo e(__('general.ar')); ?>) <span class="text-danger">*</span></label>
                                <input type="text" name="name_ar" value="<?php echo e(old('name_ar', $budget->name_ar)); ?>" class="form-custom <?php $__errorArgs = ['name_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger mt-1" style="font-size:13px"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom"><?php echo e(__('budget.single')); ?> (<?php echo e(__('general.fr')); ?>)</label>
                                <input type="text" name="name_fr" value="<?php echo e(old('name_fr', $budget->name_fr)); ?>" class="form-custom <?php $__errorArgs = ['name_fr'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name_fr'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger mt-1" style="font-size:13px"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom"><?php echo e(__('budget.single')); ?> (<?php echo e(__('general.en')); ?>)</label>
                                <input type="text" name="name_en" value="<?php echo e(old('name_en', $budget->name_en)); ?>" class="form-custom <?php $__errorArgs = ['name_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger mt-1" style="font-size:13px"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-custom"><?php echo e(__('budget.type')); ?> <span class="text-danger">*</span></label>
                                <select name="type" class="form-custom <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="monthly" <?php echo e(old('type', $budget->type) === 'monthly' ? 'selected' : ''); ?>><?php echo e(__('budget.monthly')); ?></option>
                                    <option value="yearly" <?php echo e(old('type', $budget->type) === 'yearly' ? 'selected' : ''); ?>><?php echo e(__('budget.yearly')); ?></option>
                                    <option value="custom" <?php echo e(old('type', $budget->type) === 'custom' ? 'selected' : ''); ?>><?php echo e(__('budget.custom')); ?></option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-custom"><?php echo e(__('budget.total_amount')); ?> <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="total_amount" value="<?php echo e(old('total_amount', $budget->total_amount)); ?>" class="form-custom" required id="budget_total">
                                    <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:13px"><?php echo e(config('finance.currency_symbol')); ?></span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mt-4 pt-2">
                                    <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['name' => 'is_active','checked' => old('is_active', $budget->is_active),'label' => ''.e(__('budget.is_active')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'is_active','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('is_active', $budget->is_active)),'label' => ''.e(__('budget.is_active')).'']); ?>
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
                                <label class="form-label-custom"><?php echo e(__('budget.start_date')); ?> <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" value="<?php echo e(old('start_date', $budget->start_date->format('Y-m-d'))); ?>" class="form-custom" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom"><?php echo e(__('budget.end_date')); ?></label>
                                <input type="date" name="end_date" value="<?php echo e(old('end_date', $budget->end_date?->format('Y-m-d'))); ?>" class="form-custom">
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom"><?php echo e(__('budget.notes')); ?></label>
                                <textarea name="notes" class="form-custom" rows="2" maxlength="1000"><?php echo e(old('notes', $budget->notes)); ?></textarea>
                            </div>

                            <div class="col-12">
                                <hr>
                                <h5 class="fw-bold mb-3"><i class="bi bi-list-ul me-2"></i><?php echo e(__('budget.categories')); ?></h5>
                                <?php $selectedCatIds = $budget->categories->pluck('expense_category_id')->toArray(); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $existing = $budget->categories->firstWhere('expense_category_id', $cat->id);
                                        $allocated = old("categories.{$loop->index}.allocated_amount", $existing?->allocated_amount ?? '');
                                    ?>
                                    <div class="category-row d-flex align-items-center gap-3 mb-2 p-2" style="background:var(--bg); border-radius:8px">
                                        <span style="flex:1; font-size:14px">
                                            <i class="<?php echo e($cat->icon ?? 'bi-tag'); ?>" style="color:<?php echo e($cat->color ?? '#64748B'); ?>"></i>
                                            <?php echo e(locale_name($cat)); ?>

                                        </span>
                                        <div class="input-group" style="width:200px">
                                            <input type="hidden" name="categories[<?php echo e($loop->index); ?>][category_id]" value="<?php echo e($cat->id); ?>">
                                            <input type="number" step="0.01" min="0" name="categories[<?php echo e($loop->index); ?>][allocated_amount]" class="form-custom category-amount <?php $__errorArgs = ['categories.<?php echo e($loop->index); ?>.allocated_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="width:120px" placeholder="0.00" value="<?php echo e($allocated); ?>">
                                            <span class="input-group-text" style="background:var(--bg); border:1px solid var(--border); border-radius:0 8px 8px 0; color:var(--text-muted); font-size:11px"><?php echo e(config('finance.currency_symbol')); ?></span>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['categories.<?php echo e($loop->index); ?>.allocated_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger mt-1" style="font-size:13px"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="mt-2 p-2 d-flex justify-content-between" style="background:var(--bg); border-radius:8px; font-size:14px">
                                    <span class="fw-bold"><?php echo e(__('budget.total_amount')); ?></span>
                                    <span id="allocated-total" class="fw-bold" style="color:var(--accent)">0.00</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4 pt-3" style="border-top:1px solid var(--border)">
                            <button type="submit" class="btn btn-accent btn-custom">
                                <i class="bi bi-check-lg me-1"></i><?php echo e(__('general.save')); ?>

                            </button>
                            <a href="<?php echo e(route('budget.index')); ?>" class="btn btn-outline-secondary btn-custom">
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
    function updateAllocatedTotal() {
        let total = 0;
        document.querySelectorAll('.category-amount').forEach(function(input) {
            total += parseFloat(input.value) || 0;
        });
        var el = document.getElementById('allocated-total');
        if (el) el.textContent = total.toFixed(2);
    }
    function initBudgetForm() {
        document.querySelectorAll('.category-amount').forEach(function(input) {
            input.addEventListener('input', updateAllocatedTotal);
        });
        updateAllocatedTotal();
    }
    initBudgetForm();
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\budget\edit.blade.php ENDPATH**/ ?>