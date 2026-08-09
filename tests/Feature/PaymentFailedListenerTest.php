<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Events\PaymentFailed;
use App\Models\AdminNotification;
use App\Models\Payment;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentFailedListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_failed_creates_admin_notification(): void
    {
        $workspace = Workspace::factory()->create();
        $user = User::factory()->create();
        $workspace->users()->attach($user->id, []);

        $payment = Payment::factory()->forMethod('stripe')->create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'status' => PaymentStatus::CheckoutFailed,
        ]);

        event(new PaymentFailed($payment));

        $notification = AdminNotification::where('type', 'payment_failed')->first();

        $this->assertNotNull($notification);
        $this->assertSame($payment->id, $notification->data['payment_id']);
        $this->assertSame($user->id, $notification->data['user_id']);
    }
}
