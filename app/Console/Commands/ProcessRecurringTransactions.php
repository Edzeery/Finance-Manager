<?php

namespace App\Console\Commands;

use App\Enums\RecurringFrequency;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

class ProcessRecurringTransactions extends Command
{
    protected $signature = 'finance:process-recurring';

    protected $description = 'Process recurring transactions for the current period';

    public function handle(): int
    {
        $today = now()->startOfDay();
        $processed = ['income' => 0, 'expense' => 0];

        Income::where('is_recurring', true)
            ->chunk(100, function ($incomes) use ($today, &$processed) {
                foreach ($incomes as $income) {
                    try {
                        $lastDate = $income->date;
                        $created = false;

                        while (true) {
                            $nextDate = $this->getNextRecurringDate($lastDate, $income->recurring_frequency);

                            if (! $nextDate || $nextDate > $today || ($income->recurring_end_date && $nextDate > $income->recurring_end_date)) {
                                break;
                            }

                            $this->materializeOccurrence($income, $lastDate);

                            $lastDate = $nextDate;
                            $created = true;
                            $processed['income']++;
                        }

                        if ($created) {
                            $income->update(['date' => $lastDate]);
                        }
                    } catch (\Exception $e) {
                        $this->error("Failed to process income #{$income->id}: {$e->getMessage()}");
                    }
                }
            });

        Expense::where('is_recurring', true)
            ->chunk(100, function ($expenses) use ($today, &$processed) {
                foreach ($expenses as $expense) {
                    try {
                        $lastDate = $expense->date;
                        $created = false;

                        while (true) {
                            $nextDate = $this->getNextRecurringDate($lastDate, $expense->recurring_frequency);

                            if (! $nextDate || $nextDate > $today || ($expense->recurring_end_date && $nextDate > $expense->recurring_end_date)) {
                                break;
                            }

                            $this->materializeOccurrence($expense, $lastDate);

                            $lastDate = $nextDate;
                            $created = true;
                            $processed['expense']++;
                        }

                        if ($created) {
                            $expense->update(['date' => $lastDate]);
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

    private function materializeOccurrence(Model $template, Carbon $date): void
    {
        $new = $template->replicate(['is_archived', 'is_recurring', 'recurring_frequency', 'recurring_end_date']);
        $new->date = $date;
        $new->is_archived = false;
        $new->is_recurring = false;
        $new->created_at = now();
        $new->updated_at = now();
        $new->save();
    }

    private function getNextRecurringDate(Carbon $lastDate, ?RecurringFrequency $frequency): ?Carbon
    {
        if ($frequency === null) {
            return null;
        }

        return match ($frequency->value) {
            'daily' => $lastDate->copy()->addDay(),
            'weekly' => $lastDate->copy()->addWeek(),
            'monthly' => $lastDate->copy()->addMonth(),
            'yearly' => $lastDate->copy()->addYear(),
            default => null,
        };
    }
}
