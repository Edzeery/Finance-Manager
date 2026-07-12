<?php

namespace App\Models\Concerns;

use App\Models\Scopes\WorkspaceScope;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToWorkspace
{
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function scopeWithoutWorkspace($query)
    {
        return $query->withoutGlobalScope(WorkspaceScope::class);
    }

    public static function bootBelongsToWorkspace(): void
    {
        static::creating(function (Model $model) {
            if ($model->workspace_id === null) {
                if (property_exists($model, 'allowsNullWorkspace') && $model->allowsNullWorkspace) {
                    return;
                }

                $workspace = config('app.current_workspace');

                if (!$workspace && auth()->check()) {
                    $workspace = auth()->user()->currentWorkspace;
                }

                if ($workspace) {
                    $model->workspace_id = $workspace instanceof Model ? $workspace->id : $workspace;
                }
            }
        });
    }
}
