<?php

namespace App\Repositories\Concerns;

use App\Support\DatabaseHelper;

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

    public function monthlyTotals(string $start, string $end): \Illuminate\Database\Eloquent\Collection
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
