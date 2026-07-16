<?php

namespace App\Livewire;

use App\Enums\OnlineStatus;
use App\Models\User;
use Livewire\Component;

class OnlineStatusBadge extends Component
{
    public int $userId;

    public bool $isOnline = false;

    public function mount(int $userId): void
    {
        $this->userId = $userId;
        $this->refreshStatus();
    }

    public function refreshStatus(): void
    {
        $user = User::find($this->userId);

        $this->isOnline = $user?->statusRecord
            && $user->statusRecord->online_status === OnlineStatus::Online
            && $user->statusRecord->last_activity_at
            && $user->statusRecord->last_activity_at->diffInMinutes(now()) < 15;
    }

    public function render()
    {
        return view('livewire.online-status-badge');
    }
}
