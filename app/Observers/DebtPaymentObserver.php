<?php

namespace App\Observers;

use App\Enums\DebtStatus;
use App\Models\DebtPayment;

class DebtPaymentObserver
{
    public function created(DebtPayment $payment): void
    {
        $this->syncDebtStatus($payment);
    }

    public function deleted(DebtPayment $payment): void
    {
        $this->syncDebtStatus($payment);
    }

    private function syncDebtStatus(DebtPayment $payment): void
    {
        $debt = $payment->debt;
        if (!$debt) {
            return;
        }

        $totalPaid = $debt->payments()->sum('amount');
        $status = match (true) {
            $totalPaid >= $debt->total_amount => DebtStatus::Paid,
            $totalPaid > 0 => DebtStatus::Partial,
            default => DebtStatus::Active,
        };

        $debt->update([
            'paid_amount' => $totalPaid,
            'status' => $status,
        ]);
    }
}
