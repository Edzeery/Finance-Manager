<?php

namespace App\Providers;

use App\Contracts\Services\ActivityLogServiceInterface;
use App\Jobs\LogActivity;
use App\Models\Asset;
use App\Models\Budget;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialGoal;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\ZakatRecord;
use App\Observers\DashboardCacheObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class ModelEventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $models = [
            Income::class => 'income',
            Expense::class => 'expense',
            Debt::class => 'debt',
            Asset::class => 'asset',
            Budget::class => 'budget',
            FinancialGoal::class => 'goal',
            ZakatRecord::class => 'zakat',
            ExpenseCategory::class => 'expense_category',
            IncomeCategory::class => 'income_category',
        ];

        foreach ($models as $modelClass => $label) {
            $modelClass::created(function ($subject) use ($label) {
                $userId = $subject->user_id ?? auth()->id();
                if ($userId === null) {
                    return;
                }
                $service = app(ActivityLogServiceInterface::class);
                LogActivity::dispatch(
                    $userId,
                    'created',
                    get_class($subject),
                    $subject->id,
                    __("messages.{$label}_created"),
                    $service->filterSensitiveData($subject->toArray()),
                    Request::ip(),
                    Request::userAgent(),
                );
            });

            $modelClass::updated(function ($subject) use ($label) {
                $userId = $subject->user_id ?? auth()->id();
                if ($userId === null) {
                    return;
                }
                $service = app(ActivityLogServiceInterface::class);
                $old = $service->filterSensitiveData(['old' => $subject->getOriginal()]);
                $new = $service->filterSensitiveData(['new' => $subject->getChanges()]);
                LogActivity::dispatch(
                    $userId,
                    'updated',
                    get_class($subject),
                    $subject->id,
                    __("messages.{$label}_updated"),
                    array_merge($old, $new),
                    Request::ip(),
                    Request::userAgent(),
                );
            });

            $modelClass::deleted(function ($subject) use ($label) {
                if ($subject->isForceDeleting()) {
                    return;
                }
                $userId = $subject->user_id ?? auth()->id();
                if ($userId === null) {
                    return;
                }
                $service = app(ActivityLogServiceInterface::class);
                LogActivity::dispatch(
                    $userId,
                    'deleted',
                    get_class($subject),
                    $subject->id,
                    __("messages.{$label}_deleted"),
                    $service->filterSensitiveData($subject->toArray()),
                    Request::ip(),
                    Request::userAgent(),
                );
            });

            if (in_array(SoftDeletes::class, class_uses($modelClass))) {
                $modelClass::restored(function ($subject) use ($label) {
                    $userId = $subject->user_id ?? auth()->id();
                    if ($userId === null) {
                        return;
                    }
                    $service = app(ActivityLogServiceInterface::class);
                    LogActivity::dispatch(
                        $userId,
                        'restored',
                        get_class($subject),
                        $subject->id,
                        __("messages.{$label}_restored"),
                        $service->filterSensitiveData($subject->toArray()),
                        Request::ip(),
                        Request::userAgent(),
                    );
                });
            }
        }

        $financialModels = [Income::class, Expense::class, Debt::class, Asset::class, Budget::class, FinancialGoal::class];
        foreach ($financialModels as $modelClass) {
            $modelClass::observe(DashboardCacheObserver::class);
        }
    }
}
