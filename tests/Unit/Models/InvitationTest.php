<?php

namespace Tests\Unit\Models;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invitation = Invitation::factory()->create();
    }

    public function test_it_has_correct_table(): void
    {
        $this->assertEquals('workspace_invitations', $this->invitation->getTable());
    }

    public function test_it_casts_status_to_enum(): void
    {
        $this->assertTrue($this->invitation->status instanceof InvitationStatus);
        $this->assertEquals(InvitationStatus::Pending, $this->invitation->status);
    }

    public function test_it_casts_datetime_attributes(): void
    {
        $this->assertTrue($this->invitation->expires_at instanceof Carbon);
    }

    public function test_it_belongs_to_workspace(): void
    {
        $this->assertTrue($this->invitation->workspace instanceof Workspace);
    }

    public function test_it_belongs_to_inviter(): void
    {
        $this->assertTrue($this->invitation->inviter instanceof User);
    }

    public function test_is_pending_returns_true_for_pending(): void
    {
        $invitation = Invitation::factory()->pending()->create();
        $this->assertTrue($invitation->isPending());
    }

    public function test_is_pending_returns_false_for_accepted(): void
    {
        $invitation = Invitation::factory()->accepted()->create();
        $this->assertFalse($invitation->isPending());
    }

    public function test_is_pending_returns_false_for_declined(): void
    {
        $invitation = Invitation::factory()->declined()->create();
        $this->assertFalse($invitation->isPending());
    }

    public function test_is_pending_returns_false_for_expired(): void
    {
        $invitation = Invitation::factory()->expired()->create();
        $this->assertFalse($invitation->isPending());
    }

    public function test_is_expired_returns_true_when_expired(): void
    {
        $invitation = Invitation::factory()->expired()->create();
        $this->assertTrue($invitation->isExpired());
    }

    public function test_is_expired_returns_false_when_not_expired(): void
    {
        $this->assertFalse($this->invitation->isExpired());
    }

    public function test_is_acceptable_returns_true_for_pending_not_expired(): void
    {
        $this->assertTrue($this->invitation->isAcceptable());
    }

    public function test_is_acceptable_returns_false_for_expired(): void
    {
        $invitation = Invitation::factory()->expired()->create();
        $this->assertFalse($invitation->isAcceptable());
    }

    public function test_is_acceptable_returns_false_for_accepted(): void
    {
        $invitation = Invitation::factory()->accepted()->create();
        $this->assertFalse($invitation->isAcceptable());
    }

    public function test_is_acceptable_returns_false_for_declined(): void
    {
        $invitation = Invitation::factory()->declined()->create();
        $this->assertFalse($invitation->isAcceptable());
    }

    public function test_mark_as_accepted(): void
    {
        $this->invitation->markAsAccepted();
        $this->invitation->refresh();

        $this->assertEquals(InvitationStatus::Accepted, $this->invitation->status);
        $this->assertNotNull($this->invitation->accepted_at);
    }

    public function test_mark_as_declined(): void
    {
        $this->invitation->markAsDeclined();
        $this->invitation->refresh();

        $this->assertEquals(InvitationStatus::Declined, $this->invitation->status);
        $this->assertNotNull($this->invitation->declined_at);
    }

    public function test_mark_as_cancelled(): void
    {
        $this->invitation->markAsCancelled();
        $this->invitation->refresh();

        $this->assertEquals(InvitationStatus::Cancelled, $this->invitation->status);
        $this->assertNotNull($this->invitation->cancelled_at);
    }

    public function test_mark_as_expired(): void
    {
        $this->invitation->markAsExpired();
        $this->invitation->refresh();

        $this->assertEquals(InvitationStatus::Expired, $this->invitation->status);
    }

    public function test_generate_token_returns_64_char_string(): void
    {
        $token = Invitation::generateToken();
        $this->assertEquals(64, strlen($token));
    }

    public function test_generate_token_is_unique(): void
    {
        $tokens = [];
        for ($i = 0; $i < 100; $i++) {
            $tokens[] = Invitation::generateToken();
        }
        $this->assertEquals(100, count(array_unique($tokens)));
    }

    public function test_default_expiry_is_7_days_from_now(): void
    {
        $expiry = Invitation::defaultExpiry();
        $expected = Carbon::now()->addDays(config('invitation.expiry_days', 7));
        $this->assertTrue($expiry->diffInSeconds($expected) < 2);
    }

    public function test_pending_scope_returns_only_pending(): void
    {
        Invitation::factory()->accepted()->create();
        Invitation::factory()->pending()->create();

        $pendings = Invitation::pending()->get();
        $this->assertEquals(2, $pendings->count());
        $this->assertEquals(InvitationStatus::Pending, $pendings->first()->status);
    }

    public function test_for_email_scope_is_case_insensitive(): void
    {
        Invitation::factory()->create(['email' => 'Test@Example.COM']);

        $results = Invitation::forEmail('test@example.com')->get();
        $this->assertEquals(1, $results->count());
    }

    public function test_soft_deletes_are_not_used(): void
    {
        $this->assertFalse(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(Invitation::class)));
    }
}
