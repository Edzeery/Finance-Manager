@php
    $current = request()->route()->getName();
@endphp

<div class="d-flex gap-2 mb-4">
    <x-button href="{{ route('zakat.calculator') }}"
        :variant="$current === 'zakat.calculator' ? 'accent' : 'outline'"
        icon="bi bi-calculator">{{ __('zakat.calculate') }}</x-button>
    <x-button href="{{ route('zakat.history') }}"
        :variant="$current === 'zakat.history' ? 'accent' : 'outline'"
        icon="bi bi-clock-history">{{ __('zakat.history') }}</x-button>
</div>
