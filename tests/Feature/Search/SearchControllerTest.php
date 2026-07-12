<?php

namespace Tests\Feature\Search;

use App\Models\Income;
use App\Models\Expense;
use App\Models\User;
use App\Models\Workspace;
use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;
use Database\Seeders\EnterpriseRolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithWorkspacePermission;

class SearchControllerTest extends TestCase
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

    public function test_search_returns_results(): void
    {
        Income::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->incomeCategory->id,
            'description' => 'Salary payment',
        ]);

        $this->actingAs($this->user)
            ->get(route('search', ['q' => 'Salary']))
            ->assertOk()
            ->assertViewIs('search.results')
            ->assertViewHas('results');
    }

    public function test_guest_cannot_search(): void
    {
        $this->get(route('search', ['q' => 'test']))
            ->assertRedirect(route('login'));
    }

    public function test_search_validates_minimum_length(): void
    {
        $this->actingAs($this->user)
            ->get(route('search', ['q' => 'a']))
            ->assertRedirect()
            ->assertSessionHasErrors('q');
    }

    public function test_search_returns_empty_for_no_matches(): void
    {
        $this->actingAs($this->user)
            ->get(route('search', ['q' => 'xyznonexistent123']))
            ->assertOk()
            ->assertViewHas('results', collect());
    }

    public function test_search_searches_across_multiple_entities(): void
    {
        Income::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->incomeCategory->id,
            'description' => 'Test Income',
        ]);
        Expense::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->expenseCategory->id,
            'description' => 'Test Expense',
        ]);

        $this->actingAs($this->user)
            ->get(route('search', ['q' => 'Test']))
            ->assertOk()
            ->assertViewHas('results');
    }

    public function test_search_does_not_include_other_users_data(): void
    {
        $otherWorkspace = Workspace::factory()->create();
        $otherUser = User::factory()->create(['current_workspace_id' => $otherWorkspace->id]);
        $otherUser->workspaces()->attach($otherWorkspace->id);
        $otherCategory = IncomeCategory::factory()->create(['user_id' => $otherUser->id]);
        Income::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id,
            'description' => 'Other Salary',
        ]);

        $this->actingAs($this->user)
            ->get(route('search', ['q' => 'Salary']))
            ->assertOk()
            ->assertViewHas('results', collect());
    }
}
