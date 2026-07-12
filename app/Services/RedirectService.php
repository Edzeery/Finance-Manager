<?php

namespace App\Services;

use App\Models\User;

class RedirectService
{
    public function getHomeRoute(User $user): string
    {
        $roles = config('auth-home.roles', []);
        $default = config('auth-home.default', 'dashboard');

        foreach ($roles as $role => $route) {
            if ($user->hasRole($role)) {
                return $route;
            }
        }

        return $default;
    }

    public function getHomeUrl(User $user): string
    {
        return route($this->getHomeRoute($user), absolute: false);
    }

    public function getIntendedUrl(User $user): string
    {
        return session()->pull('url.intended', $this->getHomeUrl($user));
    }
}
