<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('finance:send-debt-reminders')->dailyAt('08:00')->withoutOverlapping();
        $schedule->command('finance:check-budget-alerts')->dailyAt('09:00')->withoutOverlapping();
        $schedule->command('finance:check-goal-progress')->dailyAt('10:00')->withoutOverlapping();
        $schedule->command('finance:process-recurring')->dailyAt('03:00')->withoutOverlapping();
        $schedule->command('finance:send-zakat-reminders')->monthlyOn(1, '10:00')->withoutOverlapping();
        $schedule->command('backup:run --only-db --disable-notifications')->dailyAt('02:00')->withoutOverlapping();
        $schedule->command('backup:clean --disable-notifications')->dailyAt('02:30')->withoutOverlapping();
        $schedule->command('subscriptions:expire')->dailyAt('00:01')->withoutOverlapping();

        // Grace period expiry reminders (3 days before ends_at)
        $schedule->command('subscriptions:remind-expiry')->dailyAt('09:00')->withoutOverlapping();
        $schedule->command('noest:check-deliveries')->hourly()->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
