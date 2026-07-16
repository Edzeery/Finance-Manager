<?php

namespace App\Services\Payments\Chargily;

use App\Services\Payments\Chargily\Exceptions\ChargilyException;
use Illuminate\Support\Facades\Request;

class ChargilySignatureValidator
{
    public function validate(): array
    {
        $rawBody = Request::getContent();
        $signature = Request::header('Signature');
        $secret = ChargilyClient::setting('secret_key');

        if (! $signature) {
            throw ChargilyException::missingSignature();
        }

        if (! $secret) {
            throw ChargilyException::configuration();
        }

        $computedSignature = hash_hmac('sha256', $rawBody, $secret);

        if (! hash_equals($signature, $computedSignature)) {
            throw ChargilyException::invalidSignature();
        }

        $payload = json_decode($rawBody, true);
        if (! $payload || ! isset($payload['type'])) {
            throw ChargilyException::unhandledEvent('Invalid payload');
        }

        try {
            $webhookElement = ChargilyClient::make()->webhook()->newElement($payload);
        } catch (\RuntimeException $e) {
            throw ChargilyException::configuration($e->getMessage());
        }

        return [
            'webhook_element' => $webhookElement,
            'raw_payload' => $rawBody,
        ];
    }
}
