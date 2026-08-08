<?php

namespace Tests\Feature\Expense;

use App\Models\Debt;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithWorkspacePermission;

class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithWorkspacePermission;

    private User $user;

    private ExpenseCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpWorkspacePermission();
        $this->user = $this->workspaceUser;
        $this->category = ExpenseCategory::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    public function test_index_displays_expenses(): void
    {
        Expense::factory(3)->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->get(route('expense.index'))
            ->assertOk()
            ->assertViewIs('expense.index');
    }

    public function test_guest_cannot_access_expense(): void
    {
        $this->get(route('expense.index'))
            ->assertRedirect(route('login'));
    }

    public function test_store_creates_expense_and_redirects(): void
    {
        $data = [
            'category_id' => $this->category->id,
            'amount' => 500,
            'description' => 'Groceries',
            'date' => '2026-06-15',
        ];

        $this->actingAs($this->user)
            ->post(route('expense.store'), $data)
            ->assertRedirect(route('expense.index'));

        $this->assertDatabaseHas('expenses', [
            'user_id' => $this->user->id,
            'amount' => 500,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->post(route('expense.store'), [])
            ->assertSessionHasErrors(['amount', 'date']);
    }

    public function test_user_cannot_view_other_users_expense(): void
    {
        $otherUser = User::factory()->create();
        $expense = Expense::factory()->create(['user_id' => $otherUser->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->get(route('expense.edit', $expense))
            ->assertOk();
    }

    public function test_user_cannot_update_other_users_expense(): void
    {
        $otherUser = User::factory()->create();
        $category = ExpenseCategory::factory()->create(['user_id' => $otherUser->id]);
        $expense = Expense::factory()->create([
            'user_id' => $otherUser->id,
            'workspace_id' => $this->workspace->id,
            'amount' => 1000,
            'category_id' => $category->id,
        ]);

        $this->actingAs($this->user)
            ->put(route('expense.update', $expense), [
                'category_id' => $category->id,
                'amount' => 2000,
                'date' => '2026-06-20',
            ])
            ->assertRedirect(route('expense.index'));
    }

    public function test_user_cannot_delete_other_users_expense(): void
    {
        $otherUser = User::factory()->create();
        $expense = Expense::factory()->create(['user_id' => $otherUser->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->delete(route('expense.destroy', $expense))
            ->assertRedirect(route('expense.index'));
    }

    public function test_update_modifies_expense(): void
    {
        $expense = Expense::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'amount' => 1000,
            'category_id' => $this->category->id,
        ]);

        $this->actingAs($this->user)
            ->put(route('expense.update', $expense), [
                'category_id' => $this->category->id,
                'amount' => 2000,
                'date' => '2026-06-20',
            ])
            ->assertRedirect(route('expense.index'));

        $this->assertEquals(2000, $expense->fresh()->amount);
    }

    public function test_destroy_soft_deletes(): void
    {
        $expense = Expense::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->delete(route('expense.destroy', $expense))
            ->assertRedirect(route('expense.index'));

        $this->assertSoftDeleted($expense);
    }

    public function test_restore_recovers_soft_deleted(): void
    {
        $expense = Expense::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);
        $expense->delete();

        $this->actingAs($this->user)
            ->patch(route('expense.restore', $expense->id))
            ->assertRedirect();

        $this->assertNotSoftDeleted($expense);
    }

    public function test_bulk_delete_removes_multiple(): void
    {
        $expenses = Expense::factory(3)->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->post(route('expense.bulk-delete'), ['ids' => $expenses->pluck('id')->toArray()])
            ->assertRedirect();

        foreach ($expenses as $expense) {
            $this->assertSoftDeleted($expense);
        }
    }

    public function test_bulk_restore_recovers_multiple(): void
    {
        $expenses = Expense::factory(3)->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);
        foreach ($expenses as $expense) {
            $expense->delete();
        }

        $this->actingAs($this->user)
            ->post(route('expense.bulk-restore'), ['ids' => $expenses->pluck('id')->toArray()])
            ->assertRedirect();

        foreach ($expenses as $expense) {
            $this->assertNotSoftDeleted($expense);
        }
    }

    public function test_archive_toggles_archive_status(): void
    {
        $expense = Expense::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'is_archived' => false,
        ]);

        $this->actingAs($this->user)
            ->patch(route('expense.archive', $expense))
            ->assertRedirect();

        $this->assertTrue($expense->fresh()->is_archived);
    }

    public function test_index_can_filter_by_trashed(): void
    {
        $active = Expense::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);
        $trashed = Expense::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);
        $trashed->delete();

        $this->actingAs($this->user)
            ->get(route('expense.index', ['trashed' => 'true']))
            ->assertOk()
            ->assertViewIs('expense.index');
    }

    public function test_store_on_credit_creates_debt_without_expense(): void
    {
        $data = [
            'category_id' => $this->category->id,
            'amount' => 500,
            'description' => 'On credit',
            'date' => '2026-06-15',
            'is_new_debt' => true,
            'debt_counterparty' => 'Supplier Co',
            'debt_due_date' => '2026-08-15',
        ];

        $this->actingAs($this->user)
            ->post(route('expense.store'), $data)
            ->assertRedirect(route('expense.index'));

        $this->assertDatabaseHas('debts', [
            'user_id' => $this->user->id,
            'type' => 'owing',
            'counterparty_name' => 'Supplier Co',
            'total_amount' => 500,
            'paid_amount' => 0,
        ]);
        $this->assertDatabaseMissing('expenses', [
            'user_id' => $this->user->id,
            'amount' => 500,
        ]);
    }

    public function test_store_on_credit_with_count_at_incurrence_creates_linked_expense(): void
    {
        $data = [
            'category_id' => $this->category->id,
            'amount' => 500,
            'description' => 'On credit now',
            'date' => '2026-06-15',
            'is_new_debt' => true,
            'count_at_incurrence' => true,
            'debt_counterparty' => 'Supplier Co',
            'debt_due_date' => '2026-08-15',
        ];

        $this->actingAs($this->user)
            ->post(route('expense.store'), $data)
            ->assertRedirect(route('expense.index'));

        $debt = Debt::withoutWorkspace()->where('counterparty_name', 'Supplier Co')->first();
        $this->assertNotNull($debt);
        $this->assertDatabaseHas('expenses', [
            'user_id' => $this->user->id,
            'amount' => 500,
            'debt_id' => $debt->id,
        ]);
    }

    public function test_store_on_credit_requires_debt_fields(): void
    {
        $this->actingAs($this->user)
            ->post(route('expense.store'), [
                'category_id' => $this->category->id,
                'amount' => 500,
                'date' => '2026-06-15',
                'is_new_debt' => true,
            ])
            ->assertSessionHasErrors(['debt_counterparty', 'debt_due_date']);
    }
}
