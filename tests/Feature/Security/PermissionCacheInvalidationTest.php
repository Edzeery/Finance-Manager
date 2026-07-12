<?php

namespace Tests\Feature\Security;

use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PermissionCacheInvalidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_remove_user_flushes_permission_cache(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create();
        $workspace->users()->attach($user->id);

        $role = Role::create([
            'name' => 'Viewer', 'slug' => 'workspace_viewer',
            'level' => 'workspace', 'is_system' => false, 'sort_order' => 1,
        ]);
        $user->workspaceRoleUsers()->attach($role->id, ['workspace_id' => $workspace->id]);
        $user->update(['current_workspace_id' => $workspace->id]);

        Cache::shouldReceive('forget')->atLeast()->once();
        Cache::shouldReceive('rememberForever')->andReturn(1);

        app(WorkspaceService::class)->removeUser($workspace, $user);

        $this->assertTrue(true);
    }
}
