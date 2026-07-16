<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithWorkspacePermission;

class NotificationControllerTest extends TestCase
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

    public function test_guest_cannot_access_notifications(): void
    {
        $this->get(route('notifications.index'))
            ->assertRedirect(route('login'));
    }

    public function test_index_returns_json_for_ajax_request(): void
    {
        Notification::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('notifications.index'));

        $response->assertOk()
            ->assertJsonStructure([
                'notifications' => [['id', 'title', 'message', 'type', 'is_read', 'created_at', 'time']],
                'unread_count',
            ]);
    }

    public function test_index_returns_html_for_standard_request(): void
    {
        Notification::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('notifications.index'));

        $response->assertOk()
            ->assertViewIs('notifications.index');
    }

    public function test_mark_read_updates_notification(): void
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'is_read' => false,
        ]);

        $this->actingAs($this->user)
            ->postJson(route('notifications.read', $notification))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'is_read' => true,
        ]);
    }

    public function test_mark_read_denies_other_users(): void
    {
        $other = User::factory()->create();
        $notification = Notification::factory()->create([
            'user_id' => $other->id,
            'workspace_id' => $this->workspace->id,
        ]);

        $this->actingAs($this->user)
            ->postJson(route('notifications.read', $notification))
            ->assertForbidden();
    }

    public function test_mark_all_read_updates_all(): void
    {
        Notification::factory(3)->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'is_read' => false,
        ]);

        $this->actingAs($this->user)
            ->postJson(route('notifications.mark-all-read'))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertEquals(0, Notification::where('user_id', $this->user->id)->unread()->count());
    }

    public function test_show_empty_state(): void
    {
        $this->actingAs($this->user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee(__('general.no_notifications'));
    }
}
