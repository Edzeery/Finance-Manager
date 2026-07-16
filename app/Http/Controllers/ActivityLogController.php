<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Models\ActivityLog;
use App\Models\Scopes\WorkspaceScope;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    use HasBreadcrumbs;

    public function index(Request $request)
    {
        $isSuperAdmin = $request->route()->named('super.admin.activity-log');

        if (! $isSuperAdmin) {
            $this->resetBreadcrumbs()->resourceBreadcrumbs('settings.activity_log', 'activity.logs', 'bi-clock-history');
        }

        $query = ActivityLog::with('user', 'workspace');

        if ($isSuperAdmin) {
            $query->withoutGlobalScope(WorkspaceScope::class);
        }

        if (! $isSuperAdmin && ! auth()->user()->hasPermission('activity-log.view')) {
            $query->where('user_id', auth()->id());
        }

        $query->latest();

        $action = $request->input('action', 'all');
        if ($action !== 'all') {
            $query->where('action', $action);
        }

        if ($request->filled('subject')) {
            $query->where('subject_type', 'like', '%'.$request->subject.'%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('subject_type', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage = min((int) $request->input('per_page', 10), config('finance.per_page_max', 100));
        $logs = $query->paginate($perPage);

        if ($isSuperAdmin) {
            $countAll = ActivityLog::withoutGlobalScope(WorkspaceScope::class)->count();
            $countCreated = ActivityLog::withoutGlobalScope(WorkspaceScope::class)->where('action', 'created')->count();
            $countUpdated = ActivityLog::withoutGlobalScope(WorkspaceScope::class)->where('action', 'updated')->count();
            $countDeleted = ActivityLog::withoutGlobalScope(WorkspaceScope::class)->where('action', 'deleted')->count();
            $countRestored = ActivityLog::withoutGlobalScope(WorkspaceScope::class)->where('action', 'restored')->count();

            return view('super-admin.activity-logs', compact('logs', 'countAll', 'countCreated', 'countUpdated', 'countDeleted', 'countRestored'));
        }

        $countBase = ActivityLog::with('user', 'workspace');
        if (! auth()->user()->hasPermission('activity-log.view')) {
            $countBase->where('user_id', auth()->id());
        }
        $countAll = (clone $countBase)->count();
        $countCreated = (clone $countBase)->where('action', 'created')->count();
        $countUpdated = (clone $countBase)->where('action', 'updated')->count();
        $countDeleted = (clone $countBase)->where('action', 'deleted')->count();
        $countRestored = (clone $countBase)->where('action', 'restored')->count();

        return view('activity_logs.index', $this->withBreadcrumbs(compact('logs', 'countAll', 'countCreated', 'countUpdated', 'countDeleted', 'countRestored')));
    }

    public function show(ActivityLog $log)
    {
        if ($log->user_id !== auth()->id() && ! auth()->user()->hasPermission('activity-log.view')) {
            abort(403);
        }

        return response()->json([
            'log' => [
                'id' => $log->id,
                'action' => $log->action,
                'action_label' => $log->action_label,
                'action_icon' => $log->action_icon,
                'action_color' => $log->action_color,
                'subject_name' => $log->subject_name,
                'subject_type' => $log->subject_type,
                'subject_id' => $log->subject_id,
                'description' => $log->description,
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'created_at' => $log->created_at->format('Y/m/d H:i:s'),
                'changes' => $log->changes_summary,
            ],
        ]);
    }
}
