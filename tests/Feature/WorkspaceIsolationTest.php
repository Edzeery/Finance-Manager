<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\ExpenseRepository;
use Database\Seeders\EnterpriseRolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceIsolationTest extends TestCase
{
    use RefreshDatabase;

    private Workspace $workspaceA;
    private Workspace $workspaceB;
    private User $userA;
    private User $userB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(EnterpriseRolePermissionSeeder::class);

        $this->workspaceA = Workspace::factory()->create(['name' => 'Workspace A']);
        $this->workspaceB = Workspace::factory()->create(['name' => 'Workspace B']);

        $this->userA = User::factory()->create();
        $this->userB = User::factory()->create();

        $this->userA->workspaces()->attach($this->workspaceA->id, []);
        $this->userB->workspaces()->attach($this->workspaceB->id, []);

        $adminRole = \App\Models\Role::where('slug', 'workspace_admin')->first();
        if ($adminRole) {
            $this->userA->workspaceRoleUsers()->attach($adminRole->id, ['workspace_id' => $this->workspaceA->id]);
            $this->userB->workspaceRoleUsers()->attach($adminRole->id, ['workspace_id' => $this->workspaceB->id]);
        }

        $this->userA->current_workspace_id = $this->workspaceA->id;
        $this->userA->save();
        $this->userB->current_workspace_id = $this->workspaceB->id;
        $this->userB->save();
    }

    public function test_ownership_filter_shows_only_own_records_without_permission(): void
    {
        $noPermissionUser = User::factory()->create();
        $noPermissionUser->workspaces()->attach($this->workspaceA->id, []);
        $noPermissionUser->current_workspace_id = $this->workspaceA->id;
        $noPermissionUser->save();

        $expenseA = Expense::factory()->create(['workspace_id' => $this->workspaceA->id, 'user_id' => $noPermissionUser->id]);
        $expenseB = Expense::factory()->create(['workspace_id' => $this->workspaceA->id, 'user_id' => $this->userB->id]);

        config(['app.current_workspace' => $this->workspaceA]);
        $this->actingAs($noPermissionUser);

        $repo = app(ExpenseRepository::class);
        $visible = $repo->forUser();
        $visibleIds = $visible->pluck('id')->toArray();

        $this->assertContains($expenseA->id, $visibleIds);
        $this->assertNotContains($expenseB->id, $visibleIds);
    }

    public function test_config_workspace_isolation_cannot_be_bypassed_with_falsy_value(): void
    {
        config(['app.current_workspace' => $this->workspaceA]);
        Expense::factory()->create(['workspace_id' => $this->workspaceA->id, 'user_id' => $this->userA->id]);

        config(['app.current_workspace' => 0]);

        $count = Expense::count();
        $this->assertEquals(0, $count);
    }

    public function test_config_workspace_isolation_bypasses_with_null(): void
    {
        config(['app.current_workspace' => $this->workspaceA]);
        Expense::factory()->create(['workspace_id' => $this->workspaceA->id, 'user_id' => $this->userA->id]);

        config(['app.current_workspace' => $this->workspaceB]);
        Expense::factory()->create(['workspace_id' => $this->workspaceB->id, 'user_id' => $this->userB->id]);

        config(['app.current_workspace' => null]);

        $this->assertEquals(2, Expense::count(), 'Null config should bypass workspace scope');
    }
}
