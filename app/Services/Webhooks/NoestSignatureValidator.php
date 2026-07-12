<?php

namespace App\Services\Webhooks;

use App\Contracts\Webhooks\WebhookSignatureValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NoestSignatureValidator implements WebhookSignatureValidator
{
    public function provider(): string
    {
        return 'noest';
    }

    public function validate(Request $request): bool
    {
        $secret = config('payment.gateways.noest.webhook_secret');
        if ($secret) {
            $signature = $request->header('X-Noest-Signature') ?? $request->header('X-Webhook-Signature');
            if ($signature) {
                $payload = $request->getContent();
                $computed = hash_hmac('sha256', $payload, $secret);
                if (hash_equals($computed, $signature)) {
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
            Log::warning('Noest webhook: no webhook_secret configured');
            return false;
        }

        $token = $request->header('X-Webhook-Token');
        if (!$token || !hash_equals($secret, $token)) {
            Log::warning('Noest webhook: invalid shared token');
            return false;
        }

        return true;
    }
}
