<?php

namespace Tests\Feature\Invitation;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Workspace;
use App\Notifications\WorkspaceInvitation as WorkspaceInvitationNotification;
use Carbon\Carbon;
use Database\Seeders\EnterpriseRolePermissionSeeder;
use Database\Seeders\SubscriptionPlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class InvitationFlowTest extends TestCase
{
    use RefreshDatabase;

    private Workspace $workspace;

    private User $owner;

    private User $invitee;

    private string $inviteeEmail = 'invited@example.com';

    private string $newEmail = 'newuser@example.com';

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->seed([
            EnterpriseRolePermissionSeeder::class,
            SubscriptionPlanSeeder::class,
        ]);

        $this->workspace = Workspace::factory()->create(['name' => 'Test Workspace']);

        $ownerRole = Role::where('slug', 'workspace_admin')->first();
        $this->owner = User::factory()->create(['email' => 'owner@example.com']);
        $this->owner->workspaceRoleUsers()->attach($ownerRole->id, ['workspace_id' => $this->workspace->id]);
        $this->owner->current_workspace_id = $this->workspace->id;
        $this->owner->save();

        $this->invitee = User::factory()->create(['email' => $this->inviteeEmail]);

        $plan = SubscriptionPlan::where('slug', 'professional')->first() ?? SubscriptionPlan::factory()->create();
        Subscription::withoutWorkspace()->create([
            'workspace_id' => $this->workspace->id,
            'subscription_plan_id' => $plan->id,
            'user_id' => $this->owner->id,
            'status' => 'active',
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addYear(),
        ]);
    }

    public function test_owner_can_invite_user(): void
    {
        $this->actingAs($this->owner);

        $response = $this->post(route('settings.workspace.members.invite'), [
            'email' => $this->newEmail,
            'role' => 'workspace_viewer',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('workspace_invitations', [
            'workspace_id' => $this->workspace->id,
            'inviter_id' => $this->owner->id,
            'email' => $this->newEmail,
            'role' => 'workspace_viewer',
            'status' => InvitationStatus::Pending,
        ]);
    }

    public function test_invite_creates_notification_to_existing_user(): void
    {
        $this->actingAs($this->owner);

        $this->post(route('settings.workspace.members.invite'), [
            'email' => $this->inviteeEmail,
            'role' => 'workspace_viewer',
        ]);

        $invitation = Invitation::first();
        Notification::assertSentTo(
            $this->invitee,
            WorkspaceInvitationNotification::class,
            function ($notification, $channels) use ($invitation) {
                return $notification->invitation->id === $invitation->id;
            }
        );
    }

    public function test_invite_to_existing_member_throws_error(): void
    {
        $this->actingAs($this->owner);

        $this->workspace->users()->attach($this->invitee->id, ['joined_at' => now()]);

        $response = $this->post(route('settings.workspace.members.invite'), [
            'email' => $this->inviteeEmail,
            'role' => 'workspace_viewer',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_duplicate_pending_invite_throws_error(): void
    {
        $this->actingAs($this->owner);

        $this->post(route('settings.workspace.members.invite'), [
            'email' => $this->newEmail,
            'role' => 'workspace_viewer',
        ]);

        $response = $this->post(route('settings.workspace.members.invite'), [
            'email' => $this->newEmail,
            'role' => 'workspace_viewer',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_acceptance_page_requires_auth(): void
    {
        $invitation = Invitation::factory()->create([
            'workspace_id' => $this->workspace->id,
            'inviter_id' => $this->owner->id,
            'email' => $this->inviteeEmail,
        ]);

        $response = $this->get(route('invitations.accept', $invitation->token));

        $response->assertRedirect(route('login'));
    }

    public function test_acceptance_page_works_when_authenticated(): void
    {
        $invitation = Invitation::factory()->create([
            'workspace_id' => $this->workspace->id,
            'inviter_id' => $this->owner->id,
            'email' => $this->inviteeEmail,
        ]);

        $this->actingAs($this->invitee);

        $response = $this->get(route('invitations.accept', $invitation->token));

        $response->assertOk();
        $response->assertViewIs('invitations.accept');
    }

    public function test_user_can_accept_invitation(): void
    {
        $invitation = Invitation::factory()->create([
            'workspace_id' => $this->workspace->id,
            'inviter_id' => $this->owner->id,
            'email' => $this->inviteeEmail,
            'role' => 'workspace_viewer',
        ]);

        $this->actingAs($this->invitee);

        $response = $this->post(route('invitations.do-accept', $invitation));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('workspace_invitations', [
            'id' => $invitation->id,
            'status' => InvitationStatus::Accepted,
        ]);

        $this->assertDatabaseHas('user_workspace', [
            'workspace_id' => $this->workspace->id,
            'user_id' => $this->invitee->id,
        ]);
    }

    public function test_user_can_decline_invitation(): void
    {
        $invitation = Invitation::factory()->create([
            'workspace_id' => $this->workspace->id,
            'inviter_id' => $this->owner->id,
            'email' => $this->inviteeEmail,
        ]);

        $this->actingAs($this->invitee);

        $response = $this->post(route('invitations.do-decline', $invitation));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('workspace_invitations', [
            'id' => $invitation->id,
            'status' => InvitationStatus::Declined,
        ]);
    }

    public function test_email_mismatch_blocks_acceptance(): void
    {
        $invitation = Invitation::factory()->create([
            'workspace_id' => $this->workspace->id,
            'inviter_id' => $this->owner->id,
            'email' => 'other@example.com',
        ]);

        $this->actingAs($this->invitee);

        $response = $this->post(route('invitations.do-accept', $invitation));

        $response->assertStatus(403);
    }

    public function test_owner_can_cancel_pending_invitation(): void
    {
        $invitation = Invitation::factory()->create([
            'workspace_id' => $this->workspace->id,
            'inviter_id' => $this->owner->id,
            'email' => $this->inviteeEmail,
        ]);

        $this->actingAs($this->owner);

        $response = $this->delete(route('invitations.cancel', $invitation));

        $response->assertRedirect(route('settings.workspace.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('workspace_invitations', [
            'id' => $invitation->id,
            'status' => InvitationStatus::Cancelled,
        ]);
    }

    public function test_non_inviter_cannot_cancel(): void
    {
        $otherUser = User::factory()->create();
        $this->workspace->users()->attach($otherUser->id, ['joined_at' => now()]);

        $invitation = Invitation::factory()->create([
            'workspace_id' => $this->workspace->id,
            'inviter_id' => $this->owner->id,
            'email' => $this->inviteeEmail,
        ]);

        $this->actingAs($otherUser);

        $response = $this->delete(route('invitations.cancel', $invitation));

        $response->assertSessionHas('error');
    }

    public function test_owner_can_resend_invitation(): void
    {
        $invitation = Invitation::factory()->create([
            'workspace_id' => $this->workspace->id,
            'inviter_id' => $this->owner->id,
            'email' => $this->inviteeEmail,
        ]);

        $oldToken = $invitation->token;

        $this->actingAs($this->owner);

        $response = $this->post(route('invitations.resend', $invitation));

        $response->assertRedirect(route('settings.workspace.index'));
        $response->assertSessionHas('success');

        $invitation->refresh();
        $this->assertNotEquals($oldToken, $invitation->token);
    }

    public function test_resend_creates_notification(): void
    {
        $invitation = Invitation::factory()->create([
            'workspace_id' => $this->workspace->id,
            'inviter_id' => $this->owner->id,
            'email' => $this->inviteeEmail,
        ]);

        $this->actingAs($this->owner);
        $this->post(route('invitations.resend', $invitation));

        Notification::assertSentTo(
            $this->invitee,
            WorkspaceInvitationNotification::class
        );
    }

    public function test_expired_invitation_cannot_be_accepted(): void
    {
        $invitation = Invitation::factory()->create([
            'workspace_id' => $this->workspace->id,
            'inviter_id' => $this->owner->id,
            'email' => $this->inviteeEmail,
            'expires_at' => Carbon::now()->subDay(),
        ]);

        $this->actingAs($this->invitee);

        $response = $this->post(route('invitations.do-accept', $invitation));

        $response->assertSessionHas('error');
    }

    public function test_acceptance_of_expired_invitation_redirects_to_login(): void
    {
        Carbon::setTestNow(Carbon::now());

        $invitation = Invitation::factory()->create([
            'workspace_id' => $this->workspace->id,
            'inviter_id' => $this->owner->id,
            'email' => $this->inviteeEmail,
            'expires_at' => Carbon::now()->subDay(),
        ]);

        $this->actingAs($this->invitee);

        $response = $this->get(route('invitations.accept', $invitation->token));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');

        $invitation->refresh();
        $this->assertEquals(InvitationStatus::Expired, $invitation->status);

        Carbon::setTestNow();
    }

    public function test_accepted_invitation_cannot_be_acted_upon_again(): void
    {
        $invitation = Invitation::factory()->accepted()->create([
            'workspace_id' => $this->workspace->id,
            'inviter_id' => $this->owner->id,
            'email' => $this->inviteeEmail,
        ]);

        $this->actingAs($this->invitee);

        $this->post(route('invitations.do-accept', $invitation));
        $this->post(route('invitations.do-decline', $invitation));

        $invitation->refresh();
        $this->assertEquals(InvitationStatus::Accepted, $invitation->status);
    }

    public function test_cancel_only_cancels_pending_invitations(): void
    {
        $invitation = Invitation::factory()->accepted()->create([
            'workspace_id' => $this->workspace->id,
            'inviter_id' => $this->owner->id,
            'email' => $this->inviteeEmail,
        ]);

        $this->actingAs($this->owner);

        $this->delete(route('invitations.cancel', $invitation));

        $invitation->refresh();
        $this->assertEquals(InvitationStatus::Accepted, $invitation->status);
    }

    public function test_accept_page_redirects_if_not_authenticated(): void
    {
        $invitation = Invitation::factory()->create([
            'workspace_id' => $this->workspace->id,
            'inviter_id' => $this->owner->id,
            'email' => $this->inviteeEmail,
        ]);

        $response = $this->get(route('invitations.accept', $invitation->token));

        $response->assertRedirect(route('login'));
        $this->assertEquals($invitation->token, session('invitation_token'));
    }

    public function test_accept_page_redirects_if_email_mismatch(): void
    {
        $invitation = Invitation::factory()->create([
            'workspace_id' => $this->workspace->id,
            'inviter_id' => $this->owner->id,
            'email' => 'other@example.com',
        ]);

        $this->actingAs($this->invitee);

        $response = $this->get(route('invitations.accept', $invitation->token));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }
}
