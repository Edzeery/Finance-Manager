<?php

namespace Tests\Traits;

use App\Models\Role;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\EnterpriseRolePermissionSeeder;
use Database\Seeders\SubscriptionPlanSeeder;

trait WithWorkspacePermission
{
    protected User $workspaceUser;

    protected Workspace $workspace;

    protected function setUpWorkspacePermission(): void
    {
        $this->seed([
            EnterpriseRolePermissionSeeder::class,
            SubscriptionPlanSeeder::class,
        ]);

        $this->workspace = Workspace::factory()->create();

        $adminRole = Role::where('slug', 'workspace_admin')->first();
        $this->workspaceUser = User::factory()->create();
        $this->workspaceUser->workspaceRoleUsers()->attach($adminRole->id, ['workspace_id' => $this->workspace->id]);
        $this->workspaceUser->current_workspace_id = $this->workspace->id;
        $this->workspaceUser->save();
        $this->workspaceUser->refresh();

        $plan = SubscriptionPlan::where('slug', 'professional')->first() ?? SubscriptionPlan::first();
        if ($plan) {
            Subscription::withoutWorkspace()->create([
                'workspace_id' => $this->workspace->id,
                'user_id' => $this->workspaceUser->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addYear(),
                'billing_period' => 'monthly',
                'auto_renew' => true,
            ]);
        }
    }
}
