<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'entity' => '',
    'entityLabel' => '',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'entity' => '',
    'entityLabel' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo e(__('general.import')); ?> <?php echo e($entityLabel); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo e(route('data.import', ['entity' => $entity])); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-custom"><?php echo e(__('general.import_file')); ?></label>
                        <input type="file" name="file" class="form-custom w-100" accept=".xlsx,.csv" required>
                    </div>
                    <div style="font-size:12px; color:var(--text-muted)">
                        <i class="bi bi-info-circle me-1"></i>
                        <?php echo e(__('general.import_hint')); ?>

                        <a href="<?php echo e(route('data.template', ['entity' => $entity])); ?>" class="ms-1" style="color:var(--accent)">
                            <i class="bi bi-download me-1"></i><?php echo e(__('general.download_template')); ?>

                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-custom" data-bs-dismiss="modal"><?php echo e(__('general.cancel')); ?></button>
                    <button type="submit" class="btn btn-sm btn-accent btn-custom"><?php echo e(__('general.import')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\components\import-modal.blade.php ENDPATH**/ ?>