<?php

namespace App\Console\Commands;

use App\Enums\AssetType;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendZakatReminders extends Command
{
    protected $signature = 'finance:send-zakat-reminders';

    protected $description = 'Send zakat reminders: approaching haul (30/7/1 day) and due reminders';

    private const REMINDER_THRESHOLDS = [30, 7, 1];

    public function handle(): int
    {
        $users = User::whereHas('assets', function ($q) {
            $q->whereIn('type', AssetType::zakatableValues());
        })->whereNotNull('zakat_start_date')
            ->get();

        if ($users->isEmpty()) {
            $this->info('No eligible users found.');

            return Command::SUCCESS;
        }

        $sent = 0;
        $notificationService = app(NotificationService::class);

        foreach ($users as $user) {
            try {
                $daysLeft = $user->daysUntilNextZakat();

                if ($daysLeft === null) {
                    continue;
                }

                if ($user->isZakatDue()) {
                    $lastSent = cache()->get("zakat_due_reminder_{$user->id}");

                    if (! $lastSent) {
                        $notificationService->zakatReminder($user->id);
                        cache()->put("zakat_due_reminder_{$user->id}", now(), 86400);
                        $sent++;
                        $this->info("User #{$user->id}: zakat due reminder sent.");
                    }

                    continue;
                }

                if (in_array($daysLeft, self::REMINDER_THRESHOLDS)) {
                    $cacheKey = "zakat_approach_{$user->id}_{$daysLeft}";

                    if (! cache()->has($cacheKey)) {
                        $notificationService->zakatApproachingReminder($user->id, $daysLeft);
                        cache()->put($cacheKey, true, 86400 * 2);
                        $sent++;
                        $this->info("User #{$user->id}: {$daysLeft}-day reminder sent.");
                    }
                }
            } catch (\Exception $e) {
                $this->error("Failed for user #{$user->id}: {$e->getMessage()}");
            }
        }

        $this->info("Sent {$sent} reminder(s).");

        return Command::SUCCESS;
    }
}
