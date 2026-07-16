<?php

use App\Models\SubscriptionPlan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create the Free plan if it doesn't exist yet (so we have an ID to reference)
        $freePlan = SubscriptionPlan::firstOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Free',
                'description' => 'Free forever — basic finance management for individuals.',
                'sort_order' => 0,
                'is_free' => true,
                'trial_days' => null,
                'is_active' => true,
                'is_public' => true,
                'button_text' => 'Get Started Free',
            ]
        );

        // 2. Find the Personal plan (the old free one)
        $personalPlan = SubscriptionPlan::where('slug', 'personal')->first();

        if ($personalPlan && $freePlan) {
            // 3. Migrate existing subscriptions on Personal (free) to Free plan
            DB::table('subscriptions')
                ->where('subscription_plan_id', $personalPlan->id)
                ->where('plan_price_amount', 0)
                ->whereIn('payment_method', ['free', null])
                ->update(['subscription_plan_id' => $freePlan->id]);

            // 4. Also update users whose pending_plan_id points to Personal
            DB::table('users')
                ->where('pending_plan_id', $personalPlan->id)
                ->update(['pending_plan_id' => $freePlan->id]);
        }
    }

    public function down(): void
    {
        $freePlan = SubscriptionPlan::where('slug', 'free')->first();
        $personalPlan = SubscriptionPlan::where('slug', 'personal')->first();

        if ($personalPlan && $freePlan) {
            DB::table('subscriptions')
                ->where('subscription_plan_id', $freePlan->id)
                ->update(['subscription_plan_id' => $personalPlan->id]);

            DB::table('users')
                ->where('pending_plan_id', $freePlan->id)
                ->update(['pending_plan_id' => $personalPlan->id]);
        }

        $freePlan?->delete();
    }
};
