<?php

namespace App\Repositories\Concerns;

use App\Support\DatabaseHelper;
use Illuminate\Database\Eloquent\Collection;

trait HasMonthlyTransactions
{
    public function monthlyTotal(int $year, int $month): float
    {
        $modelClass = $this->model::class;

        return (float) $modelClass::active()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('amount');
    }

    public function monthlyTotals(string $start, string $end): Collection
    {
        $monthExpr = DatabaseHelper::monthExpression();
        $modelClass = $this->model::class;

        return $modelClass::whereBetween('date', [$start, $end])
            ->selectRaw("{$monthExpr} as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');
    }
}
