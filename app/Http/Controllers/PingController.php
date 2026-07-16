<?php

namespace App\Http\Controllers;

use App\Enums\OnlineStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PingController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $statusRecord = $user->statusRecord;

        if ($statusRecord) {
            $statusRecord->update([
                'last_activity_at' => now(),
                'online_status' => OnlineStatus::Online,
            ]);
        }

        return response()->json(['ok' => true]);
    }
}
