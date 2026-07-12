<?php

namespace App\Services\Webhooks;

use App\Contracts\Webhooks\WebhookSignatureValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WiseSignatureValidator implements WebhookSignatureValidator
{
    public function provider(): string
    {
        return 'wise';
    }

    public function validate(Request $request): bool
    {
        $secret = config('payment.gateways.wise.webhook_secret');
        if ($secret) {
            $token = $request->header('X-Webhook-Token');
            if ($token) {
                $payload = $request->getContent();
                $computed = hash_hmac('sha256', $payload, $secret);
                if (hash_equals($computed, $token)) {
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
            Log::warning('Wise webhook: no webhook_secret configured');
            return false;
        }

        $token = $request->header('X-Webhook-Token');
        if (!$token || !hash_equals($secret, $token)) {
            Log::warning('Wise webhook: invalid shared token');
            return false;
        }

        return true;
    }
}
