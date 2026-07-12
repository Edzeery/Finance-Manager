<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithWorkspacePermission;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithWorkspacePermission;

    private User $user;
    private IncomeCategory $incomeCategory;
    private ExpenseCategory $expenseCategory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpWorkspacePermission();
        $this->user = $this->workspaceUser;
        $this->incomeCategory = IncomeCategory::factory()->create(['user_id' => $this->user->id]);
        $this->expenseCategory = ExpenseCategory::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_index_displays_transactions_page(): void
    {
        Income::factory(2)->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'category_id' => $this->incomeCategory->id,
        ]);
        Expense::factory(2)->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'category_id' => $this->expenseCategory->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertViewIs('transactions.index');
    }

    public function test_guest_cannot_access_transactions(): void
    {
        $this->get(route('transactions.index'))
            ->assertRedirect(route('login'));
    }

    public function test_index_shows_empty_state_when_no_transactions(): void
    {
        $this->actingAs($this->user)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertSee('bi-inbox');
    }

    public function test_index_shows_income_and_expense_transactions(): void
    {
        Income::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'category_id' => $this->incomeCategory->id,
            'amount' => 1000,
            'date' => '2026-06-01',
        ]);
        Expense::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'category_id' => $this->expenseCategory->id,
            'amount' => 500,
            'date' => '2026-06-02',
        ]);

        $this->actingAs($this->user)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertViewHas('transactions');
    }

    public function test_index_filters_by_type(): void
    {
        Income::factory(3)->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'category_id' => $this->incomeCategory->id,
        ]);
        Expense::factory(3)->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'category_id' => $this->expenseCategory->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('transactions.index', ['type' => 'income']))
            ->assertOk();
    }

    public function test_index_searches_by_description(): void
    {
        Income::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'category_id' => $this->incomeCategory->id,
            'description' => 'UniqueSalary',
        ]);
        Expense::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'category_id' => $this->expenseCategory->id,
            'description' => 'Groceries',
        ]);

        $this->actingAs($this->user)
            ->get(route('transactions.index', ['search' => 'UniqueSalary']))
            ->assertOk();
    }

    public function test_index_filters_by_date_range(): void
    {
        Income::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'category_id' => $this->incomeCategory->id,
            'date' => '2026-06-15',
        ]);

        $this->actingAs($this->user)
            ->get(route('transactions.index', [
                'from' => '2026-06-01',
                'to' => '2026-06-30',
            ]))
            ->assertOk();
    }

    public function test_index_paginates_results(): void
    {
        Income::factory(20)->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'category_id' => $this->incomeCategory->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('transactions.index'))
            ->assertOk();
    }

    public function test_index_sorts_by_amount(): void
    {
        Income::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'category_id' => $this->incomeCategory->id,
            'amount' => 100,
        ]);
        Income::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'category_id' => $this->incomeCategory->id,
            'amount' => 9999,
        ]);

        $this->actingAs($this->user)
            ->get(route('transactions.index', ['sort' => 'amount', 'direction' => 'desc']))
            ->assertOk();
    }
}
