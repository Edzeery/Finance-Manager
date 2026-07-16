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
            return response()->json([
                'notifications' => $this->notificationService->getRecent(20),
                'unread_count' => $this->notificationService->getUnreadCount(),
            ]);
        }

        $filter = $request->input('filter', 'all');
        $query = AdminNotification::latest();

        if ($filter === 'unread') {
            $query->unread();
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
            $this->notificationService->markAsRead($notification->id);
        }

        return view('super-admin.notifications.show', compact('notification'));
    }

    public function markRead(Request $request, AdminNotification $notification): JsonResponse|RedirectResponse
    {
        $this->notificationService->markAsRead($notification->id);

        if (! $request->expectsJson() && ! $request->ajax()) {
            return redirect()->back()->with('success', __('admin.mark_read'));
        }

        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request): JsonResponse|RedirectResponse
    {
        $this->notificationService->markAllAsRead();

        if (! $request->expectsJson() && ! $request->ajax()) {
            return redirect()->back()->with('success', __('admin.mark_all_read'));
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request, AdminNotification $notification): JsonResponse|RedirectResponse
    {
        $notification->delete();

        if (! $request->expectsJson() && ! $request->ajax()) {
            return redirect()->back()->with('success', __('admin.delete'));
        }

        return response()->json(['success' => true]);
    }
}
