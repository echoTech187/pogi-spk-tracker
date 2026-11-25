<?php

namespace App\Application\UseCases\Doctor;

use App\Application\DTOs\DashboardSummaryDto;
use App\Domain\Contracts\DashboardRepositoryInterface;

class GetDashboardSummaryUseCase
{
    public function __construct(
        private readonly DashboardRepositoryInterface $dashboardRepository
    ) {}

    public function execute(?string $branchId = null, ?string $picUserId = null): DashboardSummaryDto
    {
        return $this->dashboardRepository->getDashboardSummary($branchId, $picUserId);
    }
}
