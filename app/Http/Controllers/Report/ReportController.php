<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use HasBreadcrumbs;

    public function index()
    {
        $this->resetBreadcrumbs()->resourceBreadcrumbs('general.report', 'report.index', 'bi-file-earmark-bar-graph-fill');

        return view('report.index', $this->withBreadcrumbs([]));
    }

    public function monthly(Request $request, ReportService $reportService)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $report = $reportService->monthlyReport(year: $year, month: $month);

        return view('report.monthly', compact('report', 'year', 'month'));
    }

    public function yearly(Request $request, ReportService $reportService)
    {
        $year = $request->input('year', now()->year);

        $report = $reportService->yearlyReport(year: $year);

        return view('report.yearly', compact('report', 'year'));
    }
}
