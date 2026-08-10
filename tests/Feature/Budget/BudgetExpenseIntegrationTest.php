<?php

namespace Tests\Feature\Budget;

use App\Models\Budget;
use App\Models\BudgetCategory;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithWorkspacePermission;

class BudgetExpenseIntegrationTest extends TestCase
{
    use RefreshDatabase;
    use WithWorkspacePermission;

    private User $user;

    private ExpenseCategory $category;

    private Budget $budget;

    private BudgetCategory $budgetCategory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpWorkspacePermission();
        $this->user = $this->workspaceUser;

        $this->category = ExpenseCategory::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'total_amount' => 2000,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'is_active' => true,
        ]);

        $this->budgetCategory = BudgetCategory::factory()->create([
            'budget_id' => $this->budget->id,
            'workspace_id' => $this->workspace->id,
            'expense_category_id' => $this->category->id,
            'allocated_amount' => 1000,
            'spent_amount' => 0,
        ]);
    }

    public function test_creating_expense_updates_budget_spent_amount(): void
    {
        $this->actingAs($this->user)
            ->post(route('expense.store'), [
                'category_id' => $this->category->id,
                'amount' => 500,
                'date' => now()->toDateString(),
                'description' => 'Test expense',
            ])
            ->assertRedirect(route('expense.index'));

        $this->budgetCategory->refresh();
        $this->assertEquals(500, $this->budgetCategory->spent_amount);
    }

    public function test_creating_expense_above_budget_returns_validation_error(): void
    {
        $this->actingAs($this->user)
            ->post(route('expense.store'), [
                'category_id' => $this->category->id,
                'amount' => 1500,
                'date' => now()->toDateString(),
                'description' => 'Over budget',
            ])
            ->assertSessionHasErrors('amount');
    }

    public function test_creating_expense_without_budget_is_allowed(): void
    {
        $noBudgetCategory = ExpenseCategory::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('expense.store'), [
                'category_id' => $noBudgetCategory->id,
                'amount' => 500,
                'date' => now()->toDateString(),
                'description' => 'No budget category',
            ])
            ->assertRedirect(route('expense.index'));
    }

    public function test_deleting_expense_decreases_budget_spent_amount(): void
    {
        $expense = Expense::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'category_id' => $this->category->id,
            'amount' => 300,
            'date' => now()->toDateString(),
        ]);

        $this->budgetCategory->recalculateSpentAmount();
        $this->assertEquals(300, $this->budgetCategory->fresh()->spent_amount);

        $this->actingAs($this->user)
            ->delete(route('expense.destroy', $expense))
            ->assertRedirect(route('expense.index'));

        $this->assertEquals(0, $this->budgetCategory->fresh()->spent_amount);
    }

    public function test_updating_expense_amount_recalculates_budget(): void
    {
        $expense = Expense::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'category_id' => $this->category->id,
            'amount' => 200,
            'date' => now()->toDateString(),
        ]);

        $this->budgetCategory->recalculateSpentAmount();
        $this->assertEquals(200, $this->budgetCategory->fresh()->spent_amount);

        $this->actingAs($this->user)
            ->put(route('expense.update', $expense), [
                'category_id' => $this->category->id,
                'amount' => 600,
                'date' => now()->toDateString(),
            ])
            ->assertRedirect(route('expense.index'));

        $this->assertEquals(600, $this->budgetCategory->fresh()->spent_amount);
    }

    public function test_restoring_expense_increases_budget_spent_amount(): void
    {
        $expense = Expense::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'category_id' => $this->category->id,
            'amount' => 400,
            'date' => now()->toDateString(),
        ]);

        $expense->delete();

        $this->assertEquals(0, $this->budgetCategory->fresh()->spent_amount);

        $this->actingAs($this->user)
            ->patch(route('expense.restore', $expense->id))
            ->assertRedirect();

        $this->assertEquals(400, $this->budgetCategory->fresh()->spent_amount);
    }

    public function test_multiple_expenses_accumulate_in_budget(): void
    {
        Expense::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'category_id' => $this->category->id,
            'amount' => 200,
            'date' => now()->toDateString(),
        ]);

        Expense::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'category_id' => $this->category->id,
            'amount' => 300,
            'date' => now()->toDateString(),
        ]);

        $this->budgetCategory->recalculateSpentAmount();
        $this->assertEquals(500, $this->budgetCategory->fresh()->spent_amount);

        $this->actingAs($this->user)
            ->post(route('expense.store'), [
                'category_id' => $this->category->id,
                'amount' => 400,
                'date' => now()->toDateString(),
            ])
            ->assertRedirect(route('expense.index'));

        $this->assertEquals(900, $this->budgetCategory->fresh()->spent_amount);
    }

    public function test_creating_expense_at_budget_limit_is_allowed(): void
    {
        $this->actingAs($this->user)
            ->post(route('expense.store'), [
                'category_id' => $this->category->id,
                'amount' => 1000,
                'date' => now()->toDateString(),
                'description' => 'At limit',
            ])
            ->assertRedirect(route('expense.index'));

        $this->assertEquals(1000, $this->budgetCategory->fresh()->spent_amount);
    }

    public function test_creating_expense_one_cent_over_budget_returns_error(): void
    {
        $this->actingAs($this->user)
            ->post(route('expense.store'), [
                'category_id' => $this->category->id,
                'amount' => 1000.01,
                'date' => now()->toDateString(),
                'description' => 'One cent over',
            ])
            ->assertSessionHasErrors('amount');
    }

    public function test_categories_page_prefers_newest_start_date_budget_when_overlapping(): void
    {
        // تُنشأ ميزانية أقدم أولاً ثم أحدث — بترتيب صفوف معكوس بالنسبة للقاعدة،
        // للتأكد أن الاختيار حتمي ولا يعتمد على ترتيب الصفوف في قاعدة البيانات.
        $olderBudget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'total_amount' => 2000,
            'start_date' => now()->subMonths(2),
            'end_date' => now()->addMonth(),
            'is_active' => true,
        ]);

        $newerBudget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'total_amount' => 2000,
            'start_date' => now()->subWeeks(1),
            'end_date' => now()->addMonth(),
            'is_active' => true,
        ]);

        BudgetCategory::factory()->create([
            'budget_id' => $olderBudget->id,
            'workspace_id' => $this->workspace->id,
            'expense_category_id' => $this->category->id,
            'allocated_amount' => 500,
            'spent_amount' => 50,
        ]);

        BudgetCategory::factory()->create([
            'budget_id' => $newerBudget->id,
            'workspace_id' => $this->workspace->id,
            'expense_category_id' => $this->category->id,
            'allocated_amount' => 900,
            'spent_amount' => 90,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('budget.categories'))
            ->assertOk();

        $categories = $response->viewData('categories');
        $info = $categories->firstWhere('id', $this->category->id)?->budgetInfo;

        $this->assertNotNull($info);
        $this->assertEquals($newerBudget->id, $info['budget_id']);
    }

    public function test_categories_page_prefers_highest_id_when_start_dates_are_equal(): void
    {
        $first = Budget::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'total_amount' => 2000,
            'start_date' => now()->subWeeks(1),
            'end_date' => now()->addMonth(),
            'is_active' => true,
        ]);

        $second = Budget::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'total_amount' => 2000,
            'start_date' => now()->subWeeks(1),
            'end_date' => now()->addMonth(),
            'is_active' => true,
        ]);

        BudgetCategory::factory()->create([
            'budget_id' => $first->id,
            'workspace_id' => $this->workspace->id,
            'expense_category_id' => $this->category->id,
            'allocated_amount' => 700,
            'spent_amount' => 70,
        ]);

        BudgetCategory::factory()->create([
            'budget_id' => $second->id,
            'workspace_id' => $this->workspace->id,
            'expense_category_id' => $this->category->id,
            'allocated_amount' => 800,
            'spent_amount' => 80,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('budget.categories'))
            ->assertOk();

        $categories = $response->viewData('categories');
        $info = $categories->firstWhere('id', $this->category->id)?->budgetInfo;

        $this->assertNotNull($info);
        $this->assertEquals($second->id, $info['budget_id']);
    }

    public function test_categories_page_spent_matches_stored_spent_amount(): void
    {
        Expense::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'category_id' => $this->category->id,
            'amount' => 300,
            'date' => now()->toDateString(),
        ]);

        $this->budgetCategory->recalculateSpentAmount();
        $expected = BudgetCategory::calculateSpentAmount(
            $this->category->id,
            $this->budget->start_date,
            $this->budget->end_date
        );

        $response = $this->actingAs($this->user)
            ->get(route('budget.categories'))
            ->assertOk();

        $categories = $response->viewData('categories');
        $info = $categories->firstWhere('id', $this->category->id)?->budgetInfo;

        $this->assertNotNull($info);
        $this->assertEquals((float) $this->budgetCategory->fresh()->spent_amount, $info['spent']);
        $this->assertEquals($expected, $info['spent']);
    }

    public function test_expense_by_another_workspace_user_updates_budget_spent_amount(): void
    {
        $otherUser = User::factory()->create();
        $adminRole = Role::where('slug', 'workspace_admin')->first();
        $otherUser->workspaceRoleUsers()->attach($adminRole->id, ['workspace_id' => $this->workspace->id]);
        $otherUser->current_workspace_id = $this->workspace->id;
        $otherUser->save();
        $otherUser->refresh();

        $this->actingAs($otherUser)
            ->post(route('expense.store'), [
                'category_id' => $this->category->id,
                'amount' => 400,
                'date' => now()->toDateString(),
                'description' => 'Expense by teammate',
            ])
            ->assertRedirect(route('expense.index'));

        $this->assertEquals(400, $this->budgetCategory->fresh()->spent_amount);
    }

    public function test_store_budget_rejects_category_from_another_workspace(): void
    {
        $otherWorkspace = Workspace::factory()->create();
        $foreignCategory = ExpenseCategory::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $otherWorkspace->id,
        ]);

        $this->actingAs($this->user)
            ->postJson(route('budget.store'), [
                'name_ar' => 'ميزانية',
                'name_fr' => 'Budget',
                'name_en' => 'Budget',
                'type' => 'monthly',
                'total_amount' => 10000,
                'start_date' => now()->toDateString(),
                'categories' => [
                    ['category_id' => $foreignCategory->id, 'allocated_amount' => 5000],
                ],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('categories.0.category_id');
    }

    public function test_update_budget_rejects_category_from_another_workspace(): void
    {
        $otherWorkspace = Workspace::factory()->create();
        $foreignCategory = ExpenseCategory::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $otherWorkspace->id,
        ]);

        $this->actingAs($this->user)
            ->putJson(route('budget.update', $this->budget), [
                'name_ar' => 'ميزانية',
                'name_fr' => 'Budget',
                'name_en' => 'Budget',
                'type' => 'monthly',
                'total_amount' => 10000,
                'start_date' => now()->toDateString(),
                'categories' => [
                    ['category_id' => $foreignCategory->id, 'allocated_amount' => 5000],
                ],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('categories.0.category_id');
    }
}
