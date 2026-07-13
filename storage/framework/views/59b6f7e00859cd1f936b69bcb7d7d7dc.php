<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e(in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr'); ?>" data-theme="<?php echo e(session('theme', 'light')); ?>">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Error - <?php echo e(config('app.name')); ?></title>
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
</head>
<body class="error-layout" style="--error-gradient:linear-gradient(135deg,var(--danger),#dc2626);--error-icon-bg:var(--danger-light);--error-icon-color:var(--danger)">
    <div class="error-page">
        <div class="app-name"><i class="bi bi-shield-check"></i><?php echo e(config('app.name')); ?></div>
        <div class="error-icon"><i class="bi bi-exclamation-triangle"></i></div>
        <div class="error-code">500</div>
        <div class="error-title"><?php echo e(__('Server Error')); ?></div>
        <div class="error-message"><?php echo e(__('Something went wrong on our end. Please try again later or contact support if the problem persists.')); ?></div>
        <div class="error-actions">
            <a href="<?php echo e(route('dashboard')); ?>" class="btn-error btn-error-primary"><i class="bi bi-house-door"></i> <?php echo e(__('general.dashboard')); ?></a>
            <a href="javascript:location.reload()" class="btn-error btn-error-secondary"><i class="bi bi-arrow-counterclockwise"></i> <?php echo e(__('Try Again')); ?></a>
        </div>
        <div class="error-detail"><?php echo e(config('app.name')); ?> &mdash; <?php echo e(now()->year); ?></div>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/errors/500.blade.php ENDPATH**/ ?>