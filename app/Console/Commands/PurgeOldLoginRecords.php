<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\LoginAttempt;
use Illuminate\Console\Command;

class PurgeOldLoginRecords extends Command
{
    protected $signature = 'auth:purge-old-records {--days=90 : Number of days to keep}';

    protected $description = 'Purge old login attempts and activity logs to keep the database clean';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $loginAttempts = LoginAttempt::where('created_at', '<', $cutoff)->delete();
        $activityLogs = ActivityLog::where('subject_type', 'auth')
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->info("Purged {$loginAttempts} login attempt(s) and {$activityLogs} activity log(s) older than {$days} days.");

        return Command::SUCCESS;
    }
}
