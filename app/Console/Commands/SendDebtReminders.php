<?php

namespace App\Console\Commands;

use App\Enums\DebtStatus;
use App\Mail\DebtReminder;
use App\Models\Debt;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDebtReminders extends Command
{
    protected $signature = 'finance:send-debt-reminders';

    protected $description = 'Send notifications for overdue and due debts';

    public function handle(NotificationService $notifier): int
    {
        $today = now()->startOfDay();
        $reminderDate = $today->copy()->addDays(3);

        $debts = Debt::whereIn('status', [DebtStatus::Active->value, DebtStatus::Partial->value, DebtStatus::Overdue->value])
            ->where(function ($q) use ($today, $reminderDate) {
                $q->where('due_date', $today)
                    ->orWhere('due_date', $reminderDate)
                    ->orWhere(function ($sub) use ($today) {
                        $sub->where('due_date', '<', $today)
                            ->where('status', '!=', DebtStatus::Overdue->value);
                    });
            })
            ->get();

        if ($debts->isEmpty()) {
            $this->info('No debts due for reminder.');

            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($debts as $debt) {
            try {
                $notifier->debtReminder(
                    $debt->user_id,
                    $debt->counterparty_name,
                    $debt->remaining_amount,
                    $debt->due_date?->format('Y-m-d') ?? '—'
                );

                $user = User::find($debt->user_id);
                if ($user && $user->email) {
                    Mail::to($user)->queue(new DebtReminder($debt));
                }

                if ($debt->due_date && $debt->due_date->isPast() && $debt->status !== DebtStatus::Overdue) {
                    $debt->update(['status' => DebtStatus::Overdue->value]);
                }

                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for debt #{$debt->id}: {$e->getMessage()}");
            }
        }

        $this->info("Sent {$count} debt reminder(s).");

        return Command::SUCCESS;
    }
}
