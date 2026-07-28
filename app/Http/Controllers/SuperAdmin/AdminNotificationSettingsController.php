<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotificationPreference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminNotificationSettingsController extends Controller
{
    public function index(): View
    {
        $preferences = auth()->user()->adminNotificationPreferences->keyBy('type');

        return view('super-admin.notifications.settings', compact('preferences'));
    }

    public function update(Request $request): RedirectResponse
    {
        $userId = auth()->id();
        $preferences = $request->input('preferences', []);

        foreach (AdminNotificationPreference::getAllTypes() as $type) {
            $typePrefs = $preferences[$type] ?? [];

            AdminNotificationPreference::updateOrCreate(
                ['user_id' => $userId, 'type' => $type],
                [
                    'in_app_enabled' => isset($typePrefs['in_app_enabled']),
                    'email_enabled' => isset($typePrefs['email_enabled']),
                ]
            );
        }

        return back()->with('success', __('notifications.preferences_saved'));
    }
}
