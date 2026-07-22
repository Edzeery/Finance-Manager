<?php

namespace App\Observers;

use App\Services\DateFilterService;
use Illuminate\Database\Eloquent\Model;

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
            app(DateFilterService::class)->bumpCacheVersion($userId, (int) $workspaceId);
        }
    }
}
