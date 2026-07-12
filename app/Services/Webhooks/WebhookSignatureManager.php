<?php

namespace App\Services\Webhooks;

use App\Contracts\Webhooks\WebhookSignatureValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookSignatureManager
{
    /** @var array<string, WebhookSignatureValidator> */
    private array $validators = [];

    public function register(WebhookSignatureValidator $validator): void
    {
        $this->validators[$validator->provider()] = $validator;
    }

    public function validate(string $provider, Request $request): bool
    {
        $validator = $this->validators[$provider] ?? null;

        if (!$validator) {
            Log::warning("Webhook: no signature validator registered for provider '{$provider}'");
            return $this->fallbackTokenCheck($request);
        }

        return $validator->validate($request);
    }

    private function fallbackTokenCheck(Request $request): bool
    {
        $secret = config('payment.webhook_secret');
        if (!$secret) {
            Log::warning('Webhook: shared webhook_secret not configured');
            return false;
        }

        $token = $request->header('X-Webhook-Token');
        if (!$token) {
            Log::warning('Webhook: missing X-Webhook-Token in fallback check');
            return false;
        }

        return hash_equals($secret, $token);
    }
}
