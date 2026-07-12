<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait HasForceDelete
{
    public function forceDelete($id)
    {
        $modelClass = $this->model;
        $model = $modelClass::withTrashed()->findOrFail($id);

        $this->authorize('forceDelete', $model);

        $model->forceDelete();

        return redirect()->back()->with('success', __('messages.permanently_deleted'));
    }

    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        $modelClass = $this->model;
        $models = $modelClass::withTrashed()->whereIn('id', $ids)->get();

        foreach ($models as $model) {
            $this->authorize('forceDelete', $model);
            $model->forceDelete();
        }

        return redirect()->back()->with('success', __('messages.permanently_deleted'));
    }
}
