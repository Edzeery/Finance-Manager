<?php

namespace App\Providers;

use App\Events\PaymentCompleted;
use App\Events\SubscriptionActivated;
use App\Listeners\ActivateWorkspace;
use App\Listeners\CompleteOnboarding;
use App\Listeners\CreateAdminNotification;
use App\Listeners\LogAuthEvent;
use App\Listeners\SendPaymentReceipt;
use App\Models\PersonalAccessToken;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Vite;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $vite = $this->app->make(Vite::class);
        $vite->useCspNonce();
        $vite->usePreloadTagAttributes(function ($src, $url, $chunk, $manifest) {
            if (str_ends_with($url, '.css')) {
                return false;
            }

            return [];
        });
        $vite->createAssetPathsUsing(fn ($path) => '/'.ltrim($path, '/'));

        Event::subscribe(LogAuthEvent::class);

        Event::listen(
            PaymentCompleted::class,
            SendPaymentReceipt::class,
        );

        Event::listen(
            PaymentCompleted::class,
            ActivateWorkspace::class,
        );

        Event::listen(
            PaymentCompleted::class,
            CompleteOnboarding::class,
        );

        Event::listen(
            PaymentCompleted::class,
            CreateAdminNotification::class,
        );

        Event::listen(
            SubscriptionActivated::class,
            CreateAdminNotification::class,
        );

        Event::listen(
            Registered::class,
            CreateAdminNotification::class,
        );

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        Paginator::useBootstrapFive();
    }
}
