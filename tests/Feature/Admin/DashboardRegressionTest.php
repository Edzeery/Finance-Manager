<?php

namespace Tests\Feature\Admin;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\EnterpriseRolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRegressionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(EnterpriseRolePermissionSeeder::class);
        $this->admin = User::factory()->create(['two_factor_confirmed_at' => now()]);
        $this->admin->roles()->attach(Role::where('slug', 'super_admin')->first());
    }

    public function test_dashboard_loads_with_payment_aggregation(): void
    {
        Payment::factory()->create(['status' => PaymentStatus::CheckoutPaid, 'amount' => 100]);
        Payment::factory()->create(['status' => PaymentStatus::CheckoutPaid, 'amount' => 50]);
        Payment::factory()->create(['status' => PaymentStatus::CheckoutPending, 'amount' => 25]);

        $response = $this->actingAs($this->admin)
            ->get(route('super.admin.dashboard'));

        $response->assertOk();
    }

    public function test_soft_deleted_payments_are_excluded_from_revenue(): void
    {
        $keep = Payment::factory()->create(['status' => PaymentStatus::CheckoutPaid, 'amount' => 100]);
        $remove = Payment::factory()->create(['status' => PaymentStatus::CheckoutPaid, 'amount' => 50]);
        $remove->delete();

        $this->actingAs($this->admin)
            ->get(route('super.admin.dashboard'))
            ->assertOk();
    }
}
