<?php

namespace Tests\Feature\Console;

use App\Models\FinancialGoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckGoalProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_sends_goal_achieved_notification(): void
    {
        $user = User::factory()->create();
        FinancialGoal::factory()->create([
            'user_id' => $user->id,
            'status' => 'in_progress',
            'current_amount' => 1000,
            'target_amount' => 1000,
        ]);

        $this->artisan('finance:check-goal-progress')
            ->assertSuccessful();

        $this->assertDatabaseHas('financial_goals', [
            'user_id' => $user->id,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type' => 'goal_achieved',
        ]);
    }

    public function test_command_reports_no_goals(): void
    {
        $this->artisan('finance:check-goal-progress')
            ->assertSuccessful()
            ->expectsOutput('No in-progress goals to check.');
    }

    public function test_command_handles_deadline_approaching(): void
    {
        $user = User::factory()->create();
        FinancialGoal::factory()->create([
            'user_id' => $user->id,
            'status' => 'in_progress',
            'current_amount' => 100,
            'target_amount' => 1000,
            'target_date' => now()->addDays(3),
        ]);

        $this->artisan('finance:check-goal-progress')
            ->assertSuccessful();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type' => 'goal_deadline',
        ]);
    }
}
