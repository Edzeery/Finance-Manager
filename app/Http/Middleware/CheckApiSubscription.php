<?php

namespace App\Http\Middleware;

use App\Models\Subscription;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        if ($request->is('api/health') || $request->is('api/auth/*') || $request->is('api/plans') || $request->is('api/coupon/*')) {
            return $next($request);
        }

        if ($request->is('api/workspaces*') || $request->is('api/workspace/subscription*')) {
            return $next($request);
        }

        $subscription = $user->currentWorkspace?->owner()?->first()?->activeSubscription()
            ?? $user->activeSubscription()
            ?? Subscription::withoutWorkspace()->where('user_id', $user->id)->latest()->first();

        if (!$subscription) {
            return $next($request);
        }

        if ($subscription->isActive()) {
            return $next($request);
        }

        if ($subscription->isOnGrace()) {
            return $next($request);
        }

        return response()->json([
            'message' => __('messages.subscription_expired'),
            'error' => 'subscription_required',
        ], 402);
    }
}
