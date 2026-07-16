<?php

namespace App\Http\Middleware;

use App\Models\ApiUsageLog;
use App\Services\ApiQuotaService;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class CheckApiQuota
{
    public function __construct(private ApiQuotaService $quotaService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $subscription = $user->activeSubscription();
        $plan = $subscription?->plan;
        $limits = $this->quotaService->getLimits($plan);

        if (array_sum($limits) === 0) {
            $this->logRequest($request, $user);

            return $next($request);
        }

        $usage = $this->quotaService->getUsage($user->id);
        $remaining = [
            'minute' => max(0, $limits['minute'] - $usage['minute']),
            'hour' => max(0, $limits['hour'] - $usage['hour']),
            'day' => max(0, $limits['day'] - $usage['day']),
        ];

        $exceeded = [];
        if ($limits['minute'] > 0 && $usage['minute'] >= $limits['minute']) {
            $exceeded[] = 'minute';
        }
        if ($limits['hour'] > 0 && $usage['hour'] >= $limits['hour']) {
            $exceeded[] = 'hour';
        }
        if ($limits['day'] > 0 && $usage['day'] >= $limits['day']) {
            $exceeded[] = 'day';
        }

        if (! empty($exceeded)) {
            $retryAfter = $this->retryAfter($exceeded);
            $resetTimes = $this->quotaService->getResetTimes();

            return response()->json([
                'message' => 'API rate limit exceeded.',
                'errors' => [
                    'quota' => 'You have exceeded your API request quota for the '.$exceeded[0].' window. Please wait for the quota to reset or upgrade your plan.',
                ],
                'quota' => [
                    'limit' => $limits,
                    'used' => $usage,
                    'remaining' => $remaining,
                    'reset' => $resetTimes,
                ],
            ], 429)->withHeaders([
                'Retry-After' => $retryAfter,
                'X-RateLimit-Limit-Minute' => $limits['minute'],
                'X-RateLimit-Remaining-Minute' => 0,
                'X-RateLimit-Limit-Hour' => $limits['hour'],
                'X-RateLimit-Remaining-Hour' => $remaining['hour'],
                'X-RateLimit-Limit-Day' => $limits['day'],
                'X-RateLimit-Remaining-Day' => $remaining['day'],
            ]);
        }

        $this->quotaService->increment($user->id);
        $this->logRequest($request, $user);

        $usage = $this->quotaService->getUsage($user->id);
        $remaining = [
            'minute' => max(0, $limits['minute'] - $usage['minute']),
            'hour' => max(0, $limits['hour'] - $usage['hour']),
            'day' => max(0, $limits['day'] - $usage['day']),
        ];

        $response = $next($request);

        return $this->addHeaders($response, $limits, $remaining, $this->quotaService->getResetTimes());
    }

    private function logRequest(Request $request, $user): void
    {
        $token = $request->bearerToken();
        $tokenId = null;

        if ($token) {
            $hashed = hash('sha256', explode('|', $token, 2)[1] ?? $token);
            $tokenModel = PersonalAccessToken::where('token', $hashed)->first();
            $tokenId = $tokenModel?->id;
        }

        ApiUsageLog::create([
            'user_id' => $user->id,
            'token_id' => $tokenId,
            'workspace_id' => $user->current_workspace_id,
            'method' => $request->method(),
            'route' => $request->path(),
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);
    }

    private function addHeaders($response, array $limits, array $remaining, array $reset): Response
    {
        if ($response instanceof Response) {
            $response->headers->set('X-RateLimit-Limit-Minute', $limits['minute']);
            $response->headers->set('X-RateLimit-Remaining-Minute', $remaining['minute']);
            $response->headers->set('X-RateLimit-Reset-Minute', $reset['minute']);

            $response->headers->set('X-RateLimit-Limit-Hour', $limits['hour']);
            $response->headers->set('X-RateLimit-Remaining-Hour', $remaining['hour']);
            $response->headers->set('X-RateLimit-Reset-Hour', $reset['hour']);

            $response->headers->set('X-RateLimit-Limit-Day', $limits['day']);
            $response->headers->set('X-RateLimit-Remaining-Day', $remaining['day']);
            $response->headers->set('X-RateLimit-Reset-Day', $reset['day']);
        }

        return $response;
    }

    private function retryAfter(array $exceeded): int
    {
        $now = now();
        if (in_array('minute', $exceeded)) {
            return $now->copy()->endOfMinute()->diffInSeconds($now) + 1;
        }
        if (in_array('hour', $exceeded)) {
            return $now->copy()->endOfHour()->diffInSeconds($now) + 1;
        }

        return $now->copy()->endOfDay()->diffInSeconds($now) + 1;
    }
}
