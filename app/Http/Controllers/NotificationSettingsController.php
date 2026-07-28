<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Models\NotificationPreference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationSettingsController extends Controller
{
    use HasBreadcrumbs;

    public function index(): View
    {
        $this->resetBreadcrumbs()
            ->homeBreadcrumb()
            ->addBreadcrumb(__('general.notifications'), route('notifications.index'))
            ->addBreadcrumb(__('notifications.preferences'), route('notifications.settings'));

        $user = auth()->user();
        $preferences = $user->notificationPreferences->keyBy('type');

        return view('notifications.settings', $this->withBreadcrumbs(compact('preferences')));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $preferences = $request->input('preferences', []);

        foreach (NotificationPreference::getAllTypes() as $type) {
            $typePrefs = $preferences[$type] ?? [];

            NotificationPreference::updateOrCreate(
                ['user_id' => $user->id, 'type' => $type],
                [
                    'in_app_enabled' => isset($typePrefs['in_app_enabled']),
                    'email_enabled' => isset($typePrefs['email_enabled']),
                ]
            );
        }

        return back()->with('success', __('notifications.preferences_saved'));
    }
}
