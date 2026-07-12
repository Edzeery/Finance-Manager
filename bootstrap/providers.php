<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\VoltServiceProvider::class,
    App\Providers\SettingsServiceProvider::class,
    App\Providers\BindingServiceProvider::class,
    App\Providers\GatewayServiceProvider::class,
    App\Providers\RateLimiterServiceProvider::class,
    App\Providers\ModelEventServiceProvider::class,
    App\Providers\PolicyServiceProvider::class,
    Livewire\LivewireServiceProvider::class,
    Livewire\Volt\VoltServiceProvider::class,
];
