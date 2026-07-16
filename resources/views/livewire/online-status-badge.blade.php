<div wire:poll.30s="refreshStatus">
    <x-status-badge domain="online_status" :status="$isOnline ? 'online' : 'offline'" set="bi" />
</div>
