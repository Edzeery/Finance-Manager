<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;

class NotificationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('notification.view');
    }

    public function view(User $user, Notification $notification): bool
    {
        return $notification->user_id === $user->id
            || $user->hasPermission('platform-notification.view', 'platform');
    }

    public function update(User $user, Notification $notification): bool
    {
        return $notification->user_id === $user->id
            || $user->hasPermission('platform-notification.manage', 'platform');
    }

    public function delete(User $user, Notification $notification): bool
    {
        return $notification->user_id === $user->id
            || $user->hasPermission('platform-notification.manage', 'platform');
    }
}
