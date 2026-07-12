<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Services\AdminNotificationService;
use Illuminate\Http\JsonResponse;
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

        $notifications = AdminNotification::latest()->paginate(20);
        return view('super-admin.notifications.index', compact('notifications'));
    }

    public function markRead(AdminNotification $notification): JsonResponse
    {
        $this->notificationService->markAsRead($notification->id);
        return response()->json(['success' => true]);
    }

    public function markAllRead(): JsonResponse
    {
        $this->notificationService->markAllAsRead();
        return response()->json(['success' => true]);
    }

    public function destroy(AdminNotification $notification): JsonResponse
    {
        $notification->delete();
        return response()->json(['success' => true]);
    }
}
