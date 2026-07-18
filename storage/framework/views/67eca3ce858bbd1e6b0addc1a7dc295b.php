<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e(in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr'); ?>" data-theme="<?php echo e(session('theme', 'light')); ?>">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Too Many Requests - <?php echo e(config('app.name')); ?></title>
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
</head>
<body class="error-layout" style="--error-gradient:linear-gradient(135deg,var(--warning),var(--danger));--error-icon-bg:var(--warning-light);--error-icon-color:var(--warning)">
    <?php if (isset($component)) { $__componentOriginal4d6bed2ebceb29e0a9932fbda627422a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4d6bed2ebceb29e0a9932fbda627422a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-loader','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-loader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4d6bed2ebceb29e0a9932fbda627422a)): ?>
<?php $attributes = $__attributesOriginal4d6bed2ebceb29e0a9932fbda627422a; ?>
<?php unset($__attributesOriginal4d6bed2ebceb29e0a9932fbda627422a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4d6bed2ebceb29e0a9932fbda627422a)): ?>
<?php $component = $__componentOriginal4d6bed2ebceb29e0a9932fbda627422a; ?>
<?php unset($__componentOriginal4d6bed2ebceb29e0a9932fbda627422a); ?>
<?php endif; ?>
    <div class="error-page">
        <div class="app-name"><i class="bi bi-shield-check"></i><?php echo e(config('app.name')); ?></div>
        <div class="error-icon"><i class="bi bi-speedometer2"></i></div>
        <div class="error-code">429</div>
        <div class="error-title"><?php echo e(__('Too Many Requests')); ?></div>
        <div class="error-message"><?php echo e(__('You have sent too many requests in a short period. Please wait a moment before trying again.')); ?></div>
        <div class="retry-info"><i class="bi bi-hourglass-split"></i> <?php echo e(__('Please try again later')); ?></div>
        <div class="error-actions">
            <a href="<?php echo e(route('dashboard')); ?>" class="btn-error btn-error-primary"><i class="bi bi-house-door"></i> <?php echo e(__('general.dashboard')); ?></a>
            <a href="javascript:location.reload()" class="btn-error btn-error-secondary"><i class="bi bi-arrow-counterclockwise"></i> <?php echo e(__('Try Again')); ?></a>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\errors\429.blade.php ENDPATH**/ ?>