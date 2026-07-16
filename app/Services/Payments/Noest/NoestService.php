<?php

namespace App\Services\Payments\Noest;

use App\Services\Payments\Concerns\HasGatewaySettings;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class NoestService
{
    use HasGatewaySettings;

    public function __construct(
        private readonly ?string $baseUrlOverride = null,
        private readonly ?string $tokenOverride = null,
        private readonly ?string $guidOverride = null,
    ) {}

    public function name(): string
    {
        return 'noest';
    }

    public function baseUrl(): string
    {
        return $this->baseUrlOverride
            ?? $this->gatewaySetting('base_url', config('payment.gateways.noest.base_url', 'https://app.noest-dz.com/api/public'));
    }

    public function token(): string
    {
        return $this->tokenOverride
            ?? $this->gatewaySetting('api_token', config('payment.gateways.noest.api_token', ''));
    }

    public function guid(): string
    {
        return $this->guidOverride
            ?? $this->gatewaySetting('user_guid', config('payment.gateways.noest.user_guid', ''));
    }

    public function createOrder(array $data): array
    {
        return $this->post('/create/order', $data, ['user_guid' => $this->guid()]);
    }

    public function getTrackingInfo(string $tracking): array
    {
        return $this->post('/get/trackings/info', ['tracking' => $tracking]);
    }

    public function getWilayas(): array
    {
        return $this->get('/get/wilayas');
    }

    public function getDesks(): array
    {
        $result = $this->get('/desks');

        return $result;
    }

    public function getOrders(): array
    {
        return $this->get('/get/orders', ['user_guid' => $this->guid()]);
    }

    public function validateOrder(string $tracking): array
    {
        return $this->post('/valid/order', [
            'user_guid' => $this->guid(),
            'tracking' => $tracking,
        ]);
    }

    public function validateOrders(array $trackings): array
    {
        return $this->post('/valid/orders', [
            'user_guid' => $this->guid(),
            'tracking' => $trackings,
        ]);
    }

    public function deleteOrder(string $tracking): array
    {
        return $this->post('/delete/order', [
            'user_guid' => $this->guid(),
            'tracking' => $tracking,
        ]);
    }

    public function updateOrderBeforeExpedition(string $tracking, array $data): array
    {
        return $this->post('/update/order/before/expedition', array_merge([
            'user_guid' => $this->guid(),
            'tracking' => $tracking,
        ], $data));
    }

    public function updateOrderInProgress(string $tracking, array $data): array
    {
        return $this->post('/update/order', array_merge([
            'user_guid' => $this->guid(),
            'tracking' => $tracking,
        ], $data));
    }

    public function getOrderLabel(string $tracking): string
    {
        $url = rtrim($this->baseUrl(), '/').'/get/order/label?tracking='.urlencode($tracking);

        $response = Http::withToken($this->token())
            ->withHeaders(['Accept' => 'application/json'])
            ->get($url);

        if ($response->failed()) {
            throw new \RuntimeException($this->extractErrorMessage($response));
        }

        return $response->body();
    }

    public function getCommunes(string $wilayaId): array
    {
        return $this->get('/get/communes/'.$wilayaId);
    }

    public function testConnection(): array
    {
        $endpoint = '/get/wilayas';

        // يحاول بدون token أولاً
        $attempts = [];
        $attempts[] = $this->tryGetWithoutToken($endpoint);

        // إذا فشل، جرب مع token
        if (! ($attempts[0]['successful'] ?? false)) {
            $attempts[] = $this->tryGetWithToken($endpoint);
        }

        $last = end($attempts);
        $body = $last['body'] ?? [];

        return [
            'attempts' => $attempts,
            'status' => $last['status'] ?? 0,
            'successful' => $last['successful'] ?? false,
            'url' => $last['url'] ?? '',
            'has_token' => ! empty($this->token()),
            'token_prefix' => substr($this->token(), 0, 10).'...',
            'response_keys' => array_keys($body),
            'has_data_key' => isset($body['data']),
            'is_array' => is_array($body),
            'count' => is_array($body['data'] ?? null) ? count($body['data']) : (is_array($body) ? count($body) : 0),
            'sample' => json_encode(
                is_array($body['data'][0] ?? null) ? $body['data'][0] : ($body[0] ?? null),
                JSON_UNESCAPED_UNICODE,
            ),
        ];
    }

    private function tryGetWithoutToken(string $endpoint): array
    {
        try {
            $response = Http::withHeaders(['Accept' => 'application/json'])
                ->get(rtrim($this->baseUrl(), '/').$endpoint);

            return [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'url' => rtrim($this->baseUrl(), '/').$endpoint,
                'auth' => 'none',
                'body' => $response->json() ?? [],
            ];
        } catch (\Exception $e) {
            return [
                'status' => 0,
                'successful' => false,
                'url' => rtrim($this->baseUrl(), '/').$endpoint,
                'auth' => 'none',
                'error' => $e->getMessage(),
                'body' => [],
            ];
        }
    }

    private function tryGetWithToken(string $endpoint): array
    {
        try {
            $response = Http::withToken($this->token())
                ->withHeaders(['Accept' => 'application/json'])
                ->get(rtrim($this->baseUrl(), '/').$endpoint);

            return [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'url' => rtrim($this->baseUrl(), '/').$endpoint,
                'auth' => 'token',
                'body' => $response->json() ?? [],
            ];
        } catch (\Exception $e) {
            return [
                'status' => 0,
                'successful' => false,
                'url' => rtrim($this->baseUrl(), '/').$endpoint,
                'auth' => 'token',
                'error' => $e->getMessage(),
                'body' => [],
            ];
        }
    }

    private function extractErrorMessage(Response $response): string
    {
        $body = $response->json();

        return $body['error']
            ?? $body['message']
            ?? $body['errors'][0] ?? null
            ?? $response->body()
            ?? $response->reason();
    }

    private function post(string $endpoint, array $data, array $query = []): array
    {
        $url = rtrim($this->baseUrl(), '/').$endpoint;

        if ($query) {
            $url .= '?'.http_build_query($query);
        }

        $response = Http::withToken($this->token())
            ->withHeaders(['Accept' => 'application/json'])
            ->post($url, $data);

        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $response->json() ?? [];
    }

    private function get(string $endpoint, array $query = []): array
    {
        $url = rtrim($this->baseUrl(), '/').$endpoint;

        if ($query) {
            $url .= '?'.http_build_query($query);
        }

        $response = Http::withToken($this->token())
            ->withHeaders(['Accept' => 'application/json'])
            ->get($url);

        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $response->json() ?? [];
    }
}
