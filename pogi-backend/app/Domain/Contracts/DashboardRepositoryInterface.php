<?php

namespace App\Domain\Contracts;

use App\Application\DTOs\DashboardSummaryDto;

interface DashboardRepositoryInterface
{
    /**
     * Ambil agregasi statistik ringkasan dashboard nasional atau per cabang.
     *
     * @param string|null $branchId UUID cabang untuk isolasi multi-tenant Admin Cabang
     * @return DashboardSummaryDto
     */
    public function getDashboardSummary(?string $branchId = null): DashboardSummaryDto;
}
