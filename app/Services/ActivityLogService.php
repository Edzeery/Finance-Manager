<?php

namespace App\Services;

use App\Contracts\Services\ActivityLogServiceInterface;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request as RequestFacade;

class ActivityLogService implements ActivityLogServiceInterface
{
    private const SENSITIVE_KEYS = ['account_number', 'bank_name', 'notes'];

    public function log(int $userId, string $action, object $subject, ?string $description = null, array $properties = []): void
    {
        $workspaceId = config('app.current_workspace')?->id
            ?? (auth()->check() ? auth()->user()->current_workspace_id : null)
            ?? ($subject->workspace_id ?? null);

        ActivityLog::create([
            'user_id' => $userId,
            'workspace_id' => $workspaceId,
            'action' => $action,
            'subject_type' => get_class($subject),
            'subject_id' => $subject->id,
            'description' => $description,
            'properties' => $this->filterSensitiveData($properties),
            'ip_address' => RequestFacade::ip(),
            'user_agent' => RequestFacade::userAgent(),
        ]);
    }

    public static function logWithRequest(int $userId, string $action, object $subject, ?string $description = null, array $properties = []): void
    {
        $service = app(self::class);
        $service->log($userId, $action, $subject, $description, $properties);
    }

    public function filterSensitiveData(array $data): array
    {
        if (isset($data['old']) && is_array($data['old'])) {
            $data['old'] = $this->removeSensitiveKeys($data['old']);
        }
        if (isset($data['new']) && is_array($data['new'])) {
            $data['new'] = $this->removeSensitiveKeys($data['new']);
        }
        return $this->removeSensitiveKeys($data);
    }

    public function removeSensitiveKeys(array $data): array
    {
        return array_diff_key($data, array_flip(self::SENSITIVE_KEYS));
    }
}
