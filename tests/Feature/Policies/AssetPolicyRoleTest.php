<?php

namespace Tests\Feature\Policies;

use App\Models\Asset;
use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use App\Policies\AssetPolicy;
use Database\Seeders\EnterpriseRolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetPolicyRoleTest extends TestCase
{
    use RefreshDatabase;

    private AssetPolicy $policy;
    private User $owner;
    private User $viewer;
    private User $admin;
    private Asset $asset;
    private Workspace $workspace;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(EnterpriseRolePermissionSeeder::class);

        $this->workspace = Workspace::factory()->create();
        $this->policy = new AssetPolicy;
        $this->owner = User::factory()->create(['current_workspace_id' => $this->workspace->id]);
        $this->owner->workspaces()->attach($this->workspace->id);
        $this->asset = Asset::factory()->create(['user_id' => $this->owner->id]);

        $this->viewer = User::factory()->create();
        $this->assignWorkspaceRole($this->viewer, 'workspace_viewer');

        $this->admin = User::factory()->create();
        $this->assignWorkspaceRole($this->admin, 'workspace_admin');
    }

    private function assignWorkspaceRole(User $user, string $slug): void
    {
        $role = Role::where('slug', $slug)->first();
        $user->workspaces()->syncWithoutDetaching([$this->workspace->id]);
        $user->workspaceRoleUsers()->attach($role->id, ['workspace_id' => $this->workspace->id]);
        $user->current_workspace_id = $this->workspace->id;
        $user->save();
        $user->refresh();
    }

    public function test_admin_has_workspace_role(): void
    {
        $this->assertNotNull($this->admin->currentWorkspace, 'currentWorkspace is null');
        $role = $this->admin->currentWorkspaceRole();
        $this->assertNotNull($role, 'currentWorkspaceRole is null');
        $this->assertTrue($role->hasPermission('asset.view'), 'role does not have asset.view');
        $this->assertTrue($this->admin->hasPermission('asset.view'), 'user does not have asset.view via hasPermission');
    }

    public function test_owner_can_view(): void
    {
        $this->assertTrue($this->policy->view($this->owner, $this->asset));
    }

    public function test_viewer_cannot_view_others_asset(): void
    {
        $this->assertTrue($this->policy->view($this->viewer, $this->asset));
    }

    public function test_admin_can_view(): void
    {
        $this->assertTrue($this->policy->view($this->admin, $this->asset));
    }

    public function test_viewer_cannot_update(): void
    {
        $this->assertFalse($this->policy->update($this->viewer, $this->asset));
    }

    public function test_admin_can_update(): void
    {
        $this->assertTrue($this->policy->update($this->admin, $this->asset));
    }

    public function test_viewer_cannot_delete(): void
    {
        $this->assertFalse($this->policy->delete($this->viewer, $this->asset));
    }

    public function test_admin_can_delete(): void
    {
        $this->assertTrue($this->policy->delete($this->admin, $this->asset));
    }

    public function test_create_always_allowed(): void
    {
        $this->assertTrue($this->policy->create($this->owner));
        $this->assertTrue($this->policy->create($this->viewer));
        $this->assertTrue($this->policy->create($this->admin));
    }
}
