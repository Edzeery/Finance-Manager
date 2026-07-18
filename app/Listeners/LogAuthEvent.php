<?php

namespace App\Listeners;

use App\Contracts\Services\ActivityLogServiceInterface;
use App\Jobs\LogActivity;
use App\Models\LoginAttempt;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Events\Dispatcher;

class LogAuthEvent
{
    public function __construct(
        private readonly ActivityLogServiceInterface $activityLog,
    ) {}

    public function handleLogin(Login $event): void
    {
        $this->log($event->user?->id, 'login', 'auth.login', [
            'guard' => $event->guard,
        ]);

        // Record successful login attempt
        $this->recordAttempt($event->user, 'success');
    }

    public function handleLogout(Logout $event): void
    {
        $this->log($event->user?->id, 'logout', 'general.logout', [
            'guard' => $event->guard,
        ]);

        if ($event->user?->statusRecord) {
            $event->user->statusRecord->trackLogout();
        }
    }

    public function handleRegistered(Registered $event): void
    {
        $this->log($event->user?->id, 'registered', 'auth.registered', []);
    }

    public function handleFailed(Failed $event): void
    {
        $email = $event->credentials['email'] ?? 'unknown';
        $ip = request()->ip();

        logger()->channel('auth')->warning('Failed login attempt', [
            'guard' => $event->guard,
            'email' => $email,
            'ip' => $ip,
        ]);

        // Record failed login attempt
        $user = \App\Models\User::where('email', $email)->first();
        $suspicious = LoginAttempt::detectSuspicious($email, $ip);

        $this->recordAttempt($user, 'failed', [
            'failure_reason' => 'invalid_credentials',
            'suspicious' => $suspicious,
        ]);

        // Log suspicious activity
        if ($suspicious) {
            logger()->channel('auth')->critical('Suspicious login activity detected', [
                'email' => $email,
                'ip' => $ip,
                'recent_failures' => LoginAttempt::recentFailuresForIp($ip),
            ]);
        }
    }

    public function handlePasswordReset(PasswordReset $event): void
    {
        $this->log($event->user?->id, 'password_reset', 'auth.password_reset', []);
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
            Registered::class => 'handleRegistered',
            Failed::class => 'handleFailed',
            PasswordReset::class => 'handlePasswordReset',
        ];
    }

    private function recordAttempt(?object $user, string $status, array $extra = []): void
    {
        $request = request();
        $userAgent = $request->userAgent() ?? '';

        LoginAttempt::create(array_merge([
            'user_id' => $user?->id,
            'email' => $user?->email ?? ($request->input('email') ?? 'unknown'),
            'status' => $status,
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'device' => $this->parseDevice($userAgent),
            'browser' => $this->parseBrowser($userAgent),
            'os' => $this->parseOS($userAgent),
            'created_at' => now(),
        ], $extra));
    }

    private function log(?int $userId, string $action, string $descriptionKey, array $metadata): void
    {
        if (! $userId) {
            return;
        }

        $description = __("messages.{$descriptionKey}");
        $metadata = array_merge($metadata, [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        LogActivity::dispatch(
            $userId,
            $action,
            'auth',
            $userId,
            $description,
            $metadata,
            request()->ip(),
            request()->userAgent(),
        );
    }

    private function parseDevice(string $userAgent): string
    {
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $userAgent)) {
            return preg_match('/iPad|iPod/i', $userAgent) ? 'tablet' : 'phone';
        }
        return 'desktop';
    }

    private function parseBrowser(string $userAgent): string
    {
        $browsers = [
            'Edg/' => 'Edge', 'OPR' => 'Opera', 'Chrome' => 'Chrome',
            'Firefox' => 'Firefox', 'Safari' => 'Safari', 'MSIE' => 'IE', 'Trident' => 'IE',
        ];
        foreach ($browsers as $pattern => $name) {
            if (stripos($userAgent, $pattern) !== false) {
                return $name;
            }
        }
        return 'Unknown';
    }

    private function parseOS(string $userAgent): string
    {
        $oses = [
            'Windows NT 10' => 'Windows 10/11', 'Mac OS X' => 'macOS',
            'Android' => 'Android', 'iPhone OS' => 'iOS', 'iPad' => 'iPadOS', 'Linux' => 'Linux',
        ];
        foreach ($oses as $pattern => $name) {
            if (stripos($userAgent, $pattern) !== false) {
                return $name;
            }
        }
        return 'Unknown';
    }
}
