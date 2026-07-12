@php
    $status = request('status', 'success');
    $config = [
        'success' => ['icon' => 'bi-check-circle-fill', 'color' => '#198754', 'title' => __('payment.success_title'), 'message' => __('payment.success_message')],
        'canceled' => ['icon' => 'bi-x-circle-fill', 'color' => '#dc3545', 'title' => __('payment.canceled_title'), 'message' => __('payment.canceled_message')],
    ];
    $current = $config[$status] ?? $config['success'];
@endphp

<x-app-layout>
    <div style="min-height:60vh;display:flex;align-items:center;justify-content:center">
        <div style="text-align:center;max-width:480px">
            <i class="bi {{ $current['icon'] }}" style="font-size:64px;color:{{ $current['color'] }};margin-bottom:16px"></i>
            <h3 style="font-weight:600;margin-bottom:8px">{{ $current['title'] }}</h3>
            <p style="color:var(--text-muted);margin-bottom:24px">{{ $current['message'] }}</p>
            <a href="{{ route('settings.index') }}" class="btn btn-accent">{{ __('payment.back_to_settings') }}</a>
        </div>
    </div>
</x-app-layout>
