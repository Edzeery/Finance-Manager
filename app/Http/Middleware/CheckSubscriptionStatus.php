<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionStatus
{
    protected array $alwaysAllowed = [
        'billing.subscriptions*',
        'settings.account*',
        'billing.invoices.*',
        'billing.payments.*',
        'invitations.*',
        'notifications.*',
        'payment.*',
        'chargily.back',
        'paypal.back',
        'logout',
        'locale.switch',
        'theme.switch',
        'currency.switch',
        'coupon.validate',
        'onboarding.*',
    ];

    protected array $superRoutes = [
        'super.admin.*',
        'livewire.*',
        'sanctum.*',
        'login',
        'register',
        'password.*',
        'two-factor.*',
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

        $routeName = $request->route()?->getName();

        if ($routeName && $this->matchesPattern($routeName, $this->superRoutes)) {
            return $next($request);
        }

        $subscription = $user->currentWorkspace?->owner()?->first()?->activeSubscription() ?? $user->activeSubscription();

        if (! $subscription) {
            return $this->handleBlocked($request, $next, $routeName);
        }

        $status = $this->computeStatus($subscription);

        return match ($status) {
            'active' => $next($request),
            'grace' => $this->handleGracePeriod($request, $next, $subscription),
            'blocked' => $this->handleBlocked($request, $next, $routeName),
        };
    }

    private function computeStatus($subscription): string
    {
        if ($subscription->isOnGrace()) {
            return 'grace';
        }

        if ($subscription->isActive()) {
            if ($subscription->isTrialExpired()) {
                return 'blocked';
            }

            return 'active';
        }

        return 'blocked';
    }

    private function handleGracePeriod(Request $request, Closure $next, $subscription): Response
    {
        $request->session()->flash('subscription_warning', __('messages.subscription_grace_warning', [
            'date' => $subscription->grace_ends_at->format('Y-m-d'),
            'days' => $subscription->graceDaysRemaining(),
        ]));

        return $next($request);
    }

    private function handleBlocked(Request $request, Closure $next, ?string $routeName): Response
    {
        if ($routeName && $this->matchesPattern($routeName, $this->alwaysAllowed)) {
            if (! $request->session()->has('subscription_blocked')) {
                $request->session()->flash('subscription_blocked', __('messages.subscription_expired'));
            }

            return $next($request);
        }

        return $this->redirectToSubscription($request);
    }

    private function redirectToSubscription(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('messages.subscription_expired'),
                'error' => 'subscription_required',
            ], 402);
        }

        return redirect()->route('billing.subscriptions')
            ->with('error', __('messages.subscription_expired'));
    }

    private function matchesPattern(string $routeName, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
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
