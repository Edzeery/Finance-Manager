<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_factory_creates_valid_notification(): void
    {
        $user = User::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'user_id' => $user->id,
            'type' => $notification->type,
        ]);
        $this->assertNotEmpty($notification->title_en);
        $this->assertNotEmpty($notification->message_en);
        $this->assertFalse($notification->is_read);
    }

    public function test_unread_scope(): void
    {
        $user = User::factory()->create();
        Notification::factory()->create(['user_id' => $user->id, 'is_read' => false]);
        Notification::factory()->create(['user_id' => $user->id, 'is_read' => true]);

        $unread = Notification::where('user_id', $user->id)->unread()->count();
        $this->assertEquals(1, $unread);
    }

    public function test_by_type_scope(): void
    {
        $user = User::factory()->create();
        Notification::factory()->create(['user_id' => $user->id, 'type' => 'budget_exceeded']);
        Notification::factory()->create(['user_id' => $user->id, 'type' => 'debt_reminder']);

        $budgetNotifs = Notification::where('user_id', $user->id)->byType('budget_exceeded')->count();
        $this->assertEquals(1, $budgetNotifs);
    }
}
