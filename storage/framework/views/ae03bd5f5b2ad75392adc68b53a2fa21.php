<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>"
      dir="<?php echo e(in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr'); ?>"
      data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description" content="<?php echo e(config('app.name') . ' - ' . __('super-admin.super_dashboard')); ?>">
    <meta name="theme-color" content="#6366F1">
    <meta name="theme-switch-url" content="<?php echo e(route('theme.switch')); ?>">
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <title><?php echo e($title ?? __('super-admin.super_dashboard')); ?> - <?php echo e(config('app.name', 'Finance Manager')); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="auth-page sa-guest overflow-y-auto">
    <?php echo e($slot); ?>


    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\Finance-Manager\resources\views/layouts/super-admin/guest.blade.php ENDPATH**/ ?>