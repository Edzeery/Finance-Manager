<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Events\PaymentCompleted;
use App\Events\SubscriptionActivated;
use App\Models\AdminNotification;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_completed_creates_admin_notification(): void
    {
        $workspace = Workspace::factory()->create();
        $user = User::factory()->create();
        $workspace->users()->attach($user->id, []);

        $payment = Payment::factory()->forMethod('stripe')->create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'status' => PaymentStatus::CheckoutPaid,
        ]);

        event(new PaymentCompleted($payment));

        $notification = AdminNotification::where('type', 'new_payment')->first();

        $this->assertNotNull($notification);
        $this->assertSame($payment->id, $notification->data['payment_id']);
        $this->assertSame($user->id, $notification->data['user_id']);
    }

    public function test_subscription_activated_creates_admin_notification(): void
    {
        $subscription = Subscription::factory()->active()->create();

        $payment = Payment::factory()->create([
            'subscription_id' => $subscription->id,
            'workspace_id' => $subscription->workspace_id,
            'user_id' => $subscription->user_id,
        ]);

        event(new SubscriptionActivated($subscription, $payment));

        $notification = AdminNotification::where('type', 'subscription_activated')->first();

        $this->assertNotNull($notification);
        $this->assertSame($subscription->user_id, $notification->data['user_id']);
        $this->assertStringContainsString($subscription->plan->name, $notification->message_en);
    }

    public function test_user_registered_creates_admin_notification(): void
    {
        $user = User::factory()->create();

        event(new Registered($user));

        $notification = AdminNotification::where('type', 'new_user')->first();

        $this->assertNotNull($notification);
        $this->assertSame($user->id, $notification->data['user_id']);
    }
}
