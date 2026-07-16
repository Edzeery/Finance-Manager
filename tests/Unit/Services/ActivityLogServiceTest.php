<?php

namespace Tests\Unit\Services;

use App\Services\ActivityLogService;
use PHPUnit\Framework\TestCase;

class ActivityLogServiceTest extends TestCase
{
    private ActivityLogService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ActivityLogService;
    }

    public function test_filter_sensitive_data_removes_account_number(): void
    {
        $data = ['amount' => 1000, 'account_number' => '123456789', 'notes' => 'test'];
        $filtered = $this->service->filterSensitiveData($data);

        $this->assertArrayHasKey('amount', $filtered);
        $this->assertArrayNotHasKey('account_number', $filtered);
        $this->assertArrayNotHasKey('notes', $filtered);
    }

    public function test_filter_sensitive_data_handles_nested_old_new(): void
    {
        $data = [
            'old' => ['amount' => 500, 'account_number' => 'secret'],
            'new' => ['amount' => 1000, 'account_number' => 'new-secret'],
        ];
        $filtered = $this->service->filterSensitiveData($data);

        $this->assertArrayNotHasKey('account_number', $filtered['old']);
        $this->assertArrayNotHasKey('account_number', $filtered['new']);
        $this->assertArrayHasKey('amount', $filtered['old']);
    }

    public function test_remove_sensitive_keys_removes_all_sensitive(): void
    {
        $data = ['account_number' => '123', 'bank_name' => 'Bank', 'notes' => 'test'];
        $result = $this->service->removeSensitiveKeys($data);

        $this->assertEmpty($result);
    }
}
