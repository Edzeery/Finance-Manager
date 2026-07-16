<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $casts = [
        'abilities' => 'json',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'deactivated_at' => 'datetime',
    ];

    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
        'plaintext_token',
        'deactivated_at',
    ];

    public static function findToken($token)
    {
        if (strpos($token, '|') === false) {
            return static::where('token', hash('sha256', $token))
                ->whereNull('deactivated_at')
                ->first();
        }

        [$id, $token] = explode('|', $token, 2);

        if ($instance = static::find($id)) {
            return $instance->deactivated_at === null && hash_equals($instance->token, hash('sha256', $token))
                ? $instance
                : null;
        }
    }
}
