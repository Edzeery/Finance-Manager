<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\Payments\Noest\NoestService;
use App\Services\PaymentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckNoestDeliveries extends Command
{
    protected $signature = 'noest:check-deliveries';

    protected $description = 'Check Noest delivery status for pending payments';

    public function handle(PaymentService $paymentService): int
    {
        $noestService = app(NoestService::class);

        Payment::where('method', 'noest')
            ->where('status', PaymentStatus::CheckoutPending->value)
            ->whereNotNull('transaction_id')
            ->chunk(50, function ($payments) use ($noestService, $paymentService) {
                foreach ($payments as $payment) {
                    try {
                        $status = $noestService->getTrackingInfo($payment->transaction_id);
                        $deliveryStatus = $status['data']['status'] ?? '';

                        $normalized = strtolower(trim($deliveryStatus));

                        if (in_array($normalized, ['livré', 'delivered', 'livre'])) {
                            $paymentService->applyPaymentSideEffects($payment, 'approved');
                            $this->info("Noest delivery confirmed: payment {$payment->id}");
                            Log::info('Noest delivery confirmed', [
                                'payment_id' => $payment->id,
                                'tracking' => $payment->transaction_id,
                            ]);
                        } elseif (in_array($normalized, ['retour', 'returned', 'annulé'])) {
                            $payment->update(['status' => PaymentStatus::CheckoutFailed, 'failed_at' => now()]);
                            $this->warn("Noest delivery returned: payment {$payment->id}");
                            Log::info('Noest delivery returned', [
                                'payment_id' => $payment->id,
                                'tracking' => $payment->transaction_id,
                            ]);
                        }
                    } catch (\Exception $e) {
                        $this->warn("Noest polling failed for payment {$payment->id}: {$e->getMessage()}");
                        Log::warning('Noest polling failed', [
                            'payment_id' => $payment->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            });

        $this->info('Noest delivery check completed.');

        return self::SUCCESS;
    }
}
