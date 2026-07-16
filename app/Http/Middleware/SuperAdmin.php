<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('super.admin.login');
        }

        if (! auth()->user()->hasRole('super_admin')) {
            abort(403, __('messages.unauthorized'));
        }

        return $next($request);
    }
}
