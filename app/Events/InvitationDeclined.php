<?php

namespace App\Events;

use App\Models\Invitation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationDeclined
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Invitation $invitation,
    ) {}
}
