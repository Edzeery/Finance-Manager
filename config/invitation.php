<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Invitation Expiry
    |--------------------------------------------------------------------------
    |
    | Number of days after which a pending invitation expires.
    |
    */
    'expiry_days' => env('INVITATION_EXPIRY_DAYS', 7),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Maximum number of invitations a user can send per minute.
    |
    */
    'rate_limit_per_minute' => env('INVITATION_RATE_LIMIT', 10),
];
