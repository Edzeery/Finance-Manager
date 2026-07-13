<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'tabs' => [],
    'current' => 'all',
    'route' => null,
    'preserve' => ['search', 'per_page'],
    'keyParam' => 'tab',
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
    'current' => 'all',
    'route' => null,
    'preserve' => ['search', 'per_page'],
    'keyParam' => 'tab',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="filter-tabs-wrapper">
    <div class="filter-tabs-scroll">
        <nav class="filter-tabs" role="tablist">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isActive = $current === $key;
                    $queryParams = array_merge(
                        request()->except([$keyParam, ...$preserve]),
                        [$keyParam => $key]
                    );
                    
                    $queryParams = array_filter($queryParams, fn($v) => $v !== '' && $v !== null);
                    $url = route($route ?? request()->route()->getName(), $queryParams);
                    $count = $config['count'] ?? null;
                    $label = $config['label'] ?? $key;
                    $icon = $config['icon'] ?? null;
                ?>
                <a href="<?php echo e($url); ?>"
                    class="filter-tab <?php echo e($isActive ? 'active' : ''); ?>"
                    role="tab"
                    aria-selected="<?php echo e($isActive ? 'true' : 'false'); ?>"
                >
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($icon): ?>
                        <i class="<?php echo e($icon); ?>" style="font-size:14px"></i>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span><?php echo e($label); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($count !== null): ?>
                        <span class="filter-tab-count"><?php echo e($count); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </nav>
    </div>
</div>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/components/filter-tabs.blade.php ENDPATH**/ ?>