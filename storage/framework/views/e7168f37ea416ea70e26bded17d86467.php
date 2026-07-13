<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'dropdown',     // dropdown, dropdown-bs, inline
    'showCode' => true,
    'triggerClass' => 'topbar-btn',
    'itemClass' => '',
    'dropdown' => 'end',
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
    'variant' => 'dropdown',     // dropdown, dropdown-bs, inline
    'showCode' => true,
    'triggerClass' => 'topbar-btn',
    'itemClass' => '',
    'dropdown' => 'end',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $locales = ['ar' => __('general.ar'), 'fr' => __('general.fr'), 'en' => __('general.en')];
    $current = app()->getLocale();
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant === 'dropdown'): ?>
    <div class="dropdown" x-data="{ langOpen: false }" @click.away="langOpen = false">
        <button class="<?php echo e($triggerClass); ?>" type="button" @click="langOpen = !langOpen" aria-label="<?php echo e(__('general.language')); ?>" title="<?php echo e(strtoupper($current)); ?>">
            <i class="bi bi-globe2"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-<?php echo e($dropdown); ?>" x-show="langOpen" style="display:none" x-transition>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <form method="POST" action="<?php echo e(route('locale.switch', $code)); ?>" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showCode): ?>
                                <span style="width:22px;height:22px;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;background:var(--bg-subtle);color:var(--text);text-transform:uppercase"><?php echo e($code); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span><?php echo e($name); ?></span>
                        </button>
                    </form>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>
    </div>
<?php elseif($variant === 'dropdown-bs'): ?>
    <div class="dropdown">
        <button class="<?php echo e($triggerClass); ?>" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-globe2"></i>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showCode): ?>
                <span class="d-none d-sm-inline ms-1 text-sm"><?php echo e(strtoupper($current)); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-<?php echo e($dropdown); ?> dropdown-custom">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <form method="POST" action="<?php echo e(route('locale.switch', $code)); ?>" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="dropdown-item"><?php echo e($name); ?></button>
                    </form>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>
    </div>
<?php elseif($variant === 'inline'): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <form method="POST" action="<?php echo e(route('locale.switch', $code)); ?>" class="d-inline">
            <?php echo csrf_field(); ?>
            <button type="submit" class="<?php echo e($itemClass); ?> <?php echo e($current === $code ? 'active' : ''); ?>"><?php echo e($showCode ? strtoupper($code) : $name); ?></button>
        </form>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/components/language-switcher.blade.php ENDPATH**/ ?>