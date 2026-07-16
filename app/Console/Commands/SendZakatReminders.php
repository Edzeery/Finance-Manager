<?php

namespace App\Console\Commands;

use App\Enums\AssetType;
use App\Models\User;
use App\Models\ZakatRecord;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendZakatReminders extends Command
{
    protected $signature = 'finance:send-zakat-reminders';

    protected $description = 'Remind users about upcoming zakat calculations';

    public function handle(): int
    {
        $users = User::whereHas('assets', function ($q) {
            $q->whereIn('type', AssetType::zakatableValues());
        })->get();

        if ($users->isEmpty()) {
            $this->info('No users with zakatable assets found.');

            return Command::SUCCESS;
        }

        $count = 0;
        $lastYear = now()->subYear();

        foreach ($users as $user) {
            try {
                $hasRecentRecord = ZakatRecord::where('user_id', $user->id)
                    ->where('calculation_date', '>=', $lastYear)
                    ->exists();

                if ($hasRecentRecord) {
                    continue;
                }

                app(NotificationService::class)->zakatReminder($user->id);

                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to send zakat reminder to user #{$user->id}: {$e->getMessage()}");
            }
        }

        $this->info("Sent {$count} zakat reminder(s).");

        return Command::SUCCESS;
    }
}
