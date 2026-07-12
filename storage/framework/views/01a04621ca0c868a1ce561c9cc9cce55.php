<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e(in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr'); ?>" data-theme="<?php echo e(session('theme', 'light')); ?>">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance - <?php echo e(config('app.name')); ?></title>
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
</head>
<body class="error-layout" style="--error-gradient:linear-gradient(135deg,var(--info),var(--accent));--error-icon-bg:var(--info-light);--error-icon-color:var(--info)">
    <div class="error-page">
        <div class="app-name"><i class="bi bi-tools"></i><?php echo e(config('app.name')); ?></div>
        <div class="error-icon"><i class="bi bi-gear-wide-connected"></i></div>
        <div class="error-code">503</div>
        <div class="error-title"><?php echo e(__('Under Maintenance')); ?></div>
        <div class="error-message"><?php echo e(__('We are currently performing scheduled maintenance. We will be back shortly.')); ?></div>
        <div class="maintenance-progress">
            <div class="bar"><div class="bar-fill"></div></div>
            <div class="label"><span><?php echo e(__('In progress')); ?></span><span><?php echo e(__('Almost done')); ?></span></div>
        </div>
        <div class="error-actions">
            <a href="javascript:location.reload()" class="btn-error btn-error-primary"><i class="bi bi-arrow-counterclockwise"></i> <?php echo e(__('Check Again')); ?></a>
            <a href="<?php echo e(route('login')); ?>" class="btn-error btn-error-secondary"><i class="bi bi-box-arrow-in-right"></i> <?php echo e(__('general.login')); ?></a>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views\errors\503.blade.php ENDPATH**/ ?>