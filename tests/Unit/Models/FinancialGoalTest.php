<?php

namespace Tests\Unit\Models;

use App\Models\FinancialGoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialGoalTest extends TestCase
{
    use RefreshDatabase;

    public function test_progress_is_correct_percentage(): void
    {
        $user = User::factory()->create();
        $goal = FinancialGoal::factory()->create([
            'user_id' => $user->id,
            'target_amount' => 10000,
            'current_amount' => 2500,
        ]);

        $this->assertEquals(25, (int) $goal->progress);
    }

    public function test_scope_in_progress_filters_correctly(): void
    {
        $user = User::factory()->create();
        FinancialGoal::factory()->create([
            'user_id' => $user->id,
            'status' => 'in_progress',
        ]);
        FinancialGoal::factory()->completed()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(1, FinancialGoal::inProgress()->count());
    }

    public function test_scope_completed_filters_correctly(): void
    {
        $user = User::factory()->create();
        FinancialGoal::factory()->create([
            'user_id' => $user->id,
            'status' => 'in_progress',
        ]);
        FinancialGoal::factory()->completed()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(1, FinancialGoal::completed()->count());
    }
}
