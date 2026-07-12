<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

abstract class BaseCrudController extends Controller
{
    use AuthorizesRequests;
    use Concerns\HasBreadcrumbs;
    use Concerns\HasForceDelete;

    abstract protected function getModelClass(): string;
    abstract protected function getRoutePrefix(): string;
    abstract protected function getViewPrefix(): string;
    abstract protected function getLangPrefix(): string;
    abstract protected function getIcon(): string;

    protected function getRepository()
    {
        return app()->make($this->getRepositoryInterface());
    }

    abstract protected function getRepositoryInterface(): string;

    protected function beforeDestroy($model): void
    {
    }

    protected function afterDestroy($model): void
    {
    }

    protected function beforeRestore($model): void
    {
    }

    protected function afterRestore($model): void
    {
    }

    protected function getBulkDeleteRedirect(): string
    {
        return 'back';
    }

    protected function getBulkRestoreRedirect(): string
    {
        return 'back';
    }

    protected function getRedirectUrl(string $action = 'index', array $params = []): string
    {
        if ($action === 'back') {
            return 'back';
        }
        return route($this->getRoutePrefix() . '.' . $action, $params);
    }

    protected function buildTabs(array $tabConfig): array
    {
        $modelClass = $this->getModelClass();
        $baseQuery = $modelClass::query();
        $tabs = [];

        foreach ($tabConfig as $key => $config) {
            $query = clone $baseQuery;
            $scope = $config['scope'] ?? null;
            $scopeParams = $config['scope_params'] ?? [];

            if ($scope) {
                if (is_string($scope)) {
                    $query = $query->$scope(...(array)$scopeParams);
                } elseif (is_callable($scope)) {
                    $query = $scope($query);
                }
            }

            $tabs[$key] = [
                'label' => $config['label'],
                'count' => $query->count(),
            ];
        }

        return $tabs;
    }

    public function destroy($id)
    {
        $model = $this->getRepository()->findOrFail((int) $id);

        $this->authorize('delete', $model);

        $this->beforeDestroy($model);

        $this->getRepository()->delete($model);

        $this->afterDestroy($model);

        return redirect()->route($this->getRoutePrefix() . '.index')
            ->with('success', __("messages.{$this->getLangPrefix()}_deleted"));
    }

    public function restore($id)
    {
        $repo = $this->getRepository();
        $model = $repo->withTrashedFindOrFail($id);

        $this->beforeRestore($model);
        $this->authorize('restore', $model);
        $repo->restore($model);
        $this->afterRestore($model);

        return redirect()->route($this->getRoutePrefix() . '.index', ['tab' => 'trashed'])
            ->with('success', __("messages.{$this->getLangPrefix()}_restored"));
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        $modelClass = $this->getModelClass();
        $modelClass::withTrashed()->whereIn('id', $ids)->get()->each(
            fn($m) => $this->authorize('delete', $m)
        );
        $this->getRepository()->bulkDelete($ids);

        $redirect = $this->getBulkDeleteRedirect();

        return $redirect === 'back'
            ? redirect()->back()->with('success', __('messages.deleted'))
            : redirect()->route($redirect)->with('success', __('messages.deleted'));
    }

    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids', []);
        $modelClass = $this->getModelClass();
        $modelClass::withTrashed()->whereIn('id', $ids)->get()->each(
            fn($m) => $this->authorize('restore', $m)
        );
        $this->getRepository()->bulkRestore($ids);

        $redirect = $this->getBulkRestoreRedirect();

        return $redirect === 'back'
            ? redirect()->back()->with('success', __('messages.restored'))
            : redirect()->route($redirect, ['tab' => 'trashed'])->with('success', __('messages.restored'));
    }
}
