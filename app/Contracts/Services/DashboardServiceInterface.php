<?php

namespace App\Contracts\Services;

use App\DTOs\KpiData;

interface DashboardServiceInterface
{
    public function getKpiData(?string $period = null, ?string $startDate = null, ?string $endDate = null): KpiData;
}
