<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => null,
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'label' => null,
    'hint' => null,
    'id' => null,
    'standalone' => false,
    'description' => null,
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
    'name' => null,
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'label' => null,
    'hint' => null,
    'id' => null,
    'standalone' => false,
    'description' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $id = $id ?? 'toggle_' . uniqid();
    $isChecked = filter_var($checked, FILTER_VALIDATE_BOOLEAN);
    $iconClass = $isChecked ? 'bi-toggle2-on' : 'bi-toggle2-off';
    $iconColor = $isChecked ? 'color:var(--success)' : 'color:var(--text-muted)';
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($label): ?>
    <div class="d-flex justify-content-between align-items-center mb-1">
        <label for="<?php echo e($id); ?>" class="form-label-custom mb-0"
               style="cursor:pointer"
               @click="document.getElementById('<?php echo e($id); ?>').click()"><?php echo e($label); ?></label>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hint): ?>
            <small class="text-muted"><?php echo e($hint); ?></small>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div <?php echo e($attributes->merge(['class' => 'toggle-switch-wrapper'])); ?>

     style="cursor:pointer;display:inline-flex;align-items:center;gap:8px"
     role="button"
     tabindex="0"
     @click="!$event.target.closest('.toggle-switch-btn')&&document.getElementById('<?php echo e($id); ?>').click()"
     onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();document.getElementById('<?php echo e($id); ?>').click()}">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$standalone): ?>
        <input type="hidden" name="<?php echo e($name); ?>" value="<?php echo e($isChecked ? '1' : '0'); ?>" id="<?php echo e($id); ?>_hidden" <?php echo e($attributes->whereStartsWith('wire:model')); ?>>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <button type="<?php echo e($standalone ? 'submit' : 'button'); ?>"
        class="btn btn-sm p-0 border-0 bg-transparent toggle-switch-btn"
        <?php if($standalone): ?> name="<?php echo e($name); ?>" value="<?php echo e($isChecked ? '0' : $value); ?>" <?php endif; ?>
        <?php if($disabled): ?> disabled <?php endif; ?>
        id="<?php echo e($id); ?>"
        <?php if(!$standalone): ?>
        @click="toggleSwitch($el, '<?php echo e($id); ?>_hidden')"
        <?php endif; ?>
        aria-label="Toggle"
        style="transition:all 0.15s;cursor:pointer">
        <i class="bi <?php echo e($iconClass); ?>"
           style="font-size:20px;<?php echo e($iconColor); ?>;pointer-events:none;transition:color 0.15s"></i>
    </button>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($description): ?>
        <span class="toggle-switch-text" style="font-size:13px;font-weight:500;color:var(--text);cursor:pointer"><?php echo e($description); ?></span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$standalone): ?>
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
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php /**PATH C:\laragon\www\Finance-Manager\resources\views/components/toggle-switch.blade.php ENDPATH**/ ?>