<?php

namespace App\Http\Middleware;

use App\Concerns\ParsesUserAgent;
use App\Enums\OnlineStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackLastLogin
{
    use ParsesUserAgent;

    private const INACTIVE_THRESHOLD_MINUTES = 15;

    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        if ($request->is('api/*')) {
            return $next($request);
        }

        $user = auth()->user();
        $statusRecord = $user->statusRecord;

        if (! $statusRecord) {
            return $next($request);
        }

        $currentIp = $request->ip();
        $now = now();

        // Track login: every 5 min or on IP change
        if (
            ! $statusRecord->last_login_at
            || $statusRecord->last_login_at->diffInMinutes($now) > 5
            || $statusRecord->last_login_ip !== $currentIp
        ) {
            $userAgent = $request->userAgent() ?? '';
            $statusRecord->trackLogin(
                $currentIp,
                $userAgent,
                $this->parseDevice($userAgent),
                $this->parseBrowser($userAgent),
                $this->parseOS($userAgent),
            );
        }

        // Store login_at in session for session management page
        if (! session()->has('login_at')) {
            session(['login_at' => now()->toIso8601String()]);
        }

        // Track activity: every 2 min
        if (
            ! $statusRecord->last_activity_at
            || $statusRecord->last_activity_at->diffInMinutes($now) >= 2
        ) {
            $statusRecord->trackActivity();
        }

        // Auto-set offline if inactive > 15 minutes
        if (
            $statusRecord->online_status === OnlineStatus::Online
            && $statusRecord->isInactive(self::INACTIVE_THRESHOLD_MINUTES)
        ) {
            $statusRecord->trackLogout();
        }

        return $next($request);
    }
}
