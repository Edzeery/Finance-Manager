<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        $slugs = explode('|', $permission);

        foreach ($slugs as $slug) {
            if ($user && $user->hasPermission(trim($slug))) {
                return $next($request);
            }
        }

        abort(403, __('messages.unauthorized'));
    }
}
