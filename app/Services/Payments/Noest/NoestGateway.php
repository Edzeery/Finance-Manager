<?php

namespace App\Services\Payments\Noest;

use App\Models\Payment;
use App\Services\Payments\PaymentGateway;
use App\Services\Payments\PaymentResult;

class NoestGateway implements PaymentGateway
{
    public function name(): string
    {
        return 'noest';
    }

    public function charge(array $data): PaymentResult
    {
        $payment = Payment::find($data['payment_id']);
        if (!$payment) {
            return PaymentResult::failed('Payment not found.');
        }

        $user = $payment->user;

        try {
            $response = app(NoestService::class)->createOrder([
                'reference' => $payment->reference,
                'client' => $data['noest_client'] ?? $user?->name ?? 'Client',
                'phone' => $data['noest_phone'] ?? $user?->phone ?? '',
                'phone_2' => $data['noest_phone_2'] ?? '',
                'adresse' => $data['noest_adresse'] ?? $payment->workspace?->address ?? '',
                'wilaya_id' => $data['noest_wilaya'] ?? 16,
                'commune' => $data['noest_commune'] ?? '',
                'montant' => $data['amount'] ?? $payment->amount,
                'produit' => $data['noest_produit'] ?? 'Finance Manager Subscription',
                'type_id' => 1,
                'poids' => 0.5,
                'stop_desk' => !empty($data['noest_stop_desk']) ? 1 : 0,
                'station_code' => $data['noest_station_code'] ?? '',
                'can_open' => !empty($data['noest_can_open']) ? 1 : 0,
                'remboursement' => isset($data['noest_remboursement']) ? ($data['noest_remboursement'] ? 1 : 0) : 1,
                'is_payed' => 1,
                'remarque' => $data['noest_remarque'] ?? '',
            ]);

            $tracking = $response['data']['tracking']
                ?? $response['data']['number']
                ?? $response['data']['id']
                ?? $response['tracking']
                ?? null;

            if (!$tracking) {
                return PaymentResult::failed('No tracking number received from Noest.');
            }

            return PaymentResult::pending(
                message: 'Noest delivery order created. Awaiting delivery confirmation.',
                transactionId: $tracking,
                reference: $payment->reference,
                metadata: [
                    'noest_response' => $response,
                    'noest_tracking' => $tracking,
                    'awaiting_delivery' => true,
                ],
            );
        } catch (\Exception $e) {
            $apiMessage = $e->getMessage();
            return PaymentResult::failed(NoestErrorHandler::translate($apiMessage));
        }
    }

    public function refund(Payment $payment, ?float $amount = null): PaymentResult
    {
        return PaymentResult::failed('Noest refunds must be requested manually via the Noest dashboard.');
    }

    public function verify(Payment $payment): PaymentResult
    {
        $tracking = $payment->transaction_id;
        if (!$tracking) {
            return PaymentResult::failed('No tracking number found for this payment.');
        }

        try {
            $info = app(NoestService::class)->getTrackingInfo($tracking);
            return PaymentResult::success(
                message: 'Tracking info retrieved.',
                transactionId: $tracking,
                metadata: ['tracking_info' => $info],
            );
        } catch (\Exception $e) {
            return PaymentResult::failed('Failed to retrieve tracking info: ' . $e->getMessage());
        }
    }

    public function isOnline(): bool
    {
        return false;
    }

    public function isOffline(): bool
    {
        return true;
    }

    public function supportedCurrencies(): array
    {
        return ['DZD'];
    }
}
