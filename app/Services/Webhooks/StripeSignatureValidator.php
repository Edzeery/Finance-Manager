<?php

namespace App\Services\Webhooks;

use App\Contracts\Webhooks\WebhookSignatureValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeSignatureValidator implements WebhookSignatureValidator
{
    public function provider(): string
    {
        return 'stripe';
    }

    public function validate(Request $request): bool
    {
        $signature = $request->header('stripe-signature');
        if (!$signature) {
            return $this->fallbackTokenCheck($request);
        }

        $secret = config('payment.gateways.stripe.webhook_secret');
        if (!$secret) {
            return $this->fallbackTokenCheck($request);
        }

        $payload = $request->getContent();
        $parts = explode(',', $signature);
        $computed = hash_hmac('sha256', $payload, $secret);

        foreach ($parts as $part) {
            $kv = explode('=', trim($part), 2);
            if (count($kv) === 2 && $kv[0] === 'v1' && hash_equals($computed, $kv[1])) {
                return true;
            }
        }

        return false;
    }

    private function fallbackTokenCheck(Request $request): bool
    {
        $secret = config('payment.webhook_secret');
        if (!$secret) {
            Log::warning('Stripe webhook: no webhook_secret configured');
            return false;
        }

        $token = $request->header('X-Webhook-Token');
        if (!$token || !hash_equals($secret, $token)) {
            Log::warning('Stripe webhook: invalid shared token');
            return false;
        }

        return true;
    }
}
