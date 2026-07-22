@props([
    'name' => 'icon',
    'value' => 'bi-cart',
    'id' => null,
])

@php
    $id = $id ?? 'icon_picker_' . uniqid();
@endphp

<div class="icon-picker-wrap" x-data="iconPicker_{{ $id }}()" x-init="init()" @click.away="open = false">
    <label class="form-label-custom">{{ __('general.icon') }}</label>
    <input type="hidden" name="{{ $name }}" :value="selected" id="{{ $id }}">
    <button type="button" class="form-custom icon-picker-trigger" @click="open = !open" :style="'text-align:left; display:flex; align-items:center; gap:8px;'">
        <i :class="selected" class="icon-picker-preview"></i>
        <span x-text="selected" style="flex:1; font-size:13px; color:var(--text-muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"></span>
        <i class="bi bi-chevron-down" style="font-size:11px; color:var(--text-muted);"></i>
    </button>

    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="icon-picker-dropdown" style="display:none;">
        <div class="icon-picker-search">
            <i class="bi bi-search"></i>
            <input type="text" x-model="query" @input="filter()" placeholder="Search icons..." class="icon-picker-search-input">
        </div>
        <div class="icon-picker-grid" x-ref="grid">
            <template x-for="icon in filtered" :key="icon">
                <button type="button" class="icon-picker-item" :class="selected === icon ? 'active' : ''" @click="select(icon)" :title="icon">
                    <i :class="icon"></i>
                </button>
            </template>
        </div>
        <div x-show="filtered.length === 0" style="text-align:center; padding:16px; color:var(--text-muted); font-size:13px;">
            {{ __('general.no_results') }}
        </div>
    </div>
</div>

@once
@push('styles')
<style>
.icon-picker-wrap { position: relative; }
.icon-picker-trigger {
    cursor: pointer;
    min-height: 42px;
}
.icon-picker-preview {
    font-size: 18px;
    flex-shrink: 0;
    width: 24px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.icon-picker-dropdown {
    position: absolute;
    z-index: 1050;
    top: 100%;
    left: 0;
    right: 0;
    margin-top: 4px;
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm, 8px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    overflow: hidden;
}
.icon-picker-search {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 10px;
    border-bottom: 1px solid var(--border);
}
.icon-picker-search i { font-size: 14px; color: var(--text-muted); flex-shrink: 0; }
.icon-picker-search-input {
    border: none;
    outline: none;
    background: transparent;
    font-size: 13px;
    color: var(--text);
    width: 100%;
    padding: 4px 0;
}
.icon-picker-search-input::placeholder { color: var(--text-muted); }
.icon-picker-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 2px;
    max-height: 220px;
    overflow-y: auto;
    padding: 6px;
}
.icon-picker-item {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    aspect-ratio: 1;
    border: 2px solid transparent;
    border-radius: var(--radius-sm, 6px);
    background: transparent;
    cursor: pointer;
    transition: all 0.12s;
    font-size: 17px;
    color: var(--text);
}
.icon-picker-item:hover {
    background: var(--hover-bg, rgba(99,102,241,0.08));
    border-color: var(--border);
}
.icon-picker-item.active {
    background: var(--accent-light, rgba(99,102,241,0.12));
    border-color: var(--accent);
    color: var(--accent);
}
@media (max-width: 576px) {
    .icon-picker-grid { grid-template-columns: repeat(5, 1fr); }
}
</style>
@endpush
@endonce

