<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Bus\Dispatchable;

class LogActivity
{
    use Dispatchable;

    public function __construct(
        private ?int $userId,
        private string $action,
        private string $subjectType,
        private int $subjectId,
        private ?string $description,
        private array $properties,
        private ?string $ipAddress = null,
        private ?string $userAgent = null,
    ) {}

    public function handle(): void
    {
        $workspaceId = config('app.current_workspace')?->id
            ?? ($this->userId ? User::find($this->userId)?->current_workspace_id : null);

        ActivityLog::create([
            'user_id' => $this->userId,
            'workspace_id' => $workspaceId,
            'action' => $this->action,
            'subject_type' => $this->subjectType,
            'subject_id' => $this->subjectId,
            'description' => $this->description,
            'properties' => $this->properties,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
        ]);
    }
}
