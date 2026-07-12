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
     <?php $__env->slot('title', null, []); ?> <?php echo e(__('income.categories')); ?> - <?php echo e(config('app.name')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> <?php echo e(__('income.categories')); ?> <?php $__env->endSlot(); ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card-custom">
                <div class="card-body p-4">
                    <h5 class="mb-3" style="font-size:15px; font-weight:600"><?php echo e(__('general.add')); ?> <?php echo e(__('income.category')); ?></h5>
                    <form action="<?php echo e(route('income.categories.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label-custom mb-0 d-flex align-items-center gap-2"><?php echo e(__('general.name')); ?> <span class="badge-custom badge-status" style="background:rgba(255,193,7,0.12); color:var(--accent); font-size:10px">AR</span> <span class="text-danger">*</span></label>
                            <input type="text" name="name_ar" class="form-custom <?php $__errorArgs = ['name_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name_ar')); ?>" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom mb-0 d-flex align-items-center gap-2"><?php echo e(__('general.name')); ?> <span class="badge-custom badge-status" style="background:rgba(59,130,246,0.12); color:var(--info); font-size:10px">FR</span> <span class="text-danger">*</span></label>
                            <input type="text" name="name_fr" class="form-custom <?php $__errorArgs = ['name_fr'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name_fr')); ?>" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom mb-0 d-flex align-items-center gap-2"><?php echo e(__('general.name')); ?> <span class="badge-custom badge-status" style="background:rgba(34,197,94,0.12); color:var(--success); font-size:10px">EN</span> <span class="text-danger">*</span></label>
                            <input type="text" name="name_en" class="form-custom <?php $__errorArgs = ['name_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name_en')); ?>" required maxlength="255">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label-custom"><?php echo e(__('general.icon')); ?></label>
                                <input type="text" name="icon" class="form-custom" value="<?php echo e(old('icon', 'bi-currency-dollar')); ?>" maxlength="50" placeholder="bi-currency-dollar">
                            </div>
                            <div class="col-6">
                                <label class="form-label-custom"><?php echo e(__('general.color')); ?></label>
                                <input type="color" name="color" class="form-custom" value="<?php echo e(old('color', '#22C55E')); ?>" style="height:42px; padding:4px">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom"><?php echo e(__('income.type')); ?></label>
                            <select name="type" class="form-custom">
                                <option value="variable" <?php echo e(old('type') === 'variable' ? 'selected' : ''); ?>><?php echo e(__('income.variable')); ?></option>
                                <option value="fixed" <?php echo e(old('type') === 'fixed' ? 'selected' : ''); ?>><?php echo e(__('income.fixed')); ?></option>
                                <option value="recurring" <?php echo e(old('type') === 'recurring' ? 'selected' : ''); ?>><?php echo e(__('income.recurring')); ?></option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['name' => 'is_active','checked' => old('is_active', '1'),'label' => ''.e(__('general.active')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'is_active','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('is_active', '1')),'label' => ''.e(__('general.active')).'']); ?>
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
                        <button type="submit" class="btn btn-accent btn-custom w-100">
                            <i class="bi bi-plus-lg me-1"></i><?php echo e(__('general.add')); ?>

                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-body p-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categories->count()): ?>
                        <div class="table-responsive">
                            <table class="table-custom">
                            <thead>
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th><?php echo e(__('general.icon')); ?></th>
                                    <th><?php echo e(__('general.name')); ?></th>
                                    <th><?php echo e(__('income.type')); ?></th>
                                    <th><?php echo e(__('general.status')); ?></th>
                                    <th class="text-center" style="width:100px"><?php echo e(__('general.actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td style="color:var(--text-muted)"><?php echo e($loop->iteration); ?></td>
                                        <td>
                                            <span style="display:inline-flex; align-items:center; gap:6px">
                                                <i class="<?php echo e($cat->icon); ?>" style="color:<?php echo e($cat->color); ?>"></i>
                                                <span style="width:16px; height:16px; border-radius:4px; background:<?php echo e($cat->color); ?>; display:inline-block"></span>
                                            </span>
                                        </td>
                                        <td>
                                            <span style="font-weight:500"><?php echo e(locale_name($cat)); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-custom badge-income"><?php echo e(__("income.{$cat->type}")); ?></span>
                                        </td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cat->is_active): ?>
                                                <span class="badge-custom badge-status" style="background:rgba(34,197,94,0.1); color:var(--success); border:1px solid rgba(34,197,94,0.3)">
                                                    <?php echo e(__('general.active')); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="badge-custom badge-status" style="background:rgba(100,116,139,0.1); color:var(--text-muted); border:1px solid rgba(100,116,139,0.3)">
                                                    <?php echo e(__('general.inactive')); ?>

                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="action-group justify-content-center">
                                                <button class="action-btn" @click="editCategory(<?php echo e($cat->id); ?>)" title="<?php echo e(__('general.edit')); ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="action-btn" title="<?php echo e(__('general.delete')); ?>" @click="confirmDeleteCategory(<?php echo e($cat->id); ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                        </div>
                    <?php else: ?>
                        <?php echo $__env->make('components.empty-state', [
                            'icon' => 'bi-tag',
                            'title' => __('income.no_categories'),
                            'message' => __('income.create_first_category'),
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modal-custom">
                <form id="editForm" method="POST" data-categories='<?php echo json_encode($categories->items(), 15, 512) ?>'>
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo e(__('income.edit_category')); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label-custom mb-0 d-flex align-items-center gap-2"><?php echo e(__('general.name')); ?> <span class="badge-custom badge-status" style="background:rgba(255,193,7,0.12); color:var(--accent); font-size:10px">AR</span> <span class="text-danger">*</span></label>
                            <input type="text" name="name_ar" id="edit_name_ar" class="form-custom" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom mb-0 d-flex align-items-center gap-2"><?php echo e(__('general.name')); ?> <span class="badge-custom badge-status" style="background:rgba(59,130,246,0.12); color:var(--info); font-size:10px">FR</span> <span class="text-danger">*</span></label>
                            <input type="text" name="name_fr" id="edit_name_fr" class="form-custom" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom mb-0 d-flex align-items-center gap-2"><?php echo e(__('general.name')); ?> <span class="badge-custom badge-status" style="background:rgba(34,197,94,0.12); color:var(--success); font-size:10px">EN</span> <span class="text-danger">*</span></label>
                            <input type="text" name="name_en" id="edit_name_en" class="form-custom" required maxlength="255">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label-custom"><?php echo e(__('general.icon')); ?></label>
                                <input type="text" name="icon" id="edit_icon" class="form-custom" maxlength="50">
                            </div>
                            <div class="col-6">
                                <label class="form-label-custom"><?php echo e(__('general.color')); ?></label>
                                <input type="color" name="color" id="edit_color" class="form-custom" style="height:42px; padding:4px">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom"><?php echo e(__('income.type')); ?></label>
                            <select name="type" id="edit_type" class="form-custom">
                                <option value="variable"><?php echo e(__('income.variable')); ?></option>
                                <option value="fixed"><?php echo e(__('income.fixed')); ?></option>
                                <option value="recurring"><?php echo e(__('income.recurring')); ?></option>
                            </select>
                        </div>
                        <div>
                            <?php if (isset($component)) { $__componentOriginal319c173192d983146c5bd67854bb9452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal319c173192d983146c5bd67854bb9452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.toggle-switch','data' => ['name' => 'is_active','id' => 'edit_is_active','description' => ''.e(__('general.active')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'is_active','id' => 'edit_is_active','description' => ''.e(__('general.active')).'']); ?>
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-custom" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i><?php echo e(__('general.cancel')); ?></button>
                        <button type="submit" class="btn btn-accent btn-custom"><?php echo e(__('general.save')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <form id="delete-form-category-<?php echo e($cat->id); ?>" action="<?php echo e(route('income.categories.destroy', $cat)); ?>" method="POST" style="display:none">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        </form>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php $__env->startPush('scripts'); ?>
    <script>
    function confirmDeleteCategory(id) {
        showConfirmModal(
            '<?php echo e(__('general.confirm')); ?>',
            '<?php echo e(__('messages.confirm_delete')); ?>',
            (confirmed) => {
                if (confirmed) {
                    document.getElementById('delete-form-category-' + id)?.submit();
                }
            },
            '<?php echo e(__('general.delete')); ?>',
            'btn-danger'
        );
    }

    function editCategory(id) {
        var el = document.getElementById('editForm');
        var cats = el ? JSON.parse(el.dataset.categories) : [];
        var cat = cats.find(function(c) { return c.id == id; });
        if (!cat) return;
        document.getElementById('edit_name_ar').value = cat.name_ar;
        document.getElementById('edit_name_fr').value = cat.name_fr;
        document.getElementById('edit_name_en').value = cat.name_en;
        document.getElementById('edit_icon').value = cat.icon || 'bi-currency-dollar';
        document.getElementById('edit_color').value = cat.color || '#22C55E';
        document.getElementById('edit_type').value = cat.type;
        setToggle('edit_is_active', Boolean(cat.is_active));
        document.getElementById('editForm').action = '<?php echo e(route('income.categories.update', ':id')); ?>'.replace(':id', id);
        const modalEl = document.getElementById('editModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    }
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\income\categories.blade.php ENDPATH**/ ?>