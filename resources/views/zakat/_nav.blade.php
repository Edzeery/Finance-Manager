@php
    $current = request()->route()->getName();
@endphp

<div class="d-flex gap-2 mb-4">
    <a href="{{ route('zakat.calculator') }}"
       class="btn btn-custom {{ $current === 'zakat.calculator' ? 'btn-accent' : 'btn-outline-secondary' }}">
        <i class="bi bi-calculator me-1"></i>{{ __('zakat.calculate') }}
    </a>
    <a href="{{ route('zakat.history') }}"
       class="btn btn-custom {{ $current === 'zakat.history' ? 'btn-accent' : 'btn-outline-secondary' }}">
        <i class="bi bi-clock-history me-1"></i>{{ __('zakat.history') }}
    </a>
</div>
