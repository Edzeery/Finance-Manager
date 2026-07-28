<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class EnvWriter
{
    private string $envPath;

    public function __construct(?string $envPath = null)
    {
        $this->envPath = $envPath ?? base_path('.env');
    }

    /**
     * Update multiple key-value pairs in the .env file.
     *
     * @param  array<string, string|null>  $updates  Key => value pairs (null to remove)
     * @return array<string, array{old: string|null, new: string|null}> Changed keys
     */
    public function update(array $updates): array
    {
        if (! file_exists($this->envPath)) {
            throw new \RuntimeException(".env file not found at: {$this->envPath}");
        }

        $originalContent = file_get_contents($this->envPath);
        if ($originalContent === false) {
            throw new \RuntimeException("Failed to read .env file: {$this->envPath}");
        }

        $content = $originalContent;
        $changes = [];

        foreach ($updates as $key => $value) {
            $oldValue = $this->getEnvValue($content, $key);
            $newEnvLine = $this->formatEnvLine($key, $value);

            if ($oldValue === $value) {
                continue;
            }

            $pattern = '/^'.preg_quote($key, '/').'\s*=\s*.*/m';

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $newEnvLine, $content);
            } else {
                $content = rtrim($content, "\n")."\n{$newEnvLine}\n";
            }

            $changes[$key] = ['old' => $oldValue, 'new' => $value];
        }

        if ($changes !== []) {
            $written = file_put_contents($this->envPath, $content);
            if ($written === false) {
                throw new \RuntimeException("Failed to write .env file: {$this->envPath}");
            }

            Log::info('Environment file updated', [
                'path' => $this->envPath,
                'changes' => array_keys($changes),
            ]);
        }

        return $changes;
    }

    /**
     * Read a single value from the .env content string.
     */
    private function getEnvValue(string $content, string $key): ?string
    {
        $pattern = '/^'.preg_quote($key, '/').'\s*=\s*(.*?)$/m';

        if (! preg_match($pattern, $content, $matches)) {
            return null;
        }

        $value = trim($matches[1]);

        if (($value[0] ?? '') === '"' && substr($value, -1) === '"') {
            return substr($value, 1, -1);
        }

        if (($value[0] ?? '') === "'" && substr($value, -1) === "'") {
            return substr($value, 1, -1);
        }

        return $value;
    }

    /**
     * Format a KEY=value line for .env.
     */
    private function formatEnvLine(string $key, ?string $value): string
    {
        if ($value === null) {
            return "{$key}=";
        }

        if (preg_match('/\s/', $value) || str_contains($value, '#') || str_contains($value, '"') || $value === '') {
            $escaped = str_replace('"', '\\"', $value);

            return "{$key}=\"{$escaped}\"";
        }

        return "{$key}={$value}";
    }
}
