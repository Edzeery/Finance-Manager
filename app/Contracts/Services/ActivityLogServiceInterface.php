<?php

namespace App\Contracts\Services;

interface ActivityLogServiceInterface
{
    public function log(int $userId, string $action, object $subject, ?string $description = null, array $properties = []): void;
    public function filterSensitiveData(array $data): array;
    public function removeSensitiveKeys(array $data): array;
}
