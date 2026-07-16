<?php

namespace Tests\Feature\Policies;

use App\Models\Asset;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\EnterpriseRolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedPolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $otherUser;

    private Workspace $workspace;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(EnterpriseRolePermissionSeeder::class);

        $this->workspace = Workspace::factory()->create();
        $this->owner = User::factory()->create(['current_workspace_id' => $this->workspace->id]);
        $this->otherUser = User::factory()->create();
    }

    private function assignWorkspaceRole(User $user, string $slug): void
    {
        $role = Role::where('slug', $slug)->first();
        $user->workspaceRoleUsers()->attach($role->id, ['workspace_id' => $this->workspace->id]);
        $user->current_workspace_id = $this->workspace->id;
        $user->save();
        $user->refresh();
    }

    public function test_admin_can_view_any_asset(): void
    {
        $this->assignWorkspaceRole($this->owner, 'workspace_admin');
        $asset = Asset::factory()->create(['user_id' => $this->owner->id]);
        $this->assertTrue(\Gate::forUser($this->owner)->allows('view', $asset));
    }

    public function test_admin_can_update_any_asset(): void
    {
        $admin = User::factory()->create();
        $this->assignWorkspaceRole($admin, 'workspace_admin');
        $asset = Asset::factory()->create(['user_id' => $this->owner->id]);
        $this->assertTrue(\Gate::forUser($admin)->allows('update', $asset));
    }

    public function test_admin_can_delete_any_asset(): void
    {
        $admin = User::factory()->create();
        $this->assignWorkspaceRole($admin, 'workspace_admin');
        $asset = Asset::factory()->create(['user_id' => $this->owner->id]);
        $this->assertTrue(\Gate::forUser($admin)->allows('delete', $asset));
    }

    public function test_owner_still_has_access(): void
    {
        $asset = Asset::factory()->create(['user_id' => $this->owner->id]);
        $this->assertTrue(\Gate::forUser($this->owner)->allows('view', $asset));
        $this->assertTrue(\Gate::forUser($this->owner)->allows('update', $asset));
    }

    public function test_other_user_still_cannot_access(): void
    {
        $asset = Asset::factory()->create(['user_id' => $this->owner->id]);
        $this->assertFalse(\Gate::forUser($this->otherUser)->allows('view', $asset));
        $this->assertFalse(\Gate::forUser($this->otherUser)->allows('update', $asset));
    }

    public function test_admin_can_update_any_expense(): void
    {
        $admin = User::factory()->create();
        $this->assignWorkspaceRole($admin, 'workspace_admin');
        $expense = Expense::factory()->create(['user_id' => $this->owner->id]);
        $this->assertTrue(\Gate::forUser($admin)->allows('update', $expense));
    }

    public function test_admin_can_delete_any_income(): void
    {
        $admin = User::factory()->create();
        $this->assignWorkspaceRole($admin, 'workspace_admin');
        $income = Income::factory()->create(['user_id' => $this->owner->id]);
        $this->assertTrue(\Gate::forUser($admin)->allows('delete', $income));
    }

    public function test_report_permission(): void
    {
        $this->assignWorkspaceRole($this->owner, 'workspace_admin');
        $this->assertTrue(\Gate::forUser($this->owner)->allows('report.view'));
        $this->assertFalse(\Gate::forUser($this->otherUser)->allows('report.view'));
    }
}
