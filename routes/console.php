<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('health:check', fn () => $this->call('finance:health-check'))
    ->purpose('Run system health checks');

Artisan::command('validate:soft-deletes', fn () => $this->call('finance:validate-soft-deletes'))
    ->purpose('Verify SoftDeletes models have deleted_at columns');

Artisan::command('onboarding:cleanup-abandoned', function () {
    $cutoff = now()->subHours(24);

    $count = \App\Models\Payment::pending()
        ->where('created_at', '<', $cutoff)
        ->whereNull('transaction_id')
        ->update(['status' => \App\Enums\PaymentStatus::CheckoutFailed->value, 'notes' => 'Abandoned payment - no transaction within 24 hours']);

    $workspaceCount = 0;
    \App\Models\Workspace::where('created_at', '<', $cutoff)
        ->get()
        ->filter(fn($ws) => !$ws->owner()?->first()?->subscriptions()->exists())
        ->each(function ($workspace) use (&$workspaceCount) {
            $workspace->users()->detach();
            $workspace->delete();
            $workspaceCount++;
        });

    $this->info("Marked {$count} abandoned payments as failed.");
    $this->info("Cleaned up {$workspaceCount} orphaned workspaces with no subscription.");
})->purpose('Mark abandoned payments as failed and remove orphaned workspaces after 24 hours');

Artisan::command('onboarding:backfill-existing-users', function () {
    $count = \App\Models\User::whereNull('onboarding_completed_at')->count();

    \App\Models\User::whereNull('onboarding_completed_at')->update([
        'onboarding_completed_at' => now(),
        'plan_confirmed_at' => now(),
    ]);

    $this->info("Backfilled onboarding status for {$count} existing users.");
})->purpose('Mark all existing users as having completed onboarding');
