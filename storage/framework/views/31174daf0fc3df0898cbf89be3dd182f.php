<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'periods' => [],
    'currentPeriod' => 'this_month',
    'startDate' => null,
    'endDate' => null,
    'route' => null,
    'preserve' => [],
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
    'periods' => [],
    'currentPeriod' => 'this_month',
    'startDate' => null,
    'endDate' => null,
    'route' => null,
    'preserve' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php $baseUrl = $route ?? request()->url(); ?>

<div class="date-filter-bar" x-data="dateFilterBar(<?php echo \Illuminate\Support\Js::from($currentPeriod)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($startDate)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($endDate)->toHtml() ?>)">
    <div class="d-flex flex-wrap align-items-center gap-2">
        <div class="filter-periods d-flex gap-1 flex-wrap">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($key === 'custom'): ?>
                    <button type="button"
                        class="filter-period-btn"
                        :class="{ 'active': period === 'custom' }"
                        x-on:click="setCustom()"
                    >
                        <?php echo e(__("filters.{$key}")); ?>

                    </button>
                <?php else: ?>
                    <a href="<?php echo e($baseUrl); ?>?period=<?php echo e($key); ?>"
                        class="filter-period-btn <?php echo e($currentPeriod === $key ? 'active' : ''); ?>"
                        role="tab"
                        aria-selected="<?php echo e($currentPeriod === $key ? 'true' : 'false'); ?>"
                    >
                        <?php echo e(__("filters.{$key}")); ?>

                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="filter-custom-range d-flex align-items-center gap-2"
            :class="{ 'd-none': period !== 'custom' }" x-cloak>
            <div class="filter-date-field">
                <svg class="filter-date-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <input type="date" x-model="startDate" class="form-custom">
            </div>
            <span class="filter-date-sep"><?php echo e(__('general.to')); ?></span>
            <div class="filter-date-field">
                <svg class="filter-date-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <input type="date" x-model="endDate" class="form-custom">
            </div>
            <template x-if="startDate && endDate">
                <a x-bind:href="'<?php echo e($baseUrl); ?>?period=custom&start_date=' + startDate + '&end_date=' + endDate"
                    class="btn btn-accent btn-sm px-3">
                    <i class="bi bi-check-lg"></i>
                    <span><?php echo e(__('filters.apply')); ?></span>
                </a>
            </template>
        </div>
    </div>

</div>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\components\date-filter-bar.blade.php ENDPATH**/ ?>