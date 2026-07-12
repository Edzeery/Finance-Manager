<?php

namespace Tests\Unit\Models;

use App\Models\Debt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebtTest extends TestCase
{
    use RefreshDatabase;

    public function test_remaining_amount_is_total_minus_paid(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 1000,
            'paid_amount' => 300,
        ]);

        $this->assertEquals(700, $debt->remaining_amount);
    }

    public function test_remaining_amount_never_below_zero(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 500,
            'paid_amount' => 600,
        ]);

        $this->assertEquals(0, $debt->remaining_amount);
    }

    public function test_progress_is_percentage(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 200,
            'paid_amount' => 50,
        ]);

        $this->assertEquals(25.0, $debt->progress);
    }

    public function test_is_overdue_true_when_past_due_and_not_paid(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create([
            'user_id' => $user->id,
            'due_date' => now()->subDay(),
            'status' => 'active',
        ]);

        $this->assertTrue($debt->is_overdue);
    }

    public function test_is_overdue_false_when_paid(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create([
            'user_id' => $user->id,
            'due_date' => now()->subDay(),
            'status' => 'paid',
        ]);

        $this->assertFalse($debt->is_overdue);
    }

    public function test_scope_owed_returns_only_owed(): void
    {
        $user = User::factory()->create();
        Debt::factory()->create(['user_id' => $user->id, 'type' => 'owed']);
        Debt::factory()->create(['user_id' => $user->id, 'type' => 'owing']);

        $this->assertEquals(1, Debt::owed()->count());
    }

    public function test_scope_active_returns_correct_statuses(): void
    {
        $user = User::factory()->create();
        Debt::factory()->create(['user_id' => $user->id, 'status' => 'active']);
        Debt::factory()->create(['user_id' => $user->id, 'status' => 'paid']);

        $this->assertEquals(1, Debt::active()->count());
    }
}
