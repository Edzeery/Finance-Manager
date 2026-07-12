<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'dropdown-bs',
    'triggerClass' => 'guest-navbar-btn',
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
    'variant' => 'dropdown-bs',
    'triggerClass' => 'guest-navbar-btn',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $current = session('currency', auth()->user()?->currency ?? config('finance.currency', 'DZD'));
    $currencies = config('finance.currencies', ['DZD' => [], 'USD' => [], 'EUR' => []]);
?>

<div class="dropdown">
    <button class="<?php echo e($triggerClass); ?>" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-currency-exchange"></i>
        <span class="d-none d-sm-inline ms-1" style="font-size:11px;font-weight:600"><?php echo e($current); ?></span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end dropdown-custom">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <form method="POST" action="<?php echo e(route('currency.switch', $code)); ?>" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="dropdown-item"><?php echo e($info['symbol'] ?? $code); ?> <?php echo e($code); ?></button>
                </form>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </ul>
</div>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\components\currency-switcher.blade.php ENDPATH**/ ?>