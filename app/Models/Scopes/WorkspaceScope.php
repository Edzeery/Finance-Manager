<?php

namespace App\Models\Scopes;

use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class WorkspaceScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $workspace = config('app.current_workspace');

        if ($workspace === null) {
            return;
        }

        $workspaceId = $workspace instanceof Model ? $workspace->id : $workspace;

        if (!is_int($workspaceId) && !is_string($workspaceId)) {
            return;
        }

        if ($model instanceof IncomeCategory || $model instanceof ExpenseCategory || $model instanceof Notification) {
            $builder->where(function (Builder $query) use ($workspaceId) {
                $query->where('workspace_id', $workspaceId)
                      ->orWhereNull('workspace_id');
            });
        } else {
            $builder->where('workspace_id', $workspaceId);
        }
    }
}
