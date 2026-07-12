@props([
    'variant' => 'dropdown',     // dropdown, dropdown-bs, inline
    'showCode' => true,
    'triggerClass' => 'topbar-btn',
    'itemClass' => '',
    'dropdown' => 'end',
])

@php
    $locales = ['ar' => __('general.ar'), 'fr' => __('general.fr'), 'en' => __('general.en')];
    $current = app()->getLocale();
@endphp

@if($variant === 'dropdown')
    <div class="dropdown" x-data="{ langOpen: false }" @click.away="langOpen = false">
        <button class="{{ $triggerClass }}" type="button" @click="langOpen = !langOpen" aria-label="{{ __('general.language') }}" title="{{ strtoupper($current) }}">
            <i class="bi bi-globe2"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-{{ $dropdown }}" x-show="langOpen" style="display:none" x-transition>
            @foreach($locales as $code => $name)
                <li>
                    <form method="POST" action="{{ route('locale.switch', $code) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                            @if($showCode)
                                <span style="width:22px;height:22px;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;background:var(--bg-subtle);color:var(--text);text-transform:uppercase">{{ $code }}</span>
                            @endif
                            <span>{{ $name }}</span>
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
@elseif($variant === 'dropdown-bs')
    <div class="dropdown">
        <button class="{{ $triggerClass }}" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-globe2"></i>
            @if($showCode)
                <span class="d-none d-sm-inline ms-1 text-sm">{{ strtoupper($current) }}</span>
            @endif
        </button>
        <ul class="dropdown-menu dropdown-menu-{{ $dropdown }} dropdown-custom">
            @foreach($locales as $code => $name)
                <li>
                    <form method="POST" action="{{ route('locale.switch', $code) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item">{{ $name }}</button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
@elseif($variant === 'inline')
    @foreach($locales as $code => $name)
        <form method="POST" action="{{ route('locale.switch', $code) }}" class="d-inline">
            @csrf
            <button type="submit" class="{{ $itemClass }} {{ $current === $code ? 'active' : '' }}">{{ $showCode ? strtoupper($code) : $name }}</button>
        </form>
    @endforeach
@endif
