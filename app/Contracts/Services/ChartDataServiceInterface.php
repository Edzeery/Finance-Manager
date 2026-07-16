<?php

namespace App\Contracts\Services;

use App\DTOs\ChartData;

interface ChartDataServiceInterface
{
    public function monthlyIncomeExpense(?string $period = null, ?string $startDate = null, ?string $endDate = null): ChartData;

    public function expenseByCategory(?string $period = null, ?string $startDate = null, ?string $endDate = null): ChartData;

    public function netBalanceHistory(?string $period = null, ?string $startDate = null, ?string $endDate = null): ChartData;
}
