<?php

namespace App\Services\Payments\Chargily;

use App\Models\Payment;
use App\Services\Payments\Concerns\HasGatewaySettings;
use App\Services\Payments\PaymentGateway;
use App\Services\Payments\PaymentResult;
use App\Services\Payments\ValidationResult;

class ChargilyGateway implements PaymentGateway
{
    use HasGatewaySettings;

    public function __construct(
        private readonly ChargilyCheckoutService $checkoutService,
    ) {}

    public function name(): string
    {
        return 'chargily';
    }

    public function validate(array $data): ValidationResult
    {
        $this->checkoutService->setMethod($this->_cachedMethod ?? null);
        $publicKey = $this->gatewaySetting('public_key', config('payment.gateways.chargily.public_key'));
        $secretKey = $this->gatewaySetting('secret_key', config('payment.gateways.chargily.secret_key'));
        if (! $publicKey || ! $secretKey) {
            return ValidationResult::invalid('Chargily gateway not configured.');
        }

        return ValidationResult::valid();
    }

    public static function requiredFields(): array
    {
        return [];
    }

    public function charge(array $data): PaymentResult
    {
        $this->checkoutService->setMethod($this->_cachedMethod ?? null);

        try {
            $checkoutData = $this->checkoutService->create([
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'DZD',
                'payment_id' => $data['payment_id'] ?? null,
                'user_id' => $data['user_id'] ?? null,
                'workspace_id' => $data['workspace_id'] ?? null,
                'description' => $data['description'] ?? 'Finance Manager Subscription',
                'success_url' => $data['success_url'] ?? route('chargily.back'),
                'failure_url' => $data['failure_url'] ?? route('chargily.back'),
                'webhook_url' => $data['webhook_url'] ?? ($this->gatewaySetting('webhook_url') ?? route('payment.webhook.chargily')),
            ]);
        } catch (\Throwable $e) {
            return PaymentResult::failed('Chargily charge failed: '.$e->getMessage());
        }

        return PaymentResult::success(
            message: 'Redirecting to Chargily...',
            transactionId: $checkoutData->id,
            reference: $checkoutData->id,
            metadata: [
                'id' => $checkoutData->id,
                'status' => $checkoutData->status,
                'amount' => $checkoutData->amount,
                'currency' => $checkoutData->currency,
                'checkout_url' => $checkoutData->url,
                'chargily_checkout_id' => $checkoutData->id,
            ],
            redirectUrl: $checkoutData->url,
        );
    }

    public function refund(Payment $payment, ?float $amount = null): PaymentResult
    {
        return PaymentResult::failed('Chargily refunds must be processed via the dashboard.');
    }

    public function verify(Payment $payment): PaymentResult
    {
        try {
            $checkout = $this->checkoutService->get($payment->transaction_id);
        } catch (\Throwable $e) {
            return PaymentResult::failed('Unable to verify payment with Chargily: '.$e->getMessage());
        }

        if (! $checkout) {
            return PaymentResult::failed('Checkout not found on Chargily.');
        }

        $status = $checkout->getStatus() ?? 'unknown';

        $metadata = [
            'id' => $checkout->getId() ?? null,
            'status' => $status,
            'amount' => $checkout->getAmount() ?? null,
            'currency' => $checkout->getCurrency() ?? null,
        ];

        $paymentMethod = $checkout->getPaymentMethod() ?? null;
        if ($paymentMethod) {
            $metadata['payment_method_type'] = $paymentMethod;
            $payment->updateQuietly(['payment_method_type' => strtolower($paymentMethod)]);
        } elseif ($payment->payment_method_type) {
            $metadata['payment_method_type'] = $payment->payment_method_type;
        }

        return PaymentResult::success(
            message: "Chargily verification: {$status}",
            transactionId: $payment->transaction_id,
            metadata: $metadata,
        );
    }

    public function isOnline(): bool
    {
        return true;
    }

    public function isOffline(): bool
    {
        return false;
    }

    public function supportedCurrencies(): array
    {
        return ['DZD'];
    }
}
