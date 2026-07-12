<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DateFilterService
{
    const MAX_CUSTOM_DAYS = 90;

    const PERIODS = [
        'all_time' => ['label_key' => 'filters.all_time', 'days' => null],
        'this_month' => ['label_key' => 'filters.this_month', 'days' => null],
        'last_month' => ['label_key' => 'filters.last_month', 'days' => null],
        'last_7_days' => ['label_key' => 'filters.last_7_days', 'days' => 7],
        'yesterday' => ['label_key' => 'filters.yesterday', 'days' => 1],
        'custom' => ['label_key' => 'filters.custom', 'days' => null],
    ];

    public function getPeriods(): Collection
    {
        return collect(self::PERIODS);
    }

    public function resolveDateRange(?string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        $period = $period ?? 'all_time';
        $now = now();

        return match ($period) {
            'yesterday' => ['start' => $now->copy()->subDay()->startOfDay(), 'end' => $now->copy()->subDay()->endOfDay()],
            'last_7_days' => ['start' => $now->copy()->subDays(7)->startOfDay(), 'end' => $now->copy()->endOfDay()],
            'this_month' => ['start' => $now->copy()->startOfMonth(), 'end' => $now->copy()->endOfMonth()],
            'last_month' => ['start' => $now->copy()->subMonth()->startOfMonth(), 'end' => $now->copy()->subMonth()->endOfMonth()],
            'all_time' => ['start' => null, 'end' => null],
            'custom' => $this->resolveCustomRange($startDate, $endDate),
            default => ['start' => null, 'end' => null],
        };
    }

    public function resolveCustomRange(?string $startDate, ?string $endDate): array
    {
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfDay();
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : $end->copy()->subMonth();

        if ($start->gt($end)) {
            throw new \App\Exceptions\DateFilterException(__('filters.start_after_end'));
        }

        if ($start->diffInDays($end) > self::MAX_CUSTOM_DAYS) {
            throw new \App\Exceptions\DateFilterException(__('filters.max_period_exceeded', ['days' => self::MAX_CUSTOM_DAYS]));
        }

        return ['start' => $start, 'end' => $end];
    }

    public function cacheKey(string $prefix, ?string $period, ?string $startDate = null, ?string $endDate = null): string
    {
        $period = $period ?? 'all_time';
        $wid = config('app.current_workspace');
        $wid = $wid instanceof \App\Models\Workspace ? $wid->id : $wid;
        $v = $this->cacheVersion();

        if ($period === 'custom' && $startDate && $endDate) {
            return "{$prefix}:{$v}:" . auth()->id() . ":{$wid}:{$startDate}:{$endDate}";
        }

        $suffix = match ($period) {
            'yesterday' => now()->format('Y-m-d'),
            'last_7_days' => now()->format('Y-m-d'),
            'this_month' => now()->format('Y-m'),
            'last_month' => now()->subMonth()->format('Y-m'),
            'all_time' => 'all',
            default => now()->format('Y-m-d'),
        };

        return "{$prefix}:{$v}:" . auth()->id() . ":{$wid}:{$period}:{$suffix}";
    }

    public function cacheVersion(?int $userId = null, ?int $workspaceId = null): int
    {
        $wid = $workspaceId ?? (config('app.current_workspace') instanceof \App\Models\Workspace
            ? config('app.current_workspace')->id
            : config('app.current_workspace'));
        $uid = $userId ?? auth()->id();
        return Cache::remember("dash:v:{$uid}:{$wid}", 86400 * 30, fn() => 1);
    }

    public function bumpCacheVersion(?int $userId = null, ?int $workspaceId = null): void
    {
        $wid = $workspaceId ?? (config('app.current_workspace') instanceof \App\Models\Workspace
            ? config('app.current_workspace')->id
            : config('app.current_workspace'));
        $uid = $userId ?? auth()->id();
        Cache::increment("dash:v:{$uid}:{$wid}");
    }

    public function monthsInRange(?Carbon $start, ?Carbon $end): int
    {
        if (!$start || !$end) return 12;
        return max(1, $start->diffInMonths($end) + 1);
    }

    public function monthLabels(?Carbon $start, ?Carbon $end): array
    {
        $months = $this->monthsInRange($start, $end);
        $labels = [];
        $endDate = $end ?? now();
        for ($i = $months - 1; $i >= 0; $i--) {
            $labels[] = $endDate->copy()->subMonths($i)->format('M Y');
        }
        return $labels;
    }
}
