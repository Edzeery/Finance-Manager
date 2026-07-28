<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Services\AdminNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminNotificationController extends Controller
{
    public function __construct(
        private AdminNotificationService $notificationService,
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        if ($request->expectsJson() || $request->ajax()) {
            $locale = app()->getLocale();
            $notifications = $this->notificationService->getRecent(20)->map(fn ($n) => [
                'id' => $n->id,
                'title' => $n->{"title_{$locale}"} ?: $n->title_en,
                'message' => $n->{"message_{$locale}"} ?: $n->message_en,
                'type' => $n->type,
                'is_read' => $n->is_read,
                'created_at' => $n->created_at->toISOString(),
                'time' => $n->created_at->diffForHumans(),
            ]);

            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $this->notificationService->getUnreadCount(),
            ]);
        }

        $filter = $request->input('filter', 'all');
        $query = AdminNotification::latest();

        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'read') {
            $query->where('is_read', true);
        }

        $type = $request->input('type');
        if ($type && in_array($type, ['new_user', 'new_payment', 'subscription_activated', 'backup_completed', 'system_alert'])) {
            $query->byType($type);
        }

        $notifications = $query->paginate(20);

        return view('super-admin.notifications.index', compact('notifications', 'filter', 'type'));
    }

    public function show(AdminNotification $notification): View
    {
        if (! $notification->is_read) {
            $notification->markAsRead();
        }

        return view('super-admin.notifications.show', compact('notification'));
    }

    public function markRead(Request $request, AdminNotification $notification): JsonResponse|RedirectResponse
    {
        $notification->markAsRead();

        if (! $request->expectsJson() && ! $request->ajax()) {
            return redirect()->back()->with('success', __('notifications.marked_read'));
        }

        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request): JsonResponse|RedirectResponse
    {
        $this->notificationService->markAllAsRead();

        if (! $request->expectsJson() && ! $request->ajax()) {
            return redirect()->back()->with('success', __('notifications.all_marked_read'));
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request, AdminNotification $notification): JsonResponse|RedirectResponse
    {
        $notification->delete();

        if (! $request->expectsJson() && ! $request->ajax()) {
            return redirect()->back()->with('success', __('notifications.deleted'));
        }

        return response()->json(['success' => true]);
    }
}
