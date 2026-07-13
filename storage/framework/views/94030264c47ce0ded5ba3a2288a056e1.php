<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'entity' => '',
    'routes' => [],
    'showPrint' => true,
    'showExport' => true,
    'showImport' => false,
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
    'routes' => [],
    'showPrint' => true,
    'showExport' => true,
    'showImport' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="d-flex gap-2 align-items-center">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPrint): ?>
        <button type="button" class="btn btn-sm btn-outline-secondary btn-custom d-print-none" @click="window.print()" title="<?php echo e(__('general.print')); ?>">
            <i class="bi bi-printer"></i>
        </button>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showExport): ?>
        <div class="dropdown d-print-none">
            <button class="btn btn-sm btn-outline-secondary btn-custom dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="min-width:120px">
                <li>
                    <a class="dropdown-item" href="<?php echo e(route('data.export', ['entity' => $entity, 'format' => 'xlsx']) . '?' . http_build_query(request()->query())); ?>">
                        <i class="bi bi-file-earmark-excel me-2"></i>Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?php echo e(route('data.export', ['entity' => $entity, 'format' => 'csv']) . '?' . http_build_query(request()->query())); ?>">
                        <i class="bi bi-file-earmark-text me-2"></i>CSV
                    </a>
                </li>
            </ul>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showImport): ?>
        <button type="button" class="btn btn-sm btn-outline-secondary btn-custom d-print-none" data-bs-toggle="modal" data-bs-target="#importModal" title="<?php echo e(__('general.import')); ?>">
            <i class="bi bi-upload"></i>
        </button>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php echo e($slot ?? ''); ?>

</div>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/components/data-toolbar.blade.php ENDPATH**/ ?>