<?php

namespace Tests\Feature\Budget;

use App\Models\Budget;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithWorkspacePermission;

class BudgetControllerTest extends TestCase
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

    public function test_index_displays_budgets(): void
    {
        Budget::factory(3)->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->get(route('budget.index'))
            ->assertOk()
            ->assertViewIs('budget.index');
    }

    public function test_guest_cannot_access_budget(): void
    {
        $this->get(route('budget.index'))
            ->assertRedirect(route('login'));
    }

    public function test_store_creates_budget_and_redirects(): void
    {
        $data = [
            'name_ar' => 'ميزانية',
            'name_fr' => 'Budget',
            'name_en' => 'Budget',
            'type' => 'monthly',
            'total_amount' => 10000,
            'start_date' => '2026-06-01',
        ];

        $this->actingAs($this->user)
            ->post(route('budget.store'), $data)
            ->assertRedirect(route('budget.index'));

        $this->assertDatabaseHas('budgets', [
            'user_id' => $this->user->id,
            'name_en' => 'Budget',
            'total_amount' => 10000,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->post(route('budget.store'), [])
            ->assertSessionHasErrors(['name_ar', 'type', 'total_amount', 'start_date']);
    }

    public function test_show_displays_budget(): void
    {
        $budget = Budget::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->get(route('budget.show', $budget))
            ->assertOk()
            ->assertViewIs('budget.show');
    }

    public function test_user_cannot_view_other_users_budget(): void
    {
        $otherUser = User::factory()->create();
        $budget = Budget::factory()->create(['user_id' => $otherUser->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->get(route('budget.show', $budget))
            ->assertOk();
    }

    public function test_user_cannot_edit_other_users_budget(): void
    {
        $otherUser = User::factory()->create();
        $budget = Budget::factory()->create(['user_id' => $otherUser->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->get(route('budget.edit', $budget))
            ->assertOk();
    }

    public function test_user_cannot_update_other_users_budget(): void
    {
        $otherUser = User::factory()->create();
        $budget = Budget::factory()->create([
            'user_id' => $otherUser->id,
            'workspace_id' => $this->workspace->id,
            'total_amount' => 1000,
        ]);

        $this->actingAs($this->user)
            ->put(route('budget.update', $budget), [
                'name_ar' => 'ميزانية محدثة',
                'name_fr' => 'Budget mis à jour',
                'name_en' => 'Updated Budget',
                'type' => 'monthly',
                'total_amount' => 2000,
                'start_date' => now()->toDateString(),
            ])
            ->assertRedirect(route('budget.index'));
    }

    public function test_user_cannot_delete_other_users_budget(): void
    {
        $otherUser = User::factory()->create();
        $budget = Budget::factory()->create(['user_id' => $otherUser->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->delete(route('budget.destroy', $budget))
            ->assertRedirect(route('budget.index'));
    }

    public function test_update_modifies_budget(): void
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'total_amount' => 1000,
            'name_ar' => 'ميزانية',
            'name_fr' => 'Budget',
            'name_en' => 'Budget',
            'type' => 'monthly',
            'start_date' => '2026-06-01',
        ]);

        $this->actingAs($this->user)
            ->put(route('budget.update', $budget), [
                'name_ar' => 'ميزانية جديدة',
                'name_fr' => 'Nouveau Budget',
                'name_en' => 'New Budget',
                'type' => 'yearly',
                'total_amount' => 50000,
                'start_date' => '2026-01-01',
            ])
            ->assertRedirect(route('budget.index'));

        $this->assertEquals(50000, $budget->fresh()->total_amount);
        $this->assertEquals('New Budget', $budget->fresh()->name_en);
    }

    public function test_destroy_soft_deletes(): void
    {
        $budget = Budget::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->delete(route('budget.destroy', $budget))
            ->assertRedirect(route('budget.index'));

        $this->assertSoftDeleted($budget);
    }

    public function test_restore_recovers_soft_deleted(): void
    {
        $budget = Budget::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);
        $budget->delete();

        $this->actingAs($this->user)
            ->patch(route('budget.restore', $budget->id))
            ->assertRedirect();

        $this->assertNotSoftDeleted($budget);
    }

    public function test_bulk_delete_removes_multiple(): void
    {
        $budgets = Budget::factory(3)->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->post(route('budget.bulk-delete'), ['ids' => $budgets->pluck('id')->toArray()])
            ->assertRedirect();

        foreach ($budgets as $budget) {
            $this->assertSoftDeleted($budget);
        }
    }

    public function test_bulk_restore_recovers_multiple(): void
    {
        $budgets = Budget::factory(3)->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);
        foreach ($budgets as $budget) {
            $budget->delete();
        }

        $this->actingAs($this->user)
            ->post(route('budget.bulk-restore'), ['ids' => $budgets->pluck('id')->toArray()])
            ->assertRedirect();

        foreach ($budgets as $budget) {
            $this->assertNotSoftDeleted($budget);
        }
    }

    public function test_store_with_categories_creates_budget_categories(): void
    {
        $data = [
            'name_ar' => 'ميزانية',
            'name_fr' => 'Budget',
            'name_en' => 'Budget',
            'type' => 'monthly',
            'total_amount' => 10000,
            'start_date' => '2026-06-01',
            'categories' => [
                ['category_id' => $this->category->id, 'allocated_amount' => 5000],
            ],
        ];

        $this->actingAs($this->user)
            ->post(route('budget.store'), $data)
            ->assertRedirect(route('budget.index'));

        $this->assertDatabaseHas('budget_categories', [
            'expense_category_id' => $this->category->id,
            'allocated_amount' => 5000,
        ]);
    }

    public function test_index_can_filter_by_trashed(): void
    {
        $active = Budget::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);
        $trashed = Budget::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);
        $trashed->delete();

        $this->actingAs($this->user)
            ->get(route('budget.index', ['trashed' => 'true']))
            ->assertOk()
            ->assertViewIs('budget.index');
    }

    public function test_store_with_percentage_computes_allocated_amount(): void
    {
        $data = [
            'name_ar' => 'ميزانية',
            'name_fr' => 'Budget',
            'name_en' => 'Budget',
            'type' => 'monthly',
            'total_amount' => 10000,
            'start_date' => '2026-06-01',
            'categories' => [
                ['category_id' => $this->category->id, 'use_percentage' => 1, 'percentage' => 25, 'allocated_amount' => 0],
            ],
        ];

        $this->actingAs($this->user)
            ->post(route('budget.store'), $data)
            ->assertRedirect(route('budget.index'));

        $this->assertDatabaseHas('budget_categories', [
            'expense_category_id' => $this->category->id,
            'allocated_amount' => 2500,
            'percentage' => 25,
        ]);
    }

    public function test_store_with_amount_mode_keeps_allocated_amount_and_null_percentage(): void
    {
        $data = [
            'name_ar' => 'ميزانية',
            'name_fr' => 'Budget',
            'name_en' => 'Budget',
            'type' => 'monthly',
            'total_amount' => 10000,
            'start_date' => '2026-06-01',
            'categories' => [
                ['category_id' => $this->category->id, 'allocated_amount' => 5000],
            ],
        ];

        $this->actingAs($this->user)
            ->post(route('budget.store'), $data)
            ->assertRedirect(route('budget.index'));

        $this->assertDatabaseHas('budget_categories', [
            'expense_category_id' => $this->category->id,
            'allocated_amount' => 5000,
            'percentage' => null,
        ]);
    }

    public function test_store_rejects_percentage_above_100(): void
    {
        $data = [
            'name_ar' => 'ميزانية',
            'name_fr' => 'Budget',
            'name_en' => 'Budget',
            'type' => 'monthly',
            'total_amount' => 10000,
            'start_date' => '2026-06-01',
            'categories' => [
                ['category_id' => $this->category->id, 'use_percentage' => 1, 'percentage' => 150],
            ],
        ];

        $this->actingAs($this->user)
            ->post(route('budget.store'), $data)
            ->assertSessionHasErrors('categories.0.percentage');
    }

    public function test_store_rejects_percentages_summing_over_100(): void
    {
        $secondCategory = ExpenseCategory::factory()->create(['user_id' => $this->user->id]);
        $data = [
            'name_ar' => 'ميزانية',
            'name_fr' => 'Budget',
            'name_en' => 'Budget',
            'type' => 'monthly',
            'total_amount' => 10000,
            'start_date' => '2026-06-01',
            'categories' => [
                ['category_id' => $this->category->id, 'use_percentage' => 1, 'percentage' => 60],
                ['category_id' => $secondCategory->id, 'use_percentage' => 1, 'percentage' => 50],
            ],
        ];

        $this->actingAs($this->user)
            ->post(route('budget.store'), $data)
            ->assertSessionHasErrors('categories');
    }

    public function test_update_with_percentage_computes_allocation(): void
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'total_amount' => 2000,
            'name_ar' => 'ميزانية',
            'name_fr' => 'Budget',
            'name_en' => 'Budget',
            'type' => 'monthly',
            'start_date' => '2026-06-01',
        ]);

        $this->actingAs($this->user)
            ->put(route('budget.update', $budget), [
                'name_ar' => 'ميزانية',
                'name_fr' => 'Budget',
                'name_en' => 'Budget',
                'type' => 'monthly',
                'total_amount' => 2000,
                'start_date' => '2026-06-01',
                'categories' => [
                    ['category_id' => $this->category->id, 'use_percentage' => 1, 'percentage' => 50],
                ],
            ])
            ->assertRedirect(route('budget.index'));

        $this->assertDatabaseHas('budget_categories', [
            'budget_id' => $budget->id,
            'expense_category_id' => $this->category->id,
            'allocated_amount' => 1000,
            'percentage' => 50,
        ]);
    }

    public function test_update_switching_to_amount_mode_clears_percentage(): void
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'total_amount' => 2000,
            'name_ar' => 'ميزانية',
            'name_fr' => 'Budget',
            'name_en' => 'Budget',
            'type' => 'monthly',
            'start_date' => '2026-06-01',
        ]);
        $budget->categories()->create([
            'expense_category_id' => $this->category->id,
            'allocated_amount' => 1000,
            'percentage' => 50,
            'spent_amount' => 0,
        ]);

        $this->actingAs($this->user)
            ->put(route('budget.update', $budget), [
                'name_ar' => 'ميزانية',
                'name_fr' => 'Budget',
                'name_en' => 'Budget',
                'type' => 'monthly',
                'total_amount' => 2000,
                'start_date' => '2026-06-01',
                'categories' => [
                    ['category_id' => $this->category->id, 'allocated_amount' => 1500],
                ],
            ])
            ->assertRedirect(route('budget.index'));

        $this->assertDatabaseHas('budget_categories', [
            'budget_id' => $budget->id,
            'expense_category_id' => $this->category->id,
            'allocated_amount' => 1500,
            'percentage' => null,
        ]);
    }
}
