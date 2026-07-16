<?php

namespace App\Enums;

use Edzeery\MyStatusKit\Facades\Status;

enum OnlineStatus: string
{
    case Online = 'online';
    case Offline = 'offline';

    public function label(): string
    {
        return __('status-kit::statuses.online_status.'.$this->value);
    }

    public function color(): string
    {
        return Status::for('online_status', $this->value)->color();
    }

    public function badge(?string $set = null): string
    {
        return Status::for('online_status', $this->value)->badge($set);
    }

    public function icon(?string $set = null): string
    {
        return Status::for('online_status', $this->value)->icon($set);
    }
}
