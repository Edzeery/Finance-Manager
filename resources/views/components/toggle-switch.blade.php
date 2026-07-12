@props([
    'name' => null,
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'label' => null,
    'hint' => null,
    'id' => null,
    'standalone' => false,
    'description' => null,
])

@php
    $id = $id ?? 'toggle_' . uniqid();
    $isChecked = filter_var($checked, FILTER_VALIDATE_BOOLEAN);
    $iconClass = $isChecked ? 'bi-toggle2-on' : 'bi-toggle2-off';
    $iconColor = $isChecked ? 'color:var(--success)' : 'color:var(--text-muted)';
@endphp

@if ($label)
    <div class="d-flex justify-content-between align-items-center mb-1">
        <label for="{{ $id }}" class="form-label-custom mb-0"
               style="cursor:pointer"
               @click="document.getElementById('{{ $id }}').click()">{{ $label }}</label>
        @if ($hint)
            <small class="text-muted">{{ $hint }}</small>
        @endif
    </div>
@endif

<div {{ $attributes->merge(['class' => 'toggle-switch-wrapper']) }}
     style="cursor:pointer;display:inline-flex;align-items:center;gap:8px"
     role="button"
     tabindex="0"
     @click="!$event.target.closest('.toggle-switch-btn')&&document.getElementById('{{ $id }}').click()"
     onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();document.getElementById('{{ $id }}').click()}">
    @if (!$standalone)
        <input type="hidden" name="{{ $name }}" value="{{ $isChecked ? '1' : '0' }}" id="{{ $id }}_hidden" {{ $attributes->whereStartsWith('wire:model') }}>
    @endif
    <button type="{{ $standalone ? 'submit' : 'button' }}"
        class="btn btn-sm p-0 border-0 bg-transparent toggle-switch-btn"
        @if ($standalone) name="{{ $name }}" value="{{ $isChecked ? '0' : $value }}" @endif
        @if ($disabled) disabled @endif
        id="{{ $id }}"
        @if (!$standalone)
        @click="toggleSwitch($el, '{{ $id }}_hidden')"
        @endif
        aria-label="Toggle"
        style="transition:all 0.15s;cursor:pointer">
        <i class="bi {{ $iconClass }}"
           style="font-size:20px;{{ $iconColor }};pointer-events:none;transition:color 0.15s"></i>
    </button>
    @if ($description)
        <span class="toggle-switch-text" style="font-size:13px;font-weight:500;color:var(--text);cursor:pointer">{{ $description }}</span>
    @endif
</div>

@if (!$standalone)
<script>
if (typeof toggleSwitch !== 'function') {
    function toggleSwitch(btn, hiddenId) {
        var hidden = document.getElementById(hiddenId);
        var icon = btn.querySelector('i');
        if (!hidden || !icon) return;
        var newVal = hidden.value === '1' ? '0' : '1';
        hidden.value = newVal;
        if (newVal === '1') {
            icon.className = 'bi bi-toggle2-on';
            icon.style.color = 'var(--success)';
        } else {
            icon.className = 'bi bi-toggle2-off';
            icon.style.color = 'var(--text-muted)';
        }
        hidden.dispatchEvent(new Event('change', { bubbles: true }));
    }
}
if (typeof setToggle !== 'function') {
    function setToggle(toggleId, val) {
        var hidden = document.getElementById(toggleId + '_hidden');
        var btn = document.getElementById(toggleId);
        if (!hidden || !btn) return;
        hidden.value = val ? '1' : '0';
        var icon = btn.querySelector('i');
        if (icon) {
            icon.className = val ? 'bi bi-toggle2-on' : 'bi bi-toggle2-off';
            icon.style.color = val ? 'var(--success)' : 'var(--text-muted)';
        }
    }
}
</script>
@endif