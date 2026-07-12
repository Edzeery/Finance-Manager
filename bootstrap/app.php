<?php
// bootstrap\app.php
use Illuminate\Foundation\Application;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
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
            'workspace' => \App\Http\Middleware\ApiWorkspace::class,
            'workspace.set' => \App\Http\Middleware\SetWorkspace::class,
            'workspace.role' => \App\Http\Middleware\HasWorkspaceRole::class,
            'workspace.permission' => \App\Http\Middleware\HasWorkspacePermission::class,
            'platform.role' => \App\Http\Middleware\HasPlatformRole::class,
            'platform.permission' => \App\Http\Middleware\HasPlatformPermission::class,
            'super.admin' => \App\Http\Middleware\SuperAdmin::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'ability' => \App\Http\Middleware\CheckApiAbility::class,
            'onboarding' => \App\Http\Middleware\EnsureOnboardingCompleted::class,
            'two-factor' => \App\Http\Middleware\ForceTwoFactor::class,
            'locale' => \App\Http\Middleware\SetLocale::class,
            'theme' => \App\Http\Middleware\SetTheme::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'subscription' => \App\Http\Middleware\CheckActiveSubscription::class,
            'subscription.status' => \App\Http\Middleware\CheckSubscriptionStatus::class,
            'subscription.api' => \App\Http\Middleware\CheckApiSubscription::class,
            'plan.feature' => \App\Http\Middleware\CheckPlanFeature::class,
            'quota' => \App\Http\Middleware\CheckApiQuota::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\SetTheme::class,
            \App\Http\Middleware\SetWorkspace::class,
            \App\Http\Middleware\EnsureOnboardingCompleted::class,
            \App\Http\Middleware\SecurityHeaders::class,
            'throttle:web',
        ]);

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
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
                    'status'  => 401,
                ], 401);
            }
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage() ?: __('auth.forbidden'),
                    'status'  => 403,
                ], 403);
            }
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage() ?: __('messages.not_found'),
                    'status'  => $e->getStatusCode(),
                ], $e->getStatusCode());
            }
        });
    })->create();
