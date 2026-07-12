<?php

namespace App\Enums;

enum InvitationStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Declined = 'declined';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function isActive(): bool
    {
        return $this === self::Pending;
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Accepted, self::Declined, self::Expired, self::Cancelled]);
    }

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('workspace.invitation_pending'),
            self::Accepted => __('workspace.invitation_accepted'),
            self::Declined => __('workspace.invitation_declined'),
            self::Expired => __('workspace.invitation_expired'),
            self::Cancelled => __('workspace.invitation_cancelled'),
        };
    }
}
