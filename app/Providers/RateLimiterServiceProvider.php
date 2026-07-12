<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RateLimiterServiceProvider extends ServiceProvider
{
    protected function limit(string $name): int
    {
        return (int) config("finance.rate_limits.{$name}", 120);
    }

    protected function perMinute(string $name): int
    {
        return $this->limit($name);
    }

    public function boot(): void
    {
        RateLimiter::for('api-auth', function (HttpRequest $request) {
            return Limit::perMinute($this->limit('api-auth'))->by($request->input('email') ?: $request->ip());
        });

        RateLimiter::for('api', function (HttpRequest $request) {
            return Limit::perMinute($this->limit('api'))->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('api-workspace', function (HttpRequest $request) {
            return Limit::perMinute($this->limit('api-workspace'))->by($request->user()?->id . ':' . ($request->user()?->current_workspace_id ?: '*'));
        });

        RateLimiter::for('api-sensitive', function (HttpRequest $request) {
            return Limit::perMinute($this->limit('api-sensitive'))->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('super-admin-settings', function (HttpRequest $request) {
            return Limit::perMinute($this->limit('super-admin-settings'))->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('webhook', function (HttpRequest $request) {
            return Limit::perMinute($this->limit('webhook'))->by($request->ip());
        });

        RateLimiter::for('login', function (HttpRequest $request) {
            return Limit::perMinute($this->limit('login'))->by($request->input('email') ?: $request->ip());
        });

        RateLimiter::for('register', function (HttpRequest $request) {
            return Limit::perMinute($this->limit('register'))->by($request->ip());
        });

        RateLimiter::for('web', function (HttpRequest $request) {
            return Limit::perMinute($this->limit('web'))->by($request->user()?->id ?: $request->ip());
        });

        // Inline throttle named limiters (used in route middleware)
        RateLimiter::for('web-list', function (HttpRequest $request) {
            return Limit::perMinute($this->limit('web-list'))->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('web-search', function (HttpRequest $request) {
            return Limit::perMinute($this->limit('web-search'))->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('web-crud', function (HttpRequest $request) {
            return Limit::perMinute($this->limit('web-crud'))->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('web-delete', function (HttpRequest $request) {
            return Limit::perMinute($this->limit('web-delete'))->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('web-sensitive', function (HttpRequest $request) {
            return Limit::perMinute($this->limit('web-sensitive'))->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('web-proof', function (HttpRequest $request) {
            return Limit::perMinute($this->limit('web-proof'))->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('web-invite-resend', function (HttpRequest $request) {
            return Limit::perMinute($this->limit('web-invite-resend'))->by($request->user()?->id ?: $request->ip());
        });
    }
}
