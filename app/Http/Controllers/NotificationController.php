<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use HasBreadcrumbs;

    public function index(Request $request): JsonResponse|\Illuminate\View\View
    {
        if ($request->expectsJson() || $request->ajax()) {
            $notifications = Notification::where('user_id', auth()->id())
                ->latest()
                ->take(20)
                ->get()
                ->map(fn($n) => [
                    'id' => $n->id,
                    'title' => $n->{'title_' . app()->getLocale()},
                    'message' => $n->{'message_' . app()->getLocale()},
                    'type' => $n->type,
                    'is_read' => $n->is_read,
                    'created_at' => $n->created_at->diffForHumans(),
                    'time' => $n->created_at->format('H:i'),
                ]);

            $unreadCount = Notification::where('user_id', auth()->id())
                ->unread()
                ->count();

            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
            ]);
        }

        $this->resetBreadcrumbs()
            ->homeBreadcrumb()
            ->addBreadcrumb(__('general.notifications'), route('notifications.index'));

        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('notifications.index', $this->withBreadcrumbs(compact('notifications')));
    }

    public function markRead(Notification $notification): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $notification);

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', __('notifications.marked_read'));
    }

    public function destroy(Notification $notification): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $this->authorize('delete', $notification);
        $notification->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', __('notifications.deleted'));
    }

    public function markAllRead(): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        Notification::where('user_id', auth()->id())
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', __('notifications.all_marked_read'));
    }
}
