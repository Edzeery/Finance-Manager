<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiWorkspace
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $isSuperAdmin = $user->hasRole('super_admin');

        if (!$isSuperAdmin && !$user->current_workspace_id) {
            $firstWorkspace = $user->workspaces()->first();
            if ($firstWorkspace) {
                $user->update(['current_workspace_id' => $firstWorkspace->id]);
            } else {
                return response()->json(['message' => __('messages.no_workspace_selected')], 400);
            }
        }

        if (!$isSuperAdmin && !$user->workspaces()->where('id', $user->current_workspace_id)->exists()) {
            return response()->json(['message' => __('messages.invalid_workspace')], 403);
        }

        $workspace = $user->currentWorkspace;
        if ($workspace) {
            config(['app.current_workspace' => $workspace]);
            config(['app.workspace_currency' => $workspace->currency]);
            config(['app.workspace_timezone' => $workspace->timezone]);
        }

        return $next($request);
    }
}
