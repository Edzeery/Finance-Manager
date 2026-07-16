<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChartDataService;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
        private ChartDataService $chartDataService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $kpis = $this->dashboardService->getKpiData();
        $chart = $this->chartDataService->monthlyIncomeExpense();

        return response()->json([
            'kpis' => $kpis,
            'chart' => $chart,
        ]);
    }
}
