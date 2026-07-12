@props([
    'variant' => 'dropdown-bs',
    'triggerClass' => 'guest-navbar-btn',
])

@php
    $current = session('currency', auth()->user()?->currency ?? config('finance.currency', 'DZD'));
    $currencies = config('finance.currencies', ['DZD' => [], 'USD' => [], 'EUR' => []]);
@endphp

<div class="dropdown">
    <button class="{{ $triggerClass }}" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-currency-exchange"></i>
        <span class="d-none d-sm-inline ms-1" style="font-size:11px;font-weight:600">{{ $current }}</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end dropdown-custom">
        @foreach ($currencies as $code => $info)
            <li>
                <form method="POST" action="{{ route('currency.switch', $code) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="dropdown-item">{{ $info['symbol'] ?? $code }} {{ $code }}</button>
                </form>
            </li>
        @endforeach
    </ul>
</div>
