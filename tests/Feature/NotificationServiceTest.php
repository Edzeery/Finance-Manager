<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private NotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->service = app(NotificationService::class);
    }

    public function test_budget_exceeded_creates_notification(): void
    {
        $notif = $this->service->budgetExceeded($this->user->id, 'TestBudget', 500);

        $this->assertDatabaseHas('notifications', [
            'id' => $notif->id,
            'user_id' => $this->user->id,
            'type' => 'budget_exceeded',
        ]);
    }

    public function test_debt_reminder_creates_notification(): void
    {
        $notif = $this->service->debtReminder($this->user->id, 'John', 1000, '2026-07-01');

        $this->assertDatabaseHas('notifications', [
            'id' => $notif->id,
            'type' => 'debt_reminder',
        ]);
    }

    public function test_goal_achieved_creates_notification(): void
    {
        $notif = $this->service->goalAchieved($this->user->id, 'Save for car');

        $this->assertDatabaseHas('notifications', [
            'id' => $notif->id,
            'type' => 'goal_achieved',
        ]);
    }

    public function test_budget_nearing_limit_creates_notification(): void
    {
        $notif = $this->service->budgetNearingLimit($this->user->id, 'Food', 85);

        $this->assertDatabaseHas('notifications', [
            'id' => $notif->id,
            'type' => 'budget_nearing_limit',
        ]);
    }

    public function test_goal_milestone_creates_notification(): void
    {
        $notif = $this->service->goalMilestoneReached($this->user->id, 'Travel', 50);

        $this->assertDatabaseHas('notifications', [
            'id' => $notif->id,
            'type' => 'goal_milestone',
        ]);
    }

    public function test_goal_deadline_approaching_creates_notification(): void
    {
        $notif = $this->service->goalDeadlineApproaching($this->user->id, 'Retirement', 5);

        $this->assertDatabaseHas('notifications', [
            'id' => $notif->id,
            'type' => 'goal_deadline',
        ]);
    }
}
