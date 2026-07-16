<?php

// bootstrap\app.php
use App\Http\Middleware\ApiWorkspace;
use App\Http\Middleware\CheckActiveSubscription;
use App\Http\Middleware\CheckApiAbility;
use App\Http\Middleware\CheckUserStatus;
use App\Http\Middleware\CheckApiQuota;
use App\Http\Middleware\CheckApiSubscription;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckPlanFeature;
use App\Http\Middleware\CheckSubscriptionStatus;
use App\Http\Middleware\EnsureOnboardingCompleted;
use App\Http\Middleware\ForceTwoFactor;
use App\Http\Middleware\HasPlatformPermission;
use App\Http\Middleware\HasPlatformRole;
use App\Http\Middleware\HasWorkspacePermission;
use App\Http\Middleware\HasWorkspaceRole;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SetTheme;
use App\Http\Middleware\SetWorkspace;
use App\Http\Middleware\SuperAdmin;
use App\Http\Middleware\TrackLastLogin;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',

    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'payment/webhook/*',
        ]);

        $middleware->alias([
            'workspace' => ApiWorkspace::class,
            'workspace.set' => SetWorkspace::class,
            'workspace.role' => HasWorkspaceRole::class,
            'workspace.permission' => HasWorkspacePermission::class,
            'platform.role' => HasPlatformRole::class,
            'platform.permission' => HasPlatformPermission::class,
            'super.admin' => SuperAdmin::class,
            'permission' => CheckPermission::class,
            'ability' => CheckApiAbility::class,
            'onboarding' => EnsureOnboardingCompleted::class,
            'two-factor' => ForceTwoFactor::class,
            'locale' => SetLocale::class,
            'theme' => SetTheme::class,
            'security.headers' => SecurityHeaders::class,
            'subscription' => CheckActiveSubscription::class,
            'subscription.status' => CheckSubscriptionStatus::class,
            'subscription.api' => CheckApiSubscription::class,
            'plan.feature' => CheckPlanFeature::class,
            'quota' => CheckApiQuota::class,
            'user.status' => CheckUserStatus::class,
            'track.login' => TrackLastLogin::class,
        ]);

        $middleware->web(append: [
            SetLocale::class,
            SetTheme::class,
            SetWorkspace::class,
            CheckUserStatus::class,
            TrackLastLogin::class,
            EnsureOnboardingCompleted::class,
            SecurityHeaders::class,
            'throttle:web',
        ]);

        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->api(append: [
            'security.headers',
            'throttle:api',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => __('auth.invalid_token'),
                    'status' => 401,
                ], 401);
            }
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage() ?: __('auth.forbidden'),
                    'status' => 403,
                ], 403);
            }
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage() ?: __('messages.not_found'),
                    'status' => $e->getStatusCode(),
                ], $e->getStatusCode());
            }
        });
    })->create();
