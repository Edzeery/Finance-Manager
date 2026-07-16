<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    use HasBreadcrumbs;

    public function index(Request $request)
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.workspaces'));

        $status = $request->input('status', 'all');
        $query = Workspace::with('users');

        if ($status === 'trashed') {
            $query->onlyTrashed();
        } elseif ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $perPage = min((int) $request->input('per_page', 15), config('finance.per_page_max', 100));
        $workspaces = $query->latest()->paginate($perPage);

        $types = Workspace::select('type')->distinct()->pluck('type');
        $countAll = Workspace::count();
        $countActive = Workspace::where('is_active', true)->count();
        $countInactive = Workspace::where('is_active', false)->count();
        $countTrashed = Workspace::onlyTrashed()->count();

        $typeSubTabs = collect(['' => ['label' => __('general.all')]])
            ->union($types->mapWithKeys(fn ($t) => [$t => ['label' => ucfirst($t)]]))
            ->toArray();

        return view('super-admin.workspaces', $this->withBreadcrumbs(compact(
            'workspaces', 'types', 'countAll', 'countActive', 'countInactive', 'countTrashed', 'typeSubTabs'
        )));
    }

    public function restore(int $id): RedirectResponse
    {
        $workspace = Workspace::onlyTrashed()->findOrFail($id);
        $workspace->restore();

        return redirect()->route('super.admin.workspaces.index')
            ->with('success', __('messages.workspace_restored'));
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $workspace = Workspace::withTrashed()->findOrFail($id);
        $workspace->forceDelete();

        return redirect()->route('super.admin.workspaces.index')
            ->with('success', __('messages.workspace_deleted_permanently'));
    }
}
