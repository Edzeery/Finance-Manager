<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class DashboardCacheObserver
{
    public function created(Model $model): void
    {
        $this->clearUserCache($model);
    }

    public function updated(Model $model): void
    {
        $this->clearUserCache($model);
    }

    public function deleted(Model $model): void
    {
        $this->clearUserCache($model);
    }

    public function restored(Model $model): void
    {
        $this->clearUserCache($model);
    }

    private function clearUserCache(Model $model): void
    {
        $userId = $model->getAttribute('user_id');
        $workspaceId = $model->getAttribute('workspace_id');

        if ($userId && $workspaceId) {
            Cache::forget("dashboard:kpi:{$userId}:{$workspaceId}");
            Cache::forget("chart:monthly:{$userId}:{$workspaceId}:6");
            Cache::forget("chart:category:{$userId}:{$workspaceId}:1");
            Cache::forget("chart:balance:{$userId}:{$workspaceId}:6");
        }
    }
}
