<?php

namespace App\Http\Controllers;

use App\Models\Workspace;

class WorkspaceSwitchController extends Controller
{
    public function switch(Workspace $workspace)
    {
        $user = auth()->user();
        if (!$user->hasRole('super_admin') && !$user->workspaces()->where('workspace_id', $workspace->id)->exists()) {
            abort(403);
        }
        $user->switchWorkspace($workspace);
        return redirect()->back();
    }
}
