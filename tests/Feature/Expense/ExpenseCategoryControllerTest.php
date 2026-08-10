<?php

namespace Tests\Feature\Expense;

use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithWorkspacePermission;

class ExpenseCategoryControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithWorkspacePermission;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpWorkspacePermission();
        $this->user = $this->workspaceUser;
    }

    public function test_store_persists_default_budget_percentage(): void
    {
        $this->actingAs($this->user)
            ->post(route('expense.categories.store'), [
                'name_ar' => 'غذاء',
                'name_fr' => 'Nourriture',
                'name_en' => 'Food',
                'icon' => 'bi-cart',
                'color' => '#EF4444',
                'type' => 'variable',
                'default_budget_percentage' => 15,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('expense_categories', [
            'name_en' => 'Food',
            'default_budget_percentage' => 15,
        ]);
    }

    public function test_store_rejects_default_budget_percentage_above_100(): void
    {
        $this->actingAs($this->user)
            ->post(route('expense.categories.store'), [
                'name_ar' => 'غذاء',
                'name_fr' => 'Nourriture',
                'name_en' => 'Food',
                'icon' => 'bi-cart',
                'color' => '#EF4444',
                'type' => 'variable',
                'default_budget_percentage' => 120,
            ])
            ->assertSessionHasErrors('default_budget_percentage');
    }

    public function test_update_persists_default_budget_percentage(): void
    {
        $category = ExpenseCategory::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $this->actingAs($this->user)
            ->put(route('expense.categories.update', $category), [
                'name_ar' => 'غذاء',
                'name_fr' => 'Nourriture',
                'name_en' => 'Food',
                'icon' => 'bi-cart',
                'color' => '#EF4444',
                'type' => 'variable',
                'default_budget_percentage' => 25,
            ])
            ->dump()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('expense_categories', [
            'id' => $category->id,
            'default_budget_percentage' => 25,
        ]);
    }
}
