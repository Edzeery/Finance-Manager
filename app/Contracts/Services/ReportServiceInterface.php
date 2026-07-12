<?php

namespace App\Contracts\Services;

interface ReportServiceInterface
{
    public function monthlyReport(int $year, int $month, ?int $userId = null): array;
    public function yearlyReport(int $year, ?int $userId = null): array;
}
