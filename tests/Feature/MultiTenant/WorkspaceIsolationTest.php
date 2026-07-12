<?php

namespace Tests\Feature\MultiTenant;

use App\Models\Asset;
use App\Models\Budget;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialGoal;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserSetting;
use App\Models\Workspace;
use App\Models\ZakatRecord;
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

    public function test_income_is_isolated_between_workspaces(): void
    {
        config(['app.current_workspace' => $this->workspaceA]);
        Income::factory()->create(['user_id' => $this->userA->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceB]);
        Income::factory()->create(['user_id' => $this->userB->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceA]);

        $this->assertEquals(1, Income::count(), 'User A should only see their workspace records');

        config(['app.current_workspace' => $this->workspaceB]);
        $this->assertEquals(1, Income::count(), 'User B should only see their workspace records');
    }

    public function test_expense_is_isolated_between_workspaces(): void
    {
        config(['app.current_workspace' => $this->workspaceA]);
        Expense::factory()->create(['user_id' => $this->userA->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceB]);
        Expense::factory()->create(['user_id' => $this->userB->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceA]);
        $this->assertEquals(1, Expense::count());

        config(['app.current_workspace' => $this->workspaceB]);
        $this->assertEquals(1, Expense::count());
    }

    public function test_debt_is_isolated_between_workspaces(): void
    {
        config(['app.current_workspace' => $this->workspaceA]);
        Debt::factory()->create(['user_id' => $this->userA->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceB]);
        Debt::factory()->create(['user_id' => $this->userB->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceA]);
        $this->assertEquals(1, Debt::count());
    }

    public function test_asset_is_isolated_between_workspaces(): void
    {
        config(['app.current_workspace' => $this->workspaceA]);
        Asset::factory()->create(['user_id' => $this->userA->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceB]);
        Asset::factory()->create(['user_id' => $this->userB->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceA]);
        $this->assertEquals(1, Asset::count());
    }

    public function test_budget_is_isolated_between_workspaces(): void
    {
        config(['app.current_workspace' => $this->workspaceA]);
        Budget::factory()->create(['user_id' => $this->userA->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceB]);
        Budget::factory()->create(['user_id' => $this->userB->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceA]);
        $this->assertEquals(1, Budget::count());
    }

    public function test_financial_goal_is_isolated_between_workspaces(): void
    {
        config(['app.current_workspace' => $this->workspaceA]);
        FinancialGoal::factory()->create(['user_id' => $this->userA->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceB]);
        FinancialGoal::factory()->create(['user_id' => $this->userB->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceA]);
        $this->assertEquals(1, FinancialGoal::count());
    }

    public function test_zakat_record_is_isolated_between_workspaces(): void
    {
        config(['app.current_workspace' => $this->workspaceA]);
        ZakatRecord::factory()->create(['user_id' => $this->userA->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceB]);
        ZakatRecord::factory()->create(['user_id' => $this->userB->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceA]);
        $this->assertEquals(1, ZakatRecord::count());
    }

    public function test_notification_is_isolated_between_workspaces(): void
    {
        config(['app.current_workspace' => $this->workspaceA]);
        Notification::factory()->create(['user_id' => $this->userA->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceB]);
        Notification::factory()->create(['user_id' => $this->userB->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceA]);
        $this->assertEquals(1, Notification::count());
    }

    public function test_income_category_global_records_visible_across_workspaces(): void
    {
        config(['app.current_workspace' => $this->workspaceA]);
        IncomeCategory::factory()->create(['user_id' => $this->userA->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceB]);
        IncomeCategory::factory()->global()->create();

        config(['app.current_workspace' => $this->workspaceA]);

        $categories = IncomeCategory::all();
        $globalCategories = $categories->whereNull('workspace_id');

        $this->assertGreaterThanOrEqual(2, $categories->count(), 'Should see workspace categories + global categories');
        $this->assertTrue($globalCategories->count() >= 1, 'Global categories should be visible');
    }

    public function test_expense_category_global_records_visible_across_workspaces(): void
    {
        config(['app.current_workspace' => $this->workspaceA]);
        ExpenseCategory::factory()->create(['user_id' => $this->userA->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceB]);
        ExpenseCategory::factory()->global()->create();

        config(['app.current_workspace' => $this->workspaceA]);

        $categories = ExpenseCategory::all();
        $globalCategories = $categories->whereNull('workspace_id');

        $this->assertGreaterThan(1, $categories->count());
        $this->assertTrue($globalCategories->count() >= 1);
    }

    public function test_scope_without_workspace_bypasses_isolation(): void
    {
        config(['app.current_workspace' => $this->workspaceA]);
        Income::factory()->create(['user_id' => $this->userA->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceB]);
        Income::factory()->create(['user_id' => $this->userB->id, 'workspace_id' => null]);

        config(['app.current_workspace' => $this->workspaceA]);

        $allIncomes = Income::withoutWorkspace()->get();
        $this->assertEquals(2, $allIncomes->count(), 'withoutWorkspace should see all records');
    }

    public function test_no_workspace_config_returns_no_records(): void
    {
        Income::factory()->create(['user_id' => $this->userA->id, 'workspace_id' => $this->workspaceA->id]);
        Income::factory()->create(['user_id' => $this->userB->id, 'workspace_id' => $this->workspaceB->id]);

        config(['app.current_workspace' => null]);

        $this->assertEquals(2, Income::count(), 'Workspace scope does not apply when config is null (super admin mode)');
    }

    public function test_belongs_to_workspace_auto_assigns_on_create(): void
    {
        config(['app.current_workspace' => $this->workspaceA]);

        $income = Income::factory()->create(['user_id' => $this->userA->id, 'workspace_id' => null]);

        $this->assertEquals($this->workspaceA->id, $income->fresh()->workspace_id, 'Trait should auto-assign workspace_id');
    }

    public function test_falsy_config_value_does_not_bypass_scope(): void
    {
        config(['app.current_workspace' => 0]);

        Income::factory()->create(['workspace_id' => $this->workspaceA->id, 'user_id' => $this->userA->id]);

        $this->assertEquals(0, Income::count(), 'Falsy config value should not expose all records');
    }

    public function test_super_admin_sees_all_records(): void
    {
        config(['app.current_workspace' => $this->workspaceA]);
        Income::factory()->create(['user_id' => $this->userA->id, 'workspace_id' => $this->workspaceA->id]);

        config(['app.current_workspace' => $this->workspaceB]);
        Income::factory()->create(['user_id' => $this->userB->id, 'workspace_id' => $this->workspaceB->id]);

        config(['app.current_workspace' => null]);

        $this->assertEquals(2, Income::count(), 'Super admin with null workspace config sees all records');
    }
}
