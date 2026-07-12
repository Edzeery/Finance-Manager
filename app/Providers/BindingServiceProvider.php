<?php

namespace App\Providers;

use App\Contracts\Repositories\AssetRepositoryInterface;
use App\Contracts\Repositories\BudgetRepositoryInterface;
use App\Contracts\Repositories\DebtRepositoryInterface;
use App\Contracts\Repositories\ExpenseRepositoryInterface;
use App\Contracts\Repositories\GoalRepositoryInterface;
use App\Contracts\Repositories\IncomeRepositoryInterface;
use App\Contracts\Repositories\ZakatRepositoryInterface;
use App\Contracts\Services\ActivityLogServiceInterface;
use App\Contracts\Services\ChartDataServiceInterface;
use App\Contracts\Services\DashboardServiceInterface;
use App\Contracts\Services\ReportServiceInterface;
use App\Contracts\Services\SearchServiceInterface;
use App\Repositories\AssetRepository;
use App\Repositories\BudgetRepository;
use App\Repositories\DebtRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\GoalRepository;
use App\Repositories\IncomeRepository;
use App\Repositories\ZakatRepository;
use App\Services\ActivityLogService;
use App\Services\ChartDataService;
use App\Services\DashboardService;
use App\Services\ReportService;
use App\Services\SearchService;
use Illuminate\Support\ServiceProvider;

class BindingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AssetRepositoryInterface::class, AssetRepository::class);
        $this->app->bind(BudgetRepositoryInterface::class, BudgetRepository::class);
        $this->app->bind(DebtRepositoryInterface::class, DebtRepository::class);
        $this->app->bind(ExpenseRepositoryInterface::class, ExpenseRepository::class);
        $this->app->bind(GoalRepositoryInterface::class, GoalRepository::class);
        $this->app->bind(IncomeRepositoryInterface::class, IncomeRepository::class);
        $this->app->bind(ZakatRepositoryInterface::class, ZakatRepository::class);

        $this->app->bind(SearchServiceInterface::class, SearchService::class);
        $this->app->bind(DashboardServiceInterface::class, DashboardService::class);
        $this->app->bind(ChartDataServiceInterface::class, ChartDataService::class);
        $this->app->bind(ReportServiceInterface::class, ReportService::class);
        $this->app->bind(ActivityLogServiceInterface::class, ActivityLogService::class);
    }
}
