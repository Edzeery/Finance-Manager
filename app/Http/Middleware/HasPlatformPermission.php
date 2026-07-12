<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasPlatformPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user() || !$request->user()->hasPlatformPermission($permission)) {
            abort(403, __('messages.unauthorized'));
        }

        return $next($request);
    }
}
