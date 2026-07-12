<?php

namespace App\Providers;

use App\Contracts\Services\ActivityLogServiceInterface;
use App\Services\ActivityLogService;
use App\Services\Payments\CashGateway;
use App\Services\Payments\DeliveryGateway;
use App\Services\Payments\GatewayManager;
use App\Services\Payments\Noest\NoestGateway;
use App\Services\Payments\Noest\NoestService;
use App\Services\Payments\BaridiMobGateway;
use App\Services\Payments\PayPalGateway;
use App\Services\Payments\RedotPayGateway;
use App\Services\Payments\StripeGateway;
use App\Services\Payments\WiseGateway;
use App\Services\Payments\WiseManualGateway;
use App\Services\Payments\PayoneerGateway;
use App\Services\Payments\Chargily\ChargilyCheckoutService;
use App\Services\Payments\Chargily\ChargilyGateway;
use App\Services\Payments\Chargily\ChargilySignatureValidator;
use App\Services\Payments\Chargily\ChargilyWebhookService;
use App\Services\RedirectService;
use App\Services\SubscriptionPaymentService;
use App\Services\TwoFactorAuthenticationService;
use App\Services\Webhooks\PayPalSignatureValidator;
use App\Services\Webhooks\PayoneerSignatureValidator;
use App\Services\Webhooks\StripeSignatureValidator;
use App\Services\Webhooks\NoestSignatureValidator;
use App\Services\Webhooks\WebhookSignatureManager;
use App\Services\Webhooks\WiseSignatureValidator;
use Illuminate\Support\ServiceProvider;

class GatewayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ActivityLogServiceInterface::class, ActivityLogService::class);
        $this->app->singleton(RedirectService::class);
        $this->app->singleton(TwoFactorAuthenticationService::class);
        $this->app->singleton(SubscriptionPaymentService::class);
        $this->app->singleton(ChargilyCheckoutService::class);
        $this->app->singleton(ChargilySignatureValidator::class);
        $this->app->singleton(ChargilyWebhookService::class);
        $this->app->singleton(NoestService::class);
        $this->app->singleton(WebhookSignatureManager::class, function () {
            $manager = new WebhookSignatureManager;
            $manager->register(new StripeSignatureValidator);
            $manager->register(new PayPalSignatureValidator);
            $manager->register(new WiseSignatureValidator);
            $manager->register(new PayoneerSignatureValidator);
            $manager->register(new NoestSignatureValidator);
            return $manager;
        });

        $this->app->singleton(GatewayManager::class, function () {
            $manager = new GatewayManager;

            $manager->register('chargily', new ChargilyGateway(app(ChargilyCheckoutService::class)));
            $manager->register('baridimob', new BaridiMobGateway);
            $manager->register('paypal', new PayPalGateway);
            $manager->register('redotpay', new RedotPayGateway);
            $manager->register('stripe', new StripeGateway);
            $manager->register('wise', new WiseGateway);
            $manager->register('wise_manual', new WiseManualGateway);
            $manager->register('payoneer', new PayoneerGateway);
            $manager->register('cash', new CashGateway);
            $manager->register('delivery', new DeliveryGateway);
            $manager->register('noest', new NoestGateway);

            return $manager;
        });
    }
}
