@props([
    'name' => 'password',
    'value' => '',
    'id' => null,
    'placeholder' => '••••••••',
    'required' => false,
    'maxlength' => 255,
    'error' => null,
])

@php
    $id = $id ?? 'pwd_' . uniqid();
    $errorName = $error ?? $name;
    $hasError = $errors->has($errorName);
@endphp

<div class="input-icon-wrap">
    <i class="bi bi-lock input-icon-left"></i>
    <input type="password" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}"
        maxlength="{{ $maxlength }}" @if ($required) required @endif
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'autocomplete' => 'off',
            'class' => 'form-custom has-icon-left has-icon-right' . ($hasError ? ' is-invalid' : ''),
        ]) }}>
    <button type="button" class="toggle-password" @click="togglePassword('{{ $id }}', $el)" tabindex="-1"
        aria-label="Toggle password visibility">
        <i class="bi bi-eye"></i>
    </button>
</div>

<script>
if (typeof togglePassword !== 'function') {
    function togglePassword(fieldId, btn) {
        var input = document.getElementById(fieldId);
        if (!input) return;
        var icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            if (icon) icon.className = 'bi bi-eye';
        }
    }
}
</script>
