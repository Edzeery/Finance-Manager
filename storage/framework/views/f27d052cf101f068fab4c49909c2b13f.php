<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'wireModel' => null,       // Livewire property name (e.g. 'search', 'couponSearch')
    'placeholder' => 'ابحث......',
    'size' => '',              // '' | 'sm' | 'xs'  => grid-filter / grid-filter-sm / grid-filter-xs
    'debounce' => '300ms',
    'icon' => 'bi-search',
    'name' => null,            // Form input name (for non-Livewire forms)
    'value' => null,           // Initial value (for non-Livewire forms)
    'minWidth' => '180px',     // Container min-width
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
    'wireModel' => null,       // Livewire property name (e.g. 'search', 'couponSearch')
    'placeholder' => 'ابحث......',
    'size' => '',              // '' | 'sm' | 'xs'  => grid-filter / grid-filter-sm / grid-filter-xs
    'debounce' => '300ms',
    'icon' => 'bi-search',
    'name' => null,            // Form input name (for non-Livewire forms)
    'value' => null,           // Initial value (for non-Livewire forms)
    'minWidth' => '180px',     // Container min-width
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $sizeClass = $size ? 'grid-filter-' . $size : '';
    $id = $attributes->get('id', $name ? 'search-' . $name : null);
?>

<div
    <?php if($id): ?> id="<?php echo e($id); ?>-wrapper" <?php endif; ?>
    class="<?php echo e($attributes->get('class')); ?> position-relative grid-filter <?php echo e($sizeClass); ?>"
    style="min-width:<?php echo e($minWidth); ?>"
>
    <i
        class="bi <?php echo e($icon); ?>"
        style="
            position:absolute;
            inset-inline-start:10px;
            top:50%;
            transform:translateY(-50%);
            font-size:13px;
            color:var(--text-muted);
            pointer-events:none;
        "
        aria-hidden="true"
    ></i>

    <input
        type="text"
        <?php if($wireModel): ?>
            wire:model.live.debounce.<?php echo e($debounce); ?>="<?php echo e($wireModel); ?>"
        <?php else: ?>
            name="<?php echo e($name ?? 'input'); ?>"
            <?php if(!is_null($value)): ?> value="<?php echo e($value); ?>" <?php endif; ?>
        <?php endif; ?>
        autocomplete="off"
        class="form-control"
        style="
            padding-block:7px;
            padding-inline:34px 14px;
            font-size:13px;
            border:1px solid var(--border);
            border-radius:var(--radius-sm);
            background:var(--card-bg);
            color:var(--text);
            width:100%;
        "
        placeholder="<?php echo e($placeholder); ?>"
    >
</div>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\components\search-filter.blade.php ENDPATH**/ ?>