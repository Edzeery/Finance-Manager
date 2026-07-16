<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'storage' => $this->checkStorage(),
        ];

        $allPassed = collect($checks)->every(fn ($c) => $c['passed']);

        return response()->json([
            'status' => $allPassed ? 'healthy' : 'degraded',
            'checks' => $checks,
        ], $allPassed ? 200 : 503);
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();

            return ['passed' => true, 'message' => 'Connected'];
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

            return ['passed' => $exists, 'message' => $exists ? 'Writable' : 'Not writable'];
        } catch (\Exception $e) {
            return ['passed' => false, 'message' => $e->getMessage()];
        }
    }
}
