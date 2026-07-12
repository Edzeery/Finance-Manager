<?php

namespace Database\Seeders;

use App\Models\Workspace;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateToWorkspacesSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereDoesntHave('workspaces')->get();

        foreach ($users as $user) {
            DB::transaction(function () use ($user) {
                $workspace = Workspace::create([
                    'name' => $user->name . "'s Workspace",
                    'slug' => 'personal-' . $user->id . '-' . now()->timestamp,
                    'type' => 'personal',
                    'currency' => $user->currency ?? 'DZD',
                    'timezone' => $user->timezone ?? 'Africa/Algiers',
                ]);

                $workspace->users()->attach($user->id, []);

                $adminRole = \App\Models\Role::where('slug', 'workspace_admin')->first();
                if ($adminRole) {
                    $user->workspaceRoleUsers()->attach($adminRole->id, ['workspace_id' => $workspace->id]);
                }

                $plan = \App\Models\SubscriptionPlan::where('slug', 'personal')->first();
                if ($plan) {
                    $workspace->allSubscriptions()->create([
                        'subscription_plan_id' => $plan->id,
                        'user_id' => $user->id,
                        'status' => 'active',
                        'starts_at' => now(),
                        'billing_period' => 'monthly',
                    ]);
                }

                $user->update(['current_workspace_id' => $workspace->id]);
            });
        }

        $this->command?->info('Migrated ' . $users->count() . ' users to workspaces.');
    }
}
