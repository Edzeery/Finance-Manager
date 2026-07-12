<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetWorkspace
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $isSuperAdmin = $user->hasRole('super_admin');

            // Non-super-admin: auto-select first workspace if none selected
            if (!$isSuperAdmin && !$user->current_workspace_id) {
                $firstWorkspace = $user->workspaces()->first();
                if ($firstWorkspace) {
                    $user->update(['current_workspace_id' => $firstWorkspace->id]);
                }
            }

            // Set workspace config if currentWorkspace exists (respects super_admin selection too)
            if ($user->currentWorkspace) {
                $workspace = $user->currentWorkspace;
                config(['app.current_workspace' => $workspace]);
                config(['app.workspace_currency' => $workspace->currency]);
                config(['app.workspace_timezone' => $workspace->timezone]);
            } elseif ($user->current_workspace_id && !$isSuperAdmin) {
                // Non-super-admin: broken relationship — reset to first available
                $firstAvailable = $user->workspaces()->first();
                if ($firstAvailable) {
                    $user->update(['current_workspace_id' => $firstAvailable->id]);
                    config(['app.current_workspace' => $firstAvailable]);
                    config(['app.workspace_currency' => $firstAvailable->currency]);
                    config(['app.workspace_timezone' => $firstAvailable->timezone]);
                }
            }
            // Super admin with no currentWorkspace: config stays null → WorkspaceScope returns early → sees all data
        }

        return $next($request);
    }
}
