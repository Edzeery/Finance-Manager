<?php

namespace App\Providers;

use App\Listeners\LogAuthEvent;
use App\Models\DebtPayment;
use App\Models\Invoice;
use App\Models\PersonalAccessToken;
use App\Observers\DebtPaymentObserver;
use App\Observers\InvoiceObserver;
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

        DebtPayment::observe(DebtPaymentObserver::class);
        Invoice::observe(InvoiceObserver::class);

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        Paginator::useBootstrapFive();
    }
}
