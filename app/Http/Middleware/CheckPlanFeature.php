<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanFeature
{
    protected array $except = [
        'billing.subscriptions*',
        'settings.account*',
        'settings.workspace*',
        'billing.invoices.*',
        'billing.payments.*',
        'two-factor.*',
        'locale.switch',
        'theme.switch',
        'currency.switch',
        'subscriptions*',
        'onboarding.*',
        'payment.*',
        'logout',
        'login',
        'register',
        'password.*',
        'sanctum.*',
        'livewire.*',
        'super.admin.*',
    ];

    public function handle(Request $request, Closure $next, string $feature): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();

        if ($routeName && $this->inExceptArray($routeName)) {
            return $next($request);
        }

        $workspace = $user->currentWorkspace;
        $subscription = $workspace?->owner()?->first()?->activeSubscription() ?? $user->activeSubscription();

        if (! $subscription || ! $subscription->plan) {
            return $this->redirectToUpgrade($request);
        }

        if ($subscription->plan->hasFeature($feature)) {
            return $next($request);
        }

        return $this->redirectToUpgrade($request);
    }

    private function redirectToUpgrade(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('messages.plan_upgrade_required'),
                'error' => 'plan_upgrade_required',
            ], 402);
        }

        return redirect()->route('billing.subscriptions')
            ->with('warning', __('messages.plan_upgrade_required'));
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
