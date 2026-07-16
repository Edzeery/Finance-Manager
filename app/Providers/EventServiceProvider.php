<?php

namespace App\Providers;

use App\Events\PaymentCompleted;
use App\Events\SubscriptionActivated;
use App\Listeners\ActivateWorkspace;
use App\Listeners\CompleteOnboarding;
use App\Listeners\CreateAdminNotification;
use App\Listeners\LogAuthEvent;
use App\Listeners\SendPaymentReceipt;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    public function boot(): void
    {

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
    }
}
