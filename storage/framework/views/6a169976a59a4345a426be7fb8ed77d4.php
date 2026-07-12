<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => '',
    'options' => [],
    'selected' => null,
    'placeholder' => '',
    'minWidth' => '120px',
    'wireModel' => null,
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
    'name' => '',
    'options' => [],
    'selected' => null,
    'placeholder' => '',
    'minWidth' => '120px',
    'wireModel' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $defaultClass = 'form-control grid-filter-sm';
    $mergedClass = trim($defaultClass . ' ' . $attributes->get('class', ''));
?>

<select
    <?php if($name): ?> name="<?php echo e($name); ?>" <?php endif; ?>
    <?php if($wireModel): ?> wire:model.live="<?php echo e($wireModel); ?>" <?php endif; ?>
    class="<?php echo e($mergedClass); ?>"
    style="width:auto;min-width:<?php echo e($minWidth); ?>;padding:7px 10px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--card-bg);color:var(--text);<?php echo e($attributes->get('style')); ?>"
    <?php echo e($attributes->except(['class', 'style'])); ?>

>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($placeholder): ?>
        <option value=""><?php echo e($placeholder); ?></option>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($value); ?>" <?php echo e((!is_null($selected) && $selected == $value) || (is_null($selected) && request($name) == $value) ? 'selected' : ''); ?>><?php echo e($label); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</select>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\components\select-filter.blade.php ENDPATH**/ ?>