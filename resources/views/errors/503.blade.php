<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}" data-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance - {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="error-layout" style="--error-gradient:linear-gradient(135deg,var(--info),var(--accent));--error-icon-bg:var(--info-light);--error-icon-color:var(--info)">
    <div class="error-page">
        <div class="app-name"><i class="bi bi-tools"></i>{{ config('app.name') }}</div>
        <div class="error-icon"><i class="bi bi-gear-wide-connected"></i></div>
        <div class="error-code">503</div>
        <div class="error-title">{{ __('Under Maintenance') }}</div>
        <div class="error-message">{{ __('We are currently performing scheduled maintenance. We will be back shortly.') }}</div>
        <div class="maintenance-progress">
            <div class="bar"><div class="bar-fill"></div></div>
            <div class="label"><span>{{ __('In progress') }}</span><span>{{ __('Almost done') }}</span></div>
        </div>
        <div class="error-actions">
            <a href="javascript:location.reload()" class="btn-error btn-error-primary"><i class="bi bi-arrow-counterclockwise"></i> {{ __('Check Again') }}</a>
            <a href="{{ route('login') }}" class="btn-error btn-error-secondary"><i class="bi bi-box-arrow-in-right"></i> {{ __('general.login') }}</a>
        </div>
    </div>
</body>
</html>
