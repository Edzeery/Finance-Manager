<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => 'password',
    'value' => '',
    'id' => null,
    'placeholder' => '••••••••',
    'required' => false,
    'maxlength' => 255,
    'error' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'name' => 'password',
    'value' => '',
    'id' => null,
    'placeholder' => '••••••••',
    'required' => false,
    'maxlength' => 255,
    'error' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $id = $id ?? 'pwd_' . uniqid();
    $errorName = $error ?? $name;
    $hasError = $errors->has($errorName);
?>

<div class="input-icon-wrap">
    <i class="bi bi-lock input-icon-left"></i>
    <input type="password" id="<?php echo e($id); ?>" name="<?php echo e($name); ?>" value="<?php echo e($value); ?>"
        maxlength="<?php echo e($maxlength); ?>" <?php if($required): ?> required <?php endif; ?>
        placeholder="<?php echo e($placeholder); ?>"
        <?php echo e($attributes->merge([
            'autocomplete' => 'off',
            'class' => 'form-custom has-icon-left has-icon-right' . ($hasError ? ' is-invalid' : ''),
        ])); ?>>
    <button type="button" class="toggle-password" @click="togglePassword('<?php echo e($id); ?>', $el)" tabindex="-1"
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
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\components\password-input.blade.php ENDPATH**/ ?>