@push('scripts')
<script>
if (typeof iconPicker_{{ $id }} !== 'function') {
    function iconPicker_{{ $id }}() {
        return {
            open: false,
            selected: '{{ addslashes($value) }}',
            query: '',
            icons: [
                'bi-cart','bi-cart-check','bi-cart-plus','bi-cart-x','bi-bag','bi-bag-check','bi-bag-plus','bi-bag-x',
                'bi-cash','bi-cash-coin','bi-cash-stack','bi-coin','bi-coins','bi-currency-bitcoin','bi-currency-dollar','bi-currency-euro','bi-currency-pound','bi-currency-yen',
                'bi-wallet2','bi-credit-card','bi-credit-card-2','bi-credit-card-2-front','bi-credit-card-back',
                'bi-bank','bi-bank2','bi-safe','bi-safe2','bi-vault',
                'bi-graph-up','bi-graph-down','bi-graph-up-arrow','bi-graph-down-arrow','bi-bar-chart','bi-bar-chart-line','bi-bar-chart-steps',
                'bi-pie-chart','bi-pie-chart-fill','bi-pie-chart-half',
                'bi-calculator','bi-cash-register','bi-receipt','bi-receipt-cutoff',
                'bi-arrow-up-right','bi-arrow-down-left','bi-arrow-up','bi-arrow-down','bi-arrow-left','bi-arrow-right',
                'bi-arrow-up-circle','bi-arrow-down-circle','bi-arrow-left-circle','bi-arrow-right-circle',
                'bi-plus-circle','bi-plus-lg','bi-dash-circle','bi-dash-lg','bi-x-circle','bi-x-lg',
                'bi-check-circle','bi-check-lg','bi-check','bi-check-all',
                'bi-house','bi-house-fill','bi-house-door','bi-house-gear','bi-building','bi-buildings',
                'bi-car-front','bi-car-front-fill','bi-bus-front','bi-truck','bi-fuel-pump','bi-fuel-pump-diesel',
                'bi-train-front','bi-airplane','bi-ship','bi-bicycle','bi-moped','bi-scooter',
                'bi-heart','bi-heart-fill','bi-heart-pulse','bi-heartbreak','bi-suit-heart',
                'bi-person','bi-person-fill','bi-people','bi-people-fill','bi-person-check','bi-person-plus',
                'bi-gender-male','bi-gender-female','bi-gender-trans',
                'bi-phone','bi-phone-fill','bi-phone-vibrate','bi-telephone','bi-telephone-fill',
                'bi-laptop','bi-desktop','bi-tablet','bi-smartwatch','bi-headphones','bi-speaker','bi-keyboard',
                'bi-tv','bi-camera','bi-camera-video','bi-image','bi-images','bi-film',
                'bi-food-apple','bi-cup-hot','bi-cup','bi-cake','bi-cake2','bi-egg','bi-bowl',
                'bi-shop','bi-shop-window','bi-store','bi-bag-dash',
                'bi-gift','bi-gift-fill','bi-box','bi-box2','bi-archive',
                'bi-tag','bi-tags','bi-bookmark','bi-bookmark-fill','bi-bookmark-star',
                'bi-star','bi-star-fill','bi-star-half','bi-stars',
                'bi-trophy','bi-award','bi-medal','bi-patch-check','bi-patch-question',
                'bi-lightning','bi-lightning-fill','bi-lightbulb','bi-lightbulb-fill','bi-lightbulb-off',
                'bi-sun','bi-sun-fill','bi-moon','bi-moon-fill','bi-cloud','bi-cloud-fill','bi-cloud-rain','bi-cloud-snow',
                'bi-umbrella','bi-snow','bi-thermometer-snow','bi-thermometer-sun',
                'bi-droplet','bi-droplet-fill','bi-droplets','bi-water',
                'bi-fire','bi-fire-fill','bi-smoke',
                'bi-tree','bi-tree-fill','bi-flower1','bi-flower2','bi-flower3','bi-leaf',
                'bi-globe','bi-globe2','bi-geo-alt','bi-geo-alt-fill','bi-map','bi-pin','bi-pin-fill',
                'bi-compass','bi-signpost','bi-signpost-2','bi-signpost-split',
                'bi-flag','bi-flag-fill','bi-flag2','bi-compass',
                'bi-bell','bi-bell-fill','bi-bell-slash','bi-bell-slash-fill','bi-bell-ring',
                'bi-envelope','bi-envelope-fill','bi-envelope-open','bi-inbox','bi-inbox-fill',
                'bi-chat','bi-chat-fill','bi-chat-dots','bi-chat-dots-fill','bi-chat-left','bi-chat-left-dots',
                'bi-megaphone','bi-megaphone-fill','bi-mic','bi-mic-fill','bi-mic-mute',
                'bi-volume-up','bi-volume-down','bi-volume-mute','bi-volume-off',
                'bi-camera-video','bi-camera-reels','bi-camera-fill',
                'bi-pencil','bi-pencil-fill','bi-pencil-square','bi-eraser','bi-pen',
                'bi-trash','bi-trash-fill','bi-trash2','bi-trash2-fill','bi-recycle',
                'bi-scissors','bi-clipboard','bi-clipboard-check','bi-clipboard-plus','bi-clipboard-x',
                'bi-file-earmark','bi-file-earmark-fill','bi-file-earmark-plus','bi-file-earmark-x',
                'bi-file-earmark-text','bi-file-earmark-spreadsheet','bi-file-earmark-pdf',
                'bi-folder','bi-folder-fill','bi-folder-plus','bi-folder-minus','bi-folder-check','bi-folder-x',
                'bi-hdd','bi-hdd-stack','bi-usb','bi-printer','bi-scanner',
                'bi-download','bi-upload','bi-cloud-download','bi-cloud-upload',
                'bi-arrow-repeat','bi-arrow-clockwise','bi-arrow-counterclockwise','bi-shuffle',
                'bi-sort-down','bi-sort-up','bi-sort-alpha-down','bi-sort-numeric-down',
                'bi-filter','bi-funnel','bi-funnel-fill','bi-x-lg',
                'bi-search','bi-zoom-in','bi-zoom-out',
                'bi-gear','bi-gear-fill','bi-gear-wide','bi-gear-wide-connected','bi-sliders',
                'bi-wrench','bi-wrench-adjustable','bi-screwdriver','bi-hammer','bi-tools',
                'bi-puzzle','bi-controller','bi-joystick','bi-dice-1','bi-dice-2','bi-dice-3','bi-dice-4','bi-dice-5','bi-dice-6',
                'bi-database','bi-server','bi-cloud','bi-cloudy','bi-sd-card',
                'bi-shield','bi-shield-fill','bi-shield-check','bi-shield-exclamation','bi-shield-lock',
                'bi-lock','bi-lock-fill','bi-unlock','bi-unlock-fill','bi-key','bi-key-fill',
                'bi-eye','bi-eye-fill','bi-eye-slash','bi-eye-slash-fill',
                'bi-people-circle','bi-person-badge','bi-person-workspace','bi-person-gear',
                'bi-calendar','bi-calendar-fill','bi-calendar-check','bi-calendar-event','bi-calendar-date','bi-calendar-week',
                'bi-clock','bi-clock-fill','bi-clock-history','bi-stopwatch','bi-stopwatch-fill',
                'bi-hourglass','bi-hourglass-split','bi-hourglass-bottom',
                'bi-alarm','bi-alarm-fill','bi-stop-circle','bi-play-circle','bi-pause-circle',
                'bi-skip-forward','bi-skip-backward','bi-rewind','bi-fast-forward',
                'bi-shield-fill-check','bi-person-dash','bi-person-x','bi-person-plus-fill',
                'bi-bounding-box','bi-aspect-ratio','bi-crop',
                'bi-brush','bi-paint-bucket','bi-paint','bi-palette','bi-palette-fill',
                'bi-droplet-half','bi-circle','bi-circle-fill','bi-circle-half',
                'bi-triangle','bi-triangle-fill','bi-square','bi-square-fill','bi-hexagon','bi-hexagon-fill',
                'bi-octagon','bi-octagon-fill','bi-diamond','bi-diamond-fill',
                'bi-1-circle','bi-2-circle','bi-3-circle','bi-4-circle','bi-5-circle',
                'bi-6-circle','bi-7-circle','bi-8-circle','bi-9-circle','bi-0-circle',
                'bi-1-square','bi-2-square','bi-3-square','bi-4-square','bi-5-square',
                'bi-6-square','bi-7-square','bi-8-square','bi-9-square','bi-0-square',
                'bi-arrow-return-left','bi-arrow-return-right','bi-reply','bi-reply-all',
                'bi-forward','bi-share','bi-share-fill','bi-link','bi-link-45deg','bi-chain',
                'bi-phone-flip','bi-arrows-angle-expand','bi-arrows-angle-contract',
                'bi-fullscreen','bi-fullscreen-exit','bi-arrows-move','bi-arrows-expand',
                'bi-textarea-resize','bi-textarea',
                'bi-justify-left','bi-justify-right','bi-justify-center','bi-justify',
                'bi-text-indent-left','bi-text-indent-right',
                'bi-list','bi-list-nested','bi-list-check','bi-list-ol','bi-list-ul',
                'bi-grid','bi-grid-3x3','bi-grid-fill',
                'bi-layout-split','bi-layout-text-window','bi-layout-sidebar',
                'bi-layout-wtf','bi-layout-three-columns',
                'bi-book','bi-book-fill','bi-book-half','bi-journal','bi-journal-text',
                'bi-journal-bookmark','bi-journal-bookmark-fill','bi-journal-richtext',
                'bi-newspaper','bi-newspaper',
                'bi-postcard','bi-postcard-fill','bi-sticky','bi-sticky-fill',
                'bi-filetype-aac','bi-filetype-html','bi-filetype-js','bi-filetype-json','bi-filetype-md',
                'bi-filetype-pdf','bi-filetype-php','bi-filetype-png','bi-filetype-psd',
                'bi-filetype-py','bi-filetype-raw','bi-filetype-sql','bi-filetype-svg','bi-filetype-text',
                'bi-filetype-tiff','bi-filetype-txt','bi-filetype-wav','bi-filetype-woff','bi-filetype-xls',
                'bi-filetype-xml','bi-filetype-yml',
                'bi-github','bi-twitter-x','bi-facebook','bi-instagram','bi-youtube','bi-tiktok',
                'bi-linkedin','bi-threads','bi-mastodon','bi-twitch','bi-discord',
                'bi-strava','bi-spotify','bi-slack','bi-dribbble','bi-behance',
                'bi-apple','bi-android','bi-microsoft','bi-google','bi-amazon',
                'bi-bootstrap','bi-meta','bi-paypal','bi-stripe',
                'bi-tencent-qq','bi-wechat','bi-whatsapp',
                'bi-emoji-smile','bi-emoji-laughing','bi-emoji-neutral','bi-emoji-frown','bi-emoji-angry',
                'bi-emoji-heart-eyes','bi-emoji-kiss','bi-emoji-tear','bi-emoji-surprise',
                'bi-hand-thumbs-up','bi-hand-thumbs-up-fill','bi-hand-thumbs-down','bi-hand-thumbs-down-fill',
                'bi-hand-index','bi-hand-index-fill','bi-hand-index-thumb','bi-hand-index-thumb-fill',
                'bi-hand-heart','bi-hand-heart-fill','bi-hand-rock','bi-handpeace',
                'bi-geo-fill','bi-pin-angle','bi-pin-angle-fill',
                'bi-snapchat','bi-discord'
            ],
            filtered: [],
            init() {
                this.filtered = [...this.icons];
                var self = this;
                window.addEventListener('icon-picker-set', function(e) {
                    if (e.detail.id === '{{ $id }}' && e.detail.value) {
                        self.selected = e.detail.value;
                    }
                });
            },
            filter() {
                var q = this.query.toLowerCase();
                this.filtered = this.icons.filter(function(i) { return i.includes(q); });
            },
            select(icon) {
                this.selected = icon;
                this.open = false;
                this.query = '';
                this.filter();
            }
        };
    }
}
</script>
@endpush
