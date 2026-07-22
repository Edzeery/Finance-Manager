<?php

namespace App\DTOs;

class KpiData
{
    public function __construct(
        public readonly float $totalIncome,
        public readonly float $totalExpense,
        public readonly float $netBalance,
        public readonly float $incomeChange,
        public readonly float $expenseChange,
        public readonly float $totalDebts,
        public readonly int $overdueDebts,
        public readonly float $totalAssets,
        public readonly float $totalSavings,
        public readonly float $totalIncomeAllTime,
        public readonly float $totalExpenseAllTime,
        public readonly float $totalDebtsOwing,
        public readonly float $totalDebtsPaid = 0,
        public readonly int $activeDebtsCount = 0,
        public readonly float $collectionRate = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            totalIncome: (float) ($data['totalIncome'] ?? 0),
            totalExpense: (float) ($data['totalExpense'] ?? 0),
            netBalance: (float) ($data['netBalance'] ?? 0),
            incomeChange: (float) ($data['incomeChange'] ?? 0),
            expenseChange: (float) ($data['expenseChange'] ?? 0),
            totalDebts: (float) ($data['totalDebts'] ?? 0),
            overdueDebts: (int) ($data['overdueDebts'] ?? 0),
            totalAssets: (float) ($data['totalAssets'] ?? 0),
            totalSavings: (float) ($data['totalSavings'] ?? 0),
            totalIncomeAllTime: (float) ($data['totalIncomeAllTime'] ?? 0),
            totalExpenseAllTime: (float) ($data['totalExpenseAllTime'] ?? 0),
            totalDebtsOwing: (float) ($data['totalDebtsOwing'] ?? 0),
            totalDebtsPaid: (float) ($data['totalDebtsPaid'] ?? 0),
            activeDebtsCount: (int) ($data['activeDebtsCount'] ?? 0),
            collectionRate: (float) ($data['collectionRate'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
