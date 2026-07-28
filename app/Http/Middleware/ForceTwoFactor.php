<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceTwoFactor
{
    private const FORCED_ROLES = ['super_admin', 'deputy_super_admin', 'workspace_admin'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        $hasForcedRole = collect(self::FORCED_ROLES)->contains(fn ($r) => $user->hasRole($r));

        if ($hasForcedRole && ! $user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.setup')
                ->with('warning', __('auth.2fa_required_for_admin'));
        }

        return $next($request);
    }
}
