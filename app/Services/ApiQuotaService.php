<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Cache;

class ApiQuotaService
{
    public function getUsage(int $userId): array
    {
        return [
            'minute' => (int) Cache::get($this->key($userId, 'm', now()->format('YmdHi')), 0),
            'hour'   => (int) Cache::get($this->key($userId, 'h', now()->format('YmdH')), 0),
            'day'    => (int) Cache::get($this->key($userId, 'd', now()->format('Ymd')), 0),
        ];
    }

    public function increment(int $userId): void
    {
        $windows = [
            ['period' => 'm', 'window' => now()->format('YmdHi'), 'ttl' => 120],
            ['period' => 'h', 'window' => now()->format('YmdH'),  'ttl' => 7200],
            ['period' => 'd', 'window' => now()->format('Ymd'),   'ttl' => 172800],
        ];

        foreach ($windows as $w) {
            $key = $this->key($userId, $w['period'], $w['window']);
            Cache::add($key, 0, $w['ttl']);
            Cache::increment($key);
        }
    }

    public function getLimits(?SubscriptionPlan $plan): array
    {
        if (!$plan) {
            return ['minute' => 0, 'hour' => 0, 'day' => 0];
        }

        return [
            'minute' => (int) ($plan->getFeatureValue('api_requests_per_minute') ?? 0),
            'hour'   => (int) ($plan->getFeatureValue('api_requests_per_hour') ?? 0),
            'day'    => (int) ($plan->getFeatureValue('api_requests_per_day') ?? 0),
        ];
    }

    public function getResetTimes(): array
    {
        return [
            'minute' => now()->addMinute()->startOfMinute()->timestamp,
            'hour'   => now()->addHour()->startOfHour()->timestamp,
            'day'    => now()->addDay()->startOfDay()->timestamp,
        ];
    }

    private function key(int $userId, string $period, string $window): string
    {
        return "quota:{$userId}:{$period}:{$window}";
    }
}
