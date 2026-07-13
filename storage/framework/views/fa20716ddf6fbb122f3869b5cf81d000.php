<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['items' => []]));

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

foreach (array_filter((['items' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div x-data="commandPalette()" x-init="items = <?php echo e(json_encode($items)); ?>"
     x-show="open" x-cloak
     class="command-palette-overlay"
     @click="open = false"
     x-transition.opacity
     role="dialog"
     aria-modal="true"
     aria-label="<?php echo e(__('general.search')); ?>">
    <div class="command-palette" @click.stop
         @keydown.down.prevent="selectedIndex = Math.min(selectedIndex + 1, filteredItems.length - 1)"
         @keydown.up.prevent="selectedIndex = Math.max(selectedIndex - 1, 0)"
         @keydown.enter.prevent="executeCommand(selectedIndex)">
        <div class="command-palette-input">
            <i class="bi bi-search"></i>
            <input type="text" x-ref="searchInput" x-model="searchQuery"
                   placeholder="<?php echo e(__('general.search')); ?>...">
            <kbd>ESC</kbd>
        </div>
        <div class="command-palette-results" x-show="filteredItems.length > 0">
            <template x-for="(item, index) in filteredItems" :key="index">
                <a :href="item.url" class="command-palette-item"
                   :class="{ 'active': selectedIndex === index }"
                   @mouseenter="selectedIndex = index"
                   wire:navigate>
                    <i :class="item.icon"></i>
                    <div class="command-palette-item-text">
                        <span x-text="item.title"></span>
                        <small x-text="item.description"></small>
                    </div>
                </a>
            </template>
        </div>
        <div class="command-palette-empty" x-show="searchQuery.length > 0 && filteredItems.length === 0">
            <i class="bi bi-search"></i>
            <p><?php echo e(__('general.no_results')); ?></p>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/components/command-palette.blade.php ENDPATH**/ ?>