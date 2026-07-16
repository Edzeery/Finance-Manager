<?php

namespace Tests\Unit\Models;

use App\Models\Budget;
use App\Models\BudgetCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_exceeded_returns_true_when_spent_exceeds_total(): void
    {
        $user = User::factory()->create();
        $budget = Budget::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 1000,
        ]);

        BudgetCategory::factory()->create([
            'budget_id' => $budget->id,
            'allocated_amount' => 1000,
            'spent_amount' => 1500,
        ]);

        $this->assertTrue($budget->is_exceeded);
    }

    public function test_is_exceeded_returns_false_when_spent_within_limit(): void
    {
        $user = User::factory()->create();
        $budget = Budget::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 2000,
        ]);

        BudgetCategory::factory()->create([
            'budget_id' => $budget->id,
            'allocated_amount' => 1000,
            'spent_amount' => 800,
        ]);

        $this->assertFalse($budget->is_exceeded);
    }

    public function test_scope_active_returns_only_active(): void
    {
        $user = User::factory()->create();
        Budget::factory()->create(['user_id' => $user->id, 'is_active' => true]);
        Budget::factory()->create(['user_id' => $user->id, 'is_active' => false]);

        $this->assertEquals(1, Budget::active()->count());
    }
}
