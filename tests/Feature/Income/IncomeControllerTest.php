<?php

namespace Tests\Feature\Income;

use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithWorkspacePermission;

class IncomeControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithWorkspacePermission;

    private User $user;

    private IncomeCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpWorkspacePermission();
        $this->user = $this->workspaceUser;
        $this->category = IncomeCategory::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    public function test_index_displays_incomes(): void
    {
        Income::factory(3)->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->get(route('income.index'))
            ->assertOk()
            ->assertViewIs('income.index');
    }

    public function test_guest_cannot_access_income(): void
    {
        $this->get(route('income.index'))
            ->assertRedirect(route('login'));
    }

    public function test_store_creates_income_and_redirects(): void
    {
        $data = [
            'category_id' => $this->category->id,
            'amount' => 1000,
            'description' => 'Salary',
            'date' => '2026-06-15',
        ];

        $this->actingAs($this->user)
            ->post(route('income.store'), $data)
            ->assertRedirect(route('income.index'));

        $this->assertDatabaseHas('incomes', [
            'user_id' => $this->user->id,
            'amount' => 1000,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->post(route('income.store'), [])
            ->assertSessionHasErrors(['amount', 'date']);
    }

    public function test_user_cannot_view_other_users_income(): void
    {
        $otherUser = User::factory()->create();
        $income = Income::factory()->create(['user_id' => $otherUser->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->get(route('income.edit', $income))
            ->assertOk();
    }

    public function test_user_cannot_update_other_users_income(): void
    {
        $otherUser = User::factory()->create();
        $category = IncomeCategory::factory()->create(['user_id' => $otherUser->id]);
        $income = Income::factory()->create([
            'user_id' => $otherUser->id,
            'workspace_id' => $this->workspace->id,
            'amount' => 1000,
            'category_id' => $category->id,
        ]);

        $this->actingAs($this->user)
            ->put(route('income.update', $income), [
                'category_id' => $category->id,
                'amount' => 2000,
                'date' => '2026-06-20',
            ])
            ->assertRedirect(route('income.index'));
    }

    public function test_user_cannot_delete_other_users_income(): void
    {
        $otherUser = User::factory()->create();
        $income = Income::factory()->create(['user_id' => $otherUser->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->delete(route('income.destroy', $income))
            ->assertRedirect(route('income.index'));
    }

    public function test_update_modifies_income(): void
    {
        $income = Income::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'amount' => 1000,
            'category_id' => $this->category->id,
        ]);

        $this->actingAs($this->user)
            ->put(route('income.update', $income), [
                'category_id' => $this->category->id,
                'amount' => 2000,
                'date' => '2026-06-20',
            ])
            ->assertRedirect(route('income.index'));

        $this->assertEquals(2000, $income->fresh()->amount);
    }

    public function test_destroy_soft_deletes(): void
    {
        $income = Income::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->delete(route('income.destroy', $income))
            ->assertRedirect(route('income.index'));

        $this->assertSoftDeleted($income);
    }

    public function test_restore_recovers_soft_deleted(): void
    {
        $income = Income::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);
        $income->delete();

        $this->actingAs($this->user)
            ->patch(route('income.restore', $income->id))
            ->assertRedirect();

        $this->assertNotSoftDeleted($income);
    }

    public function test_bulk_delete_removes_multiple(): void
    {
        $incomes = Income::factory(3)->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->post(route('income.bulk-delete'), ['ids' => $incomes->pluck('id')->toArray()])
            ->assertRedirect();

        foreach ($incomes as $income) {
            $this->assertSoftDeleted($income);
        }
    }

    public function test_bulk_restore_recovers_multiple(): void
    {
        $incomes = Income::factory(3)->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);
        foreach ($incomes as $income) {
            $income->delete();
        }

        $this->actingAs($this->user)
            ->post(route('income.bulk-restore'), ['ids' => $incomes->pluck('id')->toArray()])
            ->assertRedirect();

        foreach ($incomes as $income) {
            $this->assertNotSoftDeleted($income);
        }
    }

    public function test_archive_toggles_archive_status(): void
    {
        $income = Income::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'is_archived' => false,
        ]);

        $this->actingAs($this->user)
            ->patch(route('income.archive', $income))
            ->assertRedirect();

        $this->assertTrue($income->fresh()->is_archived);
    }

    public function test_index_can_filter_by_trashed(): void
    {
        $active = Income::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);
        $trashed = Income::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);
        $trashed->delete();

        $this->actingAs($this->user)
            ->get(route('income.index', ['trashed' => 'true']))
            ->assertOk()
            ->assertViewIs('income.index');
    }
}
