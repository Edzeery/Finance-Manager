<?php

namespace Tests\Feature\Onboarding;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\Workspace;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Invoice;
use Database\Seeders\PaymentGatewaySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class PaymentStatusPageTest extends TestCase
{
    use RefreshDatabase;

    private Workspace $workspace;
    private User $user;
    private Payment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PaymentGatewaySeeder::class);

        Role::create(['slug' => 'workspace_admin', 'name_en' => 'Admin', 'level' => 'workspace', 'is_system' => true, 'sort_order' => 1]);

        $plan = SubscriptionPlan::create([
            'slug' => 'premium', 'name_en' => 'Premium', 'is_free' => false,
            'yearly_discount_percent' => 17,
            'is_active' => true, 'is_public' => true, 'sort_order' => 1,
        ]);

        \App\Models\PlanPrice::create([
            'plan_id' => $plan->id,
            'billing_period' => 'monthly',
            'currency' => 'USD',
            'price' => 19.99,
            'is_active' => true,
        ]);

        $this->workspace = Workspace::factory()->create();
        $this->user = User::factory()->create();
        $this->workspace->users()->attach($this->user->id, []);
        $this->user->current_workspace_id = $this->workspace->id;
        $this->user->save();
        $this->user->refresh();

        $subscription = Subscription::create([
            'workspace_id' => $this->workspace->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'past_due',
            'starts_at' => now(),
            'payment_method' => 'chargily',
        ]);

        $this->payment = Payment::factory()->create([
            'workspace_id' => $this->workspace->id,
            'user_id' => $this->user->id,
            'subscription_id' => $subscription->id,
            'method' => 'chargily',
            'transaction_id' => 'ch_retry_1',
            'status' => PaymentStatus::CheckoutCanceled,
            'amount' => 19.99,
            'currency' => 'USD',
            'canceled_at' => now(),
        ]);
    }

    public function test_guest_cannot_access_status_page(): void
    {
        $this->get(route('payment.status', ['payment' => $this->payment->id]))
            ->assertRedirect(route('login'));
    }

    public function test_status_page_shows_canceled_view(): void
    {
        $this->actingAs($this->user);

        Volt::test('pages.onboarding.payment-status', ['payment' => $this->payment])
            ->assertSet('payment.id', $this->payment->id)
            ->assertSet('view', 'canceled');
    }

    public function test_status_page_shows_payment_info(): void
    {
        $this->actingAs($this->user);

        Volt::test('pages.onboarding.payment-status', ['payment' => $this->payment])
            ->assertSee(__('onboarding.method_chargily'))
            ->assertSee('19.99');
    }

    public function test_status_page_shows_subscription_plan_info(): void
    {
        $this->actingAs($this->user);

        Volt::test('pages.onboarding.payment-status', ['payment' => $this->payment])
            ->assertSee('Premium');
    }

    public function test_status_page_shows_invoice_when_exists(): void
    {
        $this->actingAs($this->user);

        $subscription = $this->payment->subscription;
        $invoice = Invoice::create([
            'workspace_id' => $this->workspace->id,
            'subscription_id' => $subscription->id,
            'user_id' => $this->user->id,
            'number' => 'INV-000001',
            'status' => 'draft',
            'subtotal' => 19.99,
            'tax' => 0,
            'discount' => 0,
            'total' => 19.99,
            'currency' => 'USD',
        ]);

        Volt::test('pages.onboarding.payment-status', ['payment' => $this->payment])
            ->assertSee(__('onboarding.invoice_info'))
            ->assertSee('INV-000001')
            ->assertSee('19.99');
    }

    public function test_status_page_redirects_to_setup_when_payment_completed(): void
    {
        $this->actingAs($this->user);

        $this->payment->update(['status' => PaymentStatus::CheckoutPaid, 'paid_at' => now()]);

        Volt::test('pages.onboarding.payment-status', ['payment' => $this->payment->fresh()])
            ->assertRedirect(route('onboarding.setup', absolute: false));
    }

    public function test_status_page_denies_other_user_payment(): void
    {
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);

        Volt::test('pages.onboarding.payment-status', ['payment' => $this->payment])
            ->assertSet('errorMessage', __('onboarding.payment_not_found'))
            ->assertSee(__('onboarding.payment_not_found'));
    }

    public function test_switch_gateway_redirects_to_plan(): void
    {
        $this->actingAs($this->user);

        Volt::test('pages.onboarding.payment-status', ['payment' => $this->payment])
            ->call('switchGateway')
            ->assertRedirect(route('onboarding.plan', absolute: false));
    }

    public function test_cancel_and_change_plan_redirects_to_plan(): void
    {
        $this->actingAs($this->user);

        Volt::test('pages.onboarding.payment-status', ['payment' => $this->payment])
            ->call('cancelPaymentAndChangePlan')
            ->assertRedirect(route('onboarding.plan', absolute: false));
    }

    public function test_status_page_shows_payment_method_type_when_set(): void
    {
        $this->actingAs($this->user);

        $this->payment->update(['payment_method_type' => 'edahabia']);

        Volt::test('pages.onboarding.payment-status', ['payment' => $this->payment->fresh()])
            ->assertSee('edahabia');
    }

    public function test_status_badge_shows_correct_style_for_canceled(): void
    {
        $this->actingAs($this->user);

        Volt::test('pages.onboarding.payment-status', ['payment' => $this->payment])
            ->assertSeeHtml('bg-danger text-white');
    }

    public function test_method_label_shows_translated_name(): void
    {
        $this->actingAs($this->user);

        Volt::test('pages.onboarding.payment-status', ['payment' => $this->payment])
            ->assertSee(__('onboarding.method_chargily'));
    }
}
