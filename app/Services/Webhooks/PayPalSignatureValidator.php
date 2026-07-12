<?php

namespace App\Services\Webhooks;

use App\Contracts\Webhooks\WebhookSignatureValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayPalSignatureValidator implements WebhookSignatureValidator
{
    public function provider(): string
    {
        return 'paypal';
    }

    public function validate(Request $request): bool
    {
        $secret = config('payment.gateways.paypal.webhook_secret');
        if ($secret) {
            $receivedSignature = $request->header('paypal-transmission-sig')
                ?? $request->header('PAYPAL-TRANSMISSION-SIG');
            if ($receivedSignature) {
                $payload = $request->getContent();
                $computed = hash_hmac('sha256', $payload, $secret);
                if (hash_equals($computed, $receivedSignature)) {
                    return true;
                }
            }
        }

        return $this->fallbackTokenCheck($request);
    }

    private function fallbackTokenCheck(Request $request): bool
    {
        $secret = config('payment.webhook_secret');
        if (!$secret) {
            Log::warning('PayPal webhook: no webhook_secret configured');
            return false;
        }

        $token = $request->header('X-Webhook-Token');
        if (!$token || !hash_equals($secret, $token)) {
            Log::warning('PayPal webhook: invalid shared token');
            return false;
        }

        return true;
    }
}
