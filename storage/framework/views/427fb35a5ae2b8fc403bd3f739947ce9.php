<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'tabs' => [],
    'current' => null,
    'style' => 'underline',
    'mode' => 'server',
    'route' => null,
    'preserve' => ['search', 'per_page'],
    'keyParam' => 'tab',
    'alpine' => null,
    'class' => '',
    'onTabClick' => null,
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
    'tabs' => [],
    'current' => null,
    'style' => 'underline',
    'mode' => 'server',
    'route' => null,
    'preserve' => ['search', 'per_page'],
    'keyParam' => 'tab',
    'alpine' => null,
    'class' => '',
    'onTabClick' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $wrapperClass = $style === 'pills' ? 'tabs-pills' : 'tabs-underline';
    if ($class) { $wrapperClass .= ' ' . $class; }
?>

<div class="tabs-wrapper <?php echo e($wrapperClass); ?>">
    <nav class="tabs-nav" role="tablist">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $isActive = $current === $key;
                $label = $config['label'] ?? $key;
                $icon = $config['icon'] ?? null;
                $count = $config['count'] ?? null;

                if ($mode === 'server') {
                    $queryParams = array_merge(
                        request()->except([$keyParam, ...$preserve]),
                        [$keyParam => $key]
                    );
                    $queryParams = array_filter($queryParams, fn($v) => $v !== '' && $v !== null);
                    $url = route($route ?? request()->route()->getName(), $queryParams);
                }
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'server'): ?>
                <a href="<?php echo e($url); ?>"
                    class="tabs-tab <?php echo e($isActive ? 'active' : ''); ?>"
                    role="tab"
                    aria-selected="<?php echo e($isActive ? 'true' : 'false'); ?>"
                >
            <?php else: ?>
                <button type="button"
                    class="tabs-tab <?php echo e($isActive ? 'active' : ''); ?>"
                    data-tab="<?php echo e($key); ?>"
                    role="tab"
                    aria-selected="<?php echo e($isActive ? 'true' : 'false'); ?>"
                    <?php if($alpine): ?>
                        @click="<?php echo e($alpine); ?> = '<?php echo e($key); ?>'"
                    <?php elseif($onTabClick): ?>
                        onclick="<?php echo e($onTabClick); ?>('<?php echo e($key); ?>')"
                    <?php endif; ?>
                >
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($icon): ?>
                    <i class="<?php echo e($icon); ?>"></i>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <span><?php echo e($label); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($count !== null): ?>
                    <span class="tabs-count"><?php echo e($count); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'server'): ?>
                </a>
            <?php else: ?>
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </nav>
</div>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/components/tabs.blade.php ENDPATH**/ ?>