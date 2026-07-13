<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'accent',
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'left',
    'submit' => false,
    'href' => null,
    'disabled' => false,
    'loading' => false,
    'block' => false,
    'class' => '',
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
    'variant' => 'accent',
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'left',
    'submit' => false,
    'href' => null,
    'disabled' => false,
    'loading' => false,
    'block' => false,
    'class' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $sizeClasses = [
        'sm' => 'btn-sm',
        'md' => '',
        'lg' => 'btn-lg',
    ];

    $variantClasses = [
        'accent' => 'btn-accent',
        'outline' => 'btn-outline-secondary',
        'outline-accent' => 'btn-outline-accent',
        'danger' => 'btn-danger',
        'success' => 'btn-success',
        'outline-danger' => 'btn-outline-danger',
        'outline-success' => 'btn-outline-success',
        'ghost' => 'btn-ghost',
    ];

    $iconFirst = $iconPosition === 'left';
    $hasText = $slot !== null && trim(strip_tags($slot->toHtml())) !== '';
    $classes = 'btn btn-custom ' . ($variantClasses[$variant] ?? 'btn-accent') . ' ' . ($sizeClasses[$size] ?? '');
    if ($block) { $classes .= ' btn-block-mobile'; }
    if ($loading) { $classes .= ' btn-submitting'; }
    if ($class) { $classes .= ' ' . $class; }

    $tag = $href ? 'a' : 'button';
    $type = $submit ? 'submit' : 'button';
    $hrefAttr = $href ? "href={$href}" : '';
    $disabledAttr = $disabled && $tag === 'button' ? 'disabled' : '';
?>

<<?php echo e($tag); ?> <?php echo e($hrefAttr); ?> type="<?php echo e($tag === 'button' ? $type : ''); ?>" <?php echo e($disabledAttr); ?> <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($loading): ?><span style="visibility:hidden;display:inline-flex;align-items:center;gap:8px"><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($icon && $iconFirst): ?><i class="<?php echo e($icon); ?>"></i><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasText): ?><span><?php echo e($slot); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($icon && !$iconFirst): ?><i class="<?php echo e($icon); ?>"></i><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($loading): ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</<?php echo e($tag); ?>>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/components/button.blade.php ENDPATH**/ ?>