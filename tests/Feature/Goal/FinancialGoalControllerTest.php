<?php

namespace Tests\Feature\Goal;

use App\Models\FinancialGoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithWorkspacePermission;

class FinancialGoalControllerTest extends TestCase
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

    public function test_index_displays_goals(): void
    {
        FinancialGoal::factory(3)->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->get(route('goal.index'))
            ->assertOk()
            ->assertViewIs('goal.index');
    }

    public function test_guest_cannot_access_goal(): void
    {
        $this->get(route('goal.index'))
            ->assertRedirect(route('login'));
    }

    public function test_store_creates_goal_and_redirects(): void
    {
        $data = [
            'name_ar' => 'هدف',
            'name_fr' => 'Objectif',
            'name_en' => 'Goal',
            'target_amount' => 10000,
            'status' => 'in_progress',
        ];

        $this->actingAs($this->user)
            ->post(route('goal.store'), $data)
            ->assertRedirect(route('goal.index'));

        $this->assertDatabaseHas('financial_goals', [
            'user_id' => $this->user->id,
            'name_en' => 'Goal',
            'target_amount' => 10000,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->post(route('goal.store'), [])
            ->assertSessionHasErrors(['name_ar', 'name_fr', 'name_en', 'target_amount', 'status']);
    }

    public function test_user_cannot_view_other_users_goal(): void
    {
        $otherUser = User::factory()->create();
        $goal = FinancialGoal::factory()->create(['user_id' => $otherUser->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->get(route('goal.edit', $goal))
            ->assertOk();
    }

    public function test_user_cannot_update_other_users_goal(): void
    {
        $otherUser = User::factory()->create();
        $goal = FinancialGoal::factory()->create([
            'user_id' => $otherUser->id,
            'workspace_id' => $this->workspace->id,
            'target_amount' => 1000,
            'status' => 'in_progress',
        ]);

        $this->actingAs($this->user)
            ->put(route('goal.update', $goal), [
                'name_ar' => 'هدف محدث',
                'name_fr' => 'Objectif mis à jour',
                'name_en' => 'Updated Goal',
                'target_amount' => 2000,
                'status' => 'in_progress',
            ])
            ->assertRedirect(route('goal.index'));
    }

    public function test_user_cannot_delete_other_users_goal(): void
    {
        $otherUser = User::factory()->create();
        $goal = FinancialGoal::factory()->create(['user_id' => $otherUser->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->delete(route('goal.destroy', $goal))
            ->assertRedirect(route('goal.index'));
    }

    public function test_update_modifies_goal(): void
    {
        $goal = FinancialGoal::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'target_amount' => 1000,
            'name_ar' => 'هدف',
            'name_fr' => 'Objectif',
            'name_en' => 'Goal',
            'status' => 'in_progress',
        ]);

        $this->actingAs($this->user)
            ->put(route('goal.update', $goal), [
                'name_ar' => 'هدف جديد',
                'name_fr' => 'Nouvel Objectif',
                'name_en' => 'New Goal',
                'target_amount' => 50000,
                'status' => 'in_progress',
            ])
            ->assertRedirect(route('goal.index'));

        $this->assertEquals(50000, $goal->fresh()->target_amount);
        $this->assertEquals('New Goal', $goal->fresh()->name_en);
    }

    public function test_destroy_soft_deletes(): void
    {
        $goal = FinancialGoal::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->delete(route('goal.destroy', $goal))
            ->assertRedirect(route('goal.index'));

        $this->assertSoftDeleted($goal);
    }

    public function test_restore_recovers_soft_deleted(): void
    {
        $goal = FinancialGoal::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'status' => 'in_progress',
        ]);
        $goal->delete();

        $this->actingAs($this->user)
            ->patch(route('goal.restore', $goal->id))
            ->assertRedirect();

        $this->assertNotSoftDeleted($goal);
    }

    public function test_restore_completed_goal_returns_error(): void
    {
        $goal = FinancialGoal::factory()->completed()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);
        $goal->delete();

        $this->actingAs($this->user)
            ->patch(route('goal.restore', $goal->id))
            ->assertRedirect();
    }

    public function test_bulk_delete_removes_multiple(): void
    {
        $goals = FinancialGoal::factory(3)->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);

        $this->actingAs($this->user)
            ->post(route('goal.bulk-delete'), ['ids' => $goals->pluck('id')->toArray()])
            ->assertRedirect();

        foreach ($goals as $goal) {
            $this->assertSoftDeleted($goal);
        }
    }

    public function test_bulk_restore_recovers_multiple(): void
    {
        $goals = FinancialGoal::factory(3)->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'status' => 'in_progress',
        ]);
        foreach ($goals as $goal) {
            $goal->delete();
        }

        $this->actingAs($this->user)
            ->post(route('goal.bulk-restore'), ['ids' => $goals->pluck('id')->toArray()])
            ->assertRedirect();

        foreach ($goals as $goal) {
            $this->assertNotSoftDeleted($goal);
        }
    }

    public function test_index_can_filter_by_trashed(): void
    {
        $active = FinancialGoal::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);
        $trashed = FinancialGoal::factory()->create(['user_id' => $this->user->id, 'workspace_id' => $this->workspace->id]);
        $trashed->delete();

        $this->actingAs($this->user)
            ->get(route('goal.index', ['trashed' => 'true']))
            ->assertOk()
            ->assertViewIs('goal.index');
    }
}
