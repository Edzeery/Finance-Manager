<?php

namespace App\Console\Commands;

use App\Enums\RecurringFrequency;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Console\Command;

class ProcessRecurringTransactions extends Command
{
    protected $signature = 'finance:process-recurring';
    protected $description = 'Process recurring transactions for the current period';

    public function handle(): int
    {
        $today = now()->startOfDay();
        $processed = ['income' => 0, 'expense' => 0];

        Income::where('is_recurring', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('recurring_end_date')
                  ->orWhere('recurring_end_date', '>=', $today);
            })
            ->chunk(100, function ($incomes) use ($today, &$processed) {
                foreach ($incomes as $income) {
                    try {
                        $lastDate = $income->date;
                        $nextDate = $this->getNextRecurringDate($lastDate, $income->recurring_frequency);

                        if ($nextDate && $nextDate <= $today && (!$income->recurring_end_date || $nextDate <= $income->recurring_end_date)) {
                            $new = $income->replicate(['is_archived']);
                            $new->date = $nextDate;
                            $new->is_archived = false;
                            $new->created_at = now();
                            $new->updated_at = now();
                            $new->save();

                            $income->update(['date' => $nextDate]);
                            $processed['income']++;
                        }
                    } catch (\Exception $e) {
                        $this->error("Failed to process income #{$income->id}: {$e->getMessage()}");
                    }
                }
            });

        Expense::where('is_recurring', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('recurring_end_date')
                  ->orWhere('recurring_end_date', '>=', $today);
            })
            ->chunk(100, function ($expenses) use ($today, &$processed) {
                foreach ($expenses as $expense) {
                    try {
                        $lastDate = $expense->date;
                        $nextDate = $this->getNextRecurringDate($lastDate, $expense->recurring_frequency);

                        if ($nextDate && $nextDate <= $today && (!$expense->recurring_end_date || $nextDate <= $expense->recurring_end_date)) {
                            $new = $expense->replicate(['is_archived']);
                            $new->date = $nextDate;
                            $new->is_archived = false;
                            $new->created_at = now();
                            $new->updated_at = now();
                            $new->save();

                            $expense->update(['date' => $nextDate]);
                            $processed['expense']++;
                        }
                    } catch (\Exception $e) {
                        $this->error("Failed to process expense #{$expense->id}: {$e->getMessage()}");
                    }
                }
            });

        $total = $processed['income'] + $processed['expense'];
        $this->info("Processed {$total} recurring transaction(s) (Income: {$processed['income']}, Expense: {$processed['expense']}).");
        return Command::SUCCESS;
    }

    private function getNextRecurringDate(\Carbon\Carbon $lastDate, string $frequency): ?\Carbon\Carbon
    {
        return match ($frequency) {
            RecurringFrequency::Daily->value => $lastDate->copy()->addDay(),
            RecurringFrequency::Weekly->value => $lastDate->copy()->addWeek(),
            RecurringFrequency::Monthly->value => $lastDate->copy()->addMonth(),
            RecurringFrequency::Yearly->value => $lastDate->copy()->addYear(),
            default => null,
        };
    }
}
