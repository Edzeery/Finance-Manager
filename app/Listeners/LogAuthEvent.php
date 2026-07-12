<?php

namespace App\Listeners;

use App\Contracts\Services\ActivityLogServiceInterface;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\PasswordReset;

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
    }

    public function handleLogout(Logout $event): void
    {
        $this->log($event->user?->id, 'logout', 'auth.logout', [
            'guard' => $event->guard,
        ]);
    }

    public function handleRegistered(Registered $event): void
    {
        $this->log($event->user?->id, 'registered', 'auth.registered', []);
    }

    public function handleFailed(Failed $event): void
    {
        logger()->channel('auth')->warning('Failed login attempt', [
            'guard' => $event->guard,
            'email' => $event->credentials['email'] ?? 'unknown',
            'ip' => request()->ip(),
        ]);
    }

    public function handlePasswordReset(PasswordReset $event): void
    {
        $this->log($event->user?->id, 'password_reset', 'auth.password_reset', []);
    }

    public function subscribe(\Illuminate\Events\Dispatcher $events): array
    {
        return [
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
            Registered::class => 'handleRegistered',
            Failed::class => 'handleFailed',
            PasswordReset::class => 'handlePasswordReset',
        ];
    }

    private function log(?int $userId, string $action, string $descriptionKey, array $metadata): void
    {
        if (!$userId) return;

        $description = __("messages.{$descriptionKey}");
        $metadata = array_merge($metadata, [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        \App\Jobs\LogActivity::dispatch(
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
}
