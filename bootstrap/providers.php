<?php

use App\Providers\AppServiceProvider;
use App\Providers\BindingServiceProvider;
use App\Providers\GatewayServiceProvider;
use App\Providers\ModelEventServiceProvider;
use App\Providers\PolicyServiceProvider;
use App\Providers\RateLimiterServiceProvider;
use App\Providers\SettingsServiceProvider;
use App\Providers\VoltServiceProvider;
use Livewire\LivewireServiceProvider;

return [
    AppServiceProvider::class,
    VoltServiceProvider::class,
    SettingsServiceProvider::class,
    BindingServiceProvider::class,
    GatewayServiceProvider::class,
    RateLimiterServiceProvider::class,
    ModelEventServiceProvider::class,
    PolicyServiceProvider::class,
    LivewireServiceProvider::class,
    Livewire\Volt\VoltServiceProvider::class,
];
