<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CheckHealth extends Command
{
    protected $signature = 'finance:health-check';

    protected $description = 'Run system health checks';

    public function handle(): int
    {
        $checks = [
            'Database' => $this->checkDatabase(),
            'Storage' => $this->checkStorage(),
        ];

        $allPassed = true;
        foreach ($checks as $name => $result) {
            $status = $result['passed'] ? '<info>PASS</info>' : '<error>FAIL</error>';
            $this->line("{$name}: {$status}");
            if (! $result['passed'] && isset($result['message'])) {
                $this->warn("  └─ {$result['message']}");
                $allPassed = false;
            }
        }

        return $allPassed ? Command::SUCCESS : Command::FAILURE;
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();

            return ['passed' => true];
        } catch (\Exception $e) {
            return ['passed' => false, 'message' => $e->getMessage()];
        }
    }

    private function checkStorage(): array
    {
        try {
            $testFile = '.health-'.uniqid();
            Storage::disk('local')->put($testFile, 'ok');
            $exists = Storage::disk('local')->exists($testFile);
            Storage::disk('local')->delete($testFile);

            return ['passed' => $exists];
        } catch (\Exception $e) {
            return ['passed' => false, 'message' => $e->getMessage()];
        }
    }
}
