<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Invoice;

class InvoiceObserver
{
    public function created(Invoice $invoice): void
    {
        if (! $invoice->isPaid()) {
            return;
        }

        $this->createExpenseFromInvoice($invoice);
    }

    public function updated(Invoice $invoice): void
    {
        if (! $invoice->isPaid() || $invoice->wasChanged('status')) {
            if ($invoice->isPaid() && $invoice->wasChanged('status')) {
                $this->createExpenseFromInvoice($invoice);
            }

            return;
        }
    }

    private function createExpenseFromInvoice(Invoice $invoice): void
    {
        $existing = Expense::where('user_id', $invoice->user_id)
            ->where('notes', 'LIKE', '%فاتورة اشتراك #'.$invoice->number.'%')
            ->first();

        if ($existing) {
            return;
        }

        $subscriptionCategory = ExpenseCategory::where('user_id', $invoice->user_id)
            ->where(function ($q) {
                $q->where('name_ar', 'الاشتراكات')
                    ->orWhere('name_en', 'Subscriptions')
                    ->orWhere('name_fr', 'Abonnements');
            })
            ->first();

        if (! $subscriptionCategory) {
            $subscriptionCategory = ExpenseCategory::whereNull('user_id')
                ->where(function ($q) {
                    $q->where('name_ar', 'الاشتراكات')
                        ->orWhere('name_en', 'Subscriptions')
                        ->orWhere('name_fr', 'Abonnements');
                })
                ->first();
        }

        if (! $subscriptionCategory) {
            $subscriptionCategory = ExpenseCategory::whereNull('user_id')
                ->where('name_en', 'Other')
                ->first();
        }

        if (! $subscriptionCategory) {
            return;
        }

        $plan = $invoice->subscription?->subscriptionPlan;
        $planName = $plan?->name_en ?? $plan?->name_ar ?? 'Subscription';

        Expense::create([
            'user_id' => $invoice->user_id,
            'workspace_id' => $invoice->workspace_id,
            'category_id' => $subscriptionCategory->id,
            'amount' => $invoice->total,
            'description' => "Subscription: {$planName} ({$invoice->billing_period})",
            'date' => now()->toDateString(),
            'is_recurring' => true,
            'recurring_frequency' => $invoice->billing_period === 'yearly' ? 'yearly' : 'monthly',
            'notes' => "فاتورة اشتراك #{$invoice->number}",
        ]);
    }
}
