<?php

namespace App\Listeners;

use App\Concerns\ParsesUserAgent;
use App\Contracts\Services\ActivityLogServiceInterface;
use App\Jobs\LogActivity;
use App\Models\LoginAttempt;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use App\Services\NotificationService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Events\Dispatcher;

class LogAuthEvent
{
    use ParsesUserAgent;

    public function __construct(
        private readonly ActivityLogServiceInterface $activityLog,
        private readonly NotificationService $notificationService,
    ) {}

    public function handleLogin(Login $event): void
    {
        $this->log($event->user?->id, 'login', 'auth.login', [
            'guard' => $event->guard,
        ]);

        // Record successful login attempt
        $this->recordAttempt($event->user, 'success');

        // Detect new device
        if ($event->user && $event->guard === 'web') {
            $this->detectNewDevice($event->user);
            $this->notifyAdminOnUserLogin($event->user);
        }
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
        $user = User::where('email', $email)->first();
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

            if ($user) {
                // Throttle: skip if a suspicious notification was sent in the last 30 minutes
                $recentSuspicious = Notification::withoutGlobalScopes()
                    ->where('user_id', $user->id)
                    ->where('type', 'login_suspicious')
                    ->where('created_at', '>=', now()->subMinutes(30))
                    ->exists();

                if (! $recentSuspicious) {
                    $this->notificationService->loginSuspicious(
                        $user->id,
                        $ip,
                        __('notifications.suspicious_login_reason', [
                            'count' => LoginAttempt::recentFailuresForIp($ip),
                        ])
                    );
                }
            }
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

    private function detectNewDevice(User $user): void
    {
        $currentUA = request()->userAgent() ?? '';
        if (! $currentUA) {
            return;
        }

        // Throttle: skip if a new device notification was sent in the last 30 minutes
        $recentNotification = Notification::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('type', 'login_new_device')
            ->where('created_at', '>=', now()->subMinutes(30))
            ->exists();

        if ($recentNotification) {
            return;
        }

        $statusRecord = $user->statusRecord;
        if (! $statusRecord) {
            return;
        }

        // Compare against the last known login's user agent
        if ($statusRecord->last_user_agent !== null && $statusRecord->last_user_agent === $currentUA) {
            return;
        }

        $device = $this->parseDevice($currentUA);
        $browser = $this->parseBrowser($currentUA);
        $os = $this->parseOS($currentUA);
        $ip = request()->ip();

        $this->notificationService->loginNewDevice(
            $user->id,
            $device,
            $browser,
            $os,
            $ip
        );
    }

    private function notifyAdminOnUserLogin(User $user): void
    {
        $statusRecord = $user->statusRecord;
        $lastLogin = $statusRecord?->last_login_at;

        // New user (never logged in) or absent for 30+ days
        if ($lastLogin && $lastLogin->diffInDays(now()) < 30) {
            return;
        }

        $daysSinceLastLogin = $lastLogin ? (int) $lastLogin->diffInDays(now()) : 0;

        // Notify workspace owner(s)
        $workspaceIds = $user->workspaces()->pluck('workspaces.id');
        $adminRoleId = Role::where('slug', 'workspace_admin')->value('id');

        if (! $adminRoleId) {
            return;
        }

        $ownerIds = Workspace::query()
            ->join('workspace_role_user', 'workspaces.id', '=', 'workspace_role_user.workspace_id')
            ->where('workspace_role_user.role_id', $adminRoleId)
            ->whereIn('workspaces.id', $workspaceIds)
            ->pluck('workspace_role_user.user_id')
            ->unique()
            ->reject(fn ($id) => $id === $user->id);

        foreach ($ownerIds as $ownerId) {
            $this->notificationService->workspaceMemberLoggedIn(
                $ownerId,
                $user->name,
                $user->email,
                $daysSinceLastLogin
            );
        }
    }
}
