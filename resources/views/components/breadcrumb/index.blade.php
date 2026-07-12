@php
    $items = $items ?? [];
@endphp

@if(count($items) > 0)
<nav class="breadcrumb-nav" aria-label="{{ __('general.breadcrumb') }}">
    <ol class="breadcrumb-list">
        @foreach($items as $index => $item)
            @php
                $isLast = $index === array_key_last($items);
                $label = $item['label'] ?? '';
                $url = $item['url'] ?? null;
                $icon = $item['icon'] ?? null;
            @endphp
            <li class="breadcrumb-item {{ $isLast ? 'active' : '' }}" {{ $isLast ? 'aria-current="page"' : '' }}>
                @if(!$isLast && $url)
                    <a href="{{ $url }}" class="breadcrumb-link">
                        @if($icon)
                            <i class="bi {{ $icon }}"></i>
                        @endif
                        {{ $label }}
                    </a>
                @else
                    <span class="breadcrumb-current">
                        @if($icon)
                            <i class="bi {{ $icon }}"></i>
                        @endif
                        {{ $label }}
                    </span>
                @endif
                @if(!$isLast)
                    <i class="bi {{ app()->getLocale() === 'ar' ? 'bi-chevron-left' : 'bi-chevron-right' }} breadcrumb-separator"></i>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif

