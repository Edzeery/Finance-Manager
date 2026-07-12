@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'mb-3']) }} style="padding:12px 16px; background:rgba(34,197,94,0.12); color:var(--success); border-radius:8px; font-size:14px">
        <i class="bi bi-check-circle me-1"></i>
        {{ $status }}
    </div>
@endif
