@props(['items' => []])

<div x-data="commandPalette()" x-init="items = {{ json_encode($items) }}"
     x-show="open" x-cloak
     class="command-palette-overlay"
     @click="open = false"
     x-transition.opacity
     role="dialog"
     aria-modal="true"
     aria-label="{{ __('general.search') }}">
    <div class="command-palette" @click.stop
         @keydown.down.prevent="selectedIndex = Math.min(selectedIndex + 1, filteredItems.length - 1)"
         @keydown.up.prevent="selectedIndex = Math.max(selectedIndex - 1, 0)"
         @keydown.enter.prevent="executeCommand(selectedIndex)">
        <div class="command-palette-input">
            <i class="bi bi-search"></i>
            <input type="text" x-ref="searchInput" x-model="searchQuery"
                   placeholder="{{ __('general.search') }}...">
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
            <p>{{ __('general.no_results') }}</p>
        </div>
    </div>
</div>
