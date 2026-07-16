<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    protected array $except = [
        'account.inactive',
        'account.pending',
        'account.suspended',
        'account.banned',
        'account.status.*',
        'super.admin.*',
        'logout',
        'password.*',
        'locale.switch',
        'theme.switch',
        'currency.switch',
        'livewire.*',
        'verification.*',
        'sanctum.*',
        'payment.webhook.*',
    ];

    protected array $exceptPrefixes = [
        'super.admin',
        'password',
        'verification',
        'livewire',
        'sanctum',
        'payment.webhook',
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

        $statusRecord = $user->statusRecord;

        if (! $statusRecord) {
            return $next($request);
        }

        if ($statusRecord->isExpired() && $statusRecord->status === UserStatus::Suspended) {
            $statusRecord->changeStatus(UserStatus::Active, ' Suspension period expired');
            return $next($request);
        }

        $status = $statusRecord->status;

        if ($status->isAccessible()) {
            return $next($request);
        }

        $targetRoute = match ($status) {
            UserStatus::Inactive => 'account.inactive',
            UserStatus::Pending  => 'account.pending',
            UserStatus::Suspended => 'account.suspended',
            UserStatus::Banned   => 'account.banned',
            default              => 'account.inactive',
        };

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('auth.account_' . $status->value),
                'status' => $status->value,
                'reason' => $statusRecord->status_reason,
            ], 403);
        }

        return redirect()->route($targetRoute);
    }

    protected function inExceptArray(?string $routeName): bool
    {
        if (! $routeName) {
            return false;
        }

        foreach ($this->exceptPrefixes as $prefix) {
            if (str_starts_with($routeName, $prefix)) {
                return true;
            }
        }

        return in_array($routeName, $this->except);
    }
}
