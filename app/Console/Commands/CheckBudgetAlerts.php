<?php

namespace App\Console\Commands;

use App\Models\Budget;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckBudgetAlerts extends Command
{
    protected $signature = 'finance:check-budget-alerts';

    protected $description = 'Check budgets for exceeded or nearing limits and send notifications';

    public function handle(NotificationService $notifier): int
    {
        $budgets = Budget::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->get();

        if ($budgets->isEmpty()) {
            $this->info('No active budgets to check.');

            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($budgets as $budget) {
            try {
                $name = $budget->name;
                $spentPercent = $budget->adherence_rate;

                if ($budget->is_exceeded) {
                    $notifier->budgetExceeded($budget->user_id, $name, $budget->totalSpent - $budget->total_amount);
                    $count++;
                } elseif ($spentPercent >= 80) {
                    $notifier->budgetNearingLimit($budget->user_id, $name, $spentPercent);
                    $count++;
                }
            } catch (\Exception $e) {
                $this->error("Failed to check budget #{$budget->id}: {$e->getMessage()}");
            }
        }

        $this->info("Checked {$budgets->count()} budget(s), sent {$count} alert(s).");

        return Command::SUCCESS;
    }
}
