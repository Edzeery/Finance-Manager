<?php

namespace App\Services\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentTransitionValidator
{
    public function validate(Payment $payment, PaymentStatus $target): bool
    {
        return $payment->status->canTransitionTo($target);
    }

    public function assert(Payment $payment, PaymentStatus $target): void
    {
        if (! $this->validate($payment, $target)) {
            throw new \RuntimeException(sprintf(
                'Illegal payment status transition: %s → %s',
                $payment->status->value,
                $target->value,
            ));
        }
    }

    public function transition(Payment $payment, PaymentStatus $target, array $extra = []): Payment
    {
        DB::transaction(function () use ($payment, $target, $extra) {
            $payment = Payment::withoutWorkspace()->lockForUpdate()->find($payment->id);

            if (! $payment) {
                throw new \RuntimeException('Payment not found for transition');
            }

            $this->assert($payment, $target);

            $data = array_merge([
                'status' => $target,
                'webhook_processed_at' => now(),
            ], $extra);

            if ($target->isFailure() && ! isset($extra['failed_at'])) {
                $data['failed_at'] = now();
            }

            if ($target === PaymentStatus::CheckoutPaid && ! isset($extra['paid_at'])) {
                $data['paid_at'] = now();
            }

            if ($target === PaymentStatus::CheckoutCanceled && ! isset($extra['canceled_at'])) {
                $data['canceled_at'] = now();
            }

            $payment->update($data);
        });

        return $payment->fresh();
    }
}
