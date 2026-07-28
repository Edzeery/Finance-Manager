<?php

namespace App\Console\Commands;

use App\Enums\GoalStatus;
use App\Models\FinancialGoal;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckGoalProgress extends Command
{
    protected $signature = 'finance:check-goal-progress';

    protected $description = 'Check goals for completion, milestones, and approaching deadlines';

    public function handle(NotificationService $notifier): int
    {
        $goals = FinancialGoal::where('status', GoalStatus::InProgress->value)->get();

        if ($goals->isEmpty()) {
            $this->info('No in-progress goals to check.');

            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($goals as $goal) {
            try {
                $name = $goal->name;
                $progress = $goal->progress;

                if ($progress >= 100) {
                    $notifier->goalAchieved($goal->user_id, $name);
                    $goal->update(['status' => GoalStatus::Completed->value, 'completed_at' => now()]);
                    $count++;
                } else {
                    $milestone = (int) (floor($progress / 25) * 25);
                    if ($milestone > 0 && $milestone > $goal->last_milestone_notified) {
                        $notifier->goalMilestoneReached($goal->user_id, $name, $milestone);
                        $goal->update(['last_milestone_notified' => $milestone]);
                        $count++;
                    }

                    if ($goal->target_date && $goal->days_remaining !== null && $goal->days_remaining <= 7) {
                        $notifier->goalDeadlineApproaching($goal->user_id, $name, $goal->days_remaining);
                        $count++;
                    }
                }
            } catch (\Exception $e) {
                $this->error("Failed to check goal #{$goal->id}: {$e->getMessage()}");
            }
        }

        $this->info("Checked {$goals->count()} goal(s), sent {$count} notification(s).");

        return Command::SUCCESS;
    }
}
