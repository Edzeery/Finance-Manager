<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
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

        $query = Workspace::with('users');

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
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

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $perPage = min((int) $request->input('per_page', 15), config('finance.per_page_max', 100));
        $workspaces = $query->latest()->paginate($perPage);

        $types = Workspace::select('type')->distinct()->pluck('type');

        return view('super-admin.workspaces', $this->withBreadcrumbs(compact('workspaces', 'types')));
    }

    public function restore(int $id): RedirectResponse
    {
        $workspace = Workspace::onlyTrashed()->findOrFail($id);
        $workspace->restore();

        return redirect()->route('super.admin.workspaces.index')
            ->with('success', __('messages.workspace_restored'));
    }

    public function forceDelete(Workspace $workspace): RedirectResponse
    {
        $workspace->forceDelete();

        return redirect()->route('super.admin.workspaces.index')
            ->with('success', __('messages.workspace_deleted_permanently'));
    }
}
