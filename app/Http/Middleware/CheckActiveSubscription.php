<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveSubscription
{
    protected array $except = [
        'billing.subscriptions*',
        'billing.invoices.*',
        'billing.payments.*',
        'settings.account*',
        'settings.workspace*',
        'onboarding.*',
        'invitations.*',
        'notifications.*',
        'super.admin.*',
        'livewire.*',
        'payment.*',
        'chargily.back',
        'paypal.back',
        'coupon.validate',
        'locale.switch',
        'theme.switch',
        'currency.switch',
        'logout',
        'login',
        'register',
        'password.*',
        'two-factor.*',
        'sanctum.*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        if ($request->is('api/*')) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();

        if ($routeName && $this->inExceptArray($routeName)) {
            return $next($request);
        }

        $subscription = $user->currentWorkspace?->owner()?->first()?->activeSubscription() ?? $user->activeSubscription();

        if ($subscription && ! $subscription->isExpired()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('messages.subscription_expired'),
                'error' => 'subscription_required',
            ], 402);
        }

        return redirect()->route('billing.subscriptions')
            ->with('warning', __('messages.subscription_required'));
    }

    protected function inExceptArray(?string $routeName): bool
    {
        if (! $routeName) {
            return false;
        }

        foreach ($this->except as $pattern) {
            if (str_ends_with($pattern, '.*')) {
                $prefix = substr($pattern, 0, -2);
                if (str_starts_with($routeName, $prefix)) {
                    return true;
                }
            }
            if (str_ends_with($pattern, '*')) {
                $prefix = substr($pattern, 0, -1);
                if (str_starts_with($routeName, $prefix)) {
                    return true;
                }
            } elseif ($routeName === $pattern) {
                return true;
            }
        }

        return false;
    }
}
