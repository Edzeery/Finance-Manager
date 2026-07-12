<?php

namespace Tests\Unit\Services;

use App\Services\Payments\Noest\NoestService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NoestServiceTest extends TestCase
{
    private NoestService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NoestService(
            baseUrlOverride: 'https://app.noest-dz.com/api/public',
            tokenOverride: 'test_token',
            guidOverride: 'test_guid',
        );
    }

    public function test_create_order_success(): void
    {
        Http::fake([
            '*/create/order*' => Http::response([
                'success' => true,
                'data' => ['tracking' => 'ECS123456789'],
            ]),
        ]);

        $result = $this->service->createOrder([
            'reference' => 'REF-001',
            'client' => 'Ahmed',
            'phone' => '0550505050',
            'montant' => 3500,
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('ECS123456789', $result['data']['tracking']);
    }

    public function test_create_order_failure_throws_exception(): void
    {
        Http::fake([
            '*/create/order*' => Http::response([
                'success' => false,
                'message' => 'Validation failed',
            ], 422),
        ]);

        $this->expectException(RequestException::class);

        $this->service->createOrder([
            'reference' => 'REF-001',
        ]);
    }

    public function test_get_tracking_info(): void
    {
        Http::fake([
            '*/get/trackings/info' => Http::response([
                'success' => true,
                'data' => [
                    'tracking' => 'ECS123456789',
                    'status' => 'delivered',
                ],
            ]),
        ]);

        $result = $this->service->getTrackingInfo('ECS123456789');

        $this->assertTrue($result['success']);
        $this->assertEquals('delivered', $result['data']['status']);
    }

    public function test_sends_correct_headers(): void
    {
        Http::fake([
            '*/create/order*' => Http::response(['success' => true, 'data' => []]),
        ]);

        $this->service->createOrder(['reference' => 'T']);

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer test_token')
                && $request->hasHeader('Accept', 'application/json');
        });
    }

    public function test_includes_user_guid_as_query_param(): void
    {
        Http::fake([
            '*/create/order*' => Http::response(['success' => true, 'data' => []]),
        ]);

        $this->service->createOrder(['reference' => 'T']);

        Http::assertSent(function ($request) {
            $uri = $request->toPsrRequest()->getUri();
            parse_str($uri->getQuery(), $query);
            return $request->method() === 'POST'
                && ($query['user_guid'] ?? null) === 'test_guid'
                && !isset($request->data()['user_guid']);
        });
    }
}
