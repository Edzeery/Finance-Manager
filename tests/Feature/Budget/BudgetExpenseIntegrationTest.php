<?php

namespace Tests\Feature\Budget;

use App\Models\Budget;
use App\Models\BudgetCategory;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
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
}
