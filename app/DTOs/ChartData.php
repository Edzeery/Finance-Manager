<?php

namespace App\DTOs;

class ChartData
{
    public function __construct(
        public readonly array $labels = [],
        public readonly array $datasets = [],
    ) {}

    public static function fromMonthlyData(array $labels, array $incomeData, array $expenseData): self
    {
        return new self($labels, [
            ['label' => __('dashboard.total_income'), 'data' => $incomeData],
            ['label' => __('dashboard.total_expense'), 'data' => $expenseData],
        ]);
    }

    public static function fromCategoryData(array $labels, array $data, array $colors): self
    {
        return new self($labels, [
            ['data' => $data, 'colors' => $colors],
        ]);
    }

    public static function fromGrowthData(array $labels, array $data): self
    {
        return new self($labels, [
            ['label' => __('dashboard.net_balance'), 'data' => $data],
        ]);
    }
}
