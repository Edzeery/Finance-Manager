<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            RateLimitSettingsSeeder::class,
            CurrencySeeder::class,
            EnterpriseRolePermissionSeeder::class,
            SubscriptionPlanSeeder::class,
            PaymentGatewaySeeder::class,
            PaymentMethodSeeder::class,
            DemoDataSeeder::class,
        ]);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
            'plan_confirmed_at' => now(),
            'locale' => 'ar',
            'theme' => 'light',
            'currency' => 'DZD',
            'timezone' => 'Africa/Algiers',
            'is_active' => true,
        ]);

        $admin->roles()->attach(
            Role::where('slug', 'super_admin')->first()
        );

        $this->call(MigrateToWorkspacesSeeder::class);

        // Upgrade super admin to professional plan for testing
        $professionalPlan = \App\Models\SubscriptionPlan::where('slug', 'professional')->first();
        if ($professionalPlan) {
            $adminSub = \App\Models\Subscription::withoutWorkspace()
                ->where('user_id', $admin->id)
                ->latest()
                ->first();
            if ($adminSub) {
                $adminSub->update([
                    'subscription_plan_id' => $professionalPlan->id,
                    'billing_period' => 'monthly',
                ]);
            }
        }

        $this->call(TestChecklistSeeder::class);

        User::flushAllPermissionCaches();
    }
}
