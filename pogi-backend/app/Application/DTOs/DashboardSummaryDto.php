<?php

namespace App\Application\DTOs;

readonly class DashboardSummaryDto
{
    /**
     * @param array<int, array{name: string, total: int, complete: int, pct: int}> $lowestProvinces
     * @param array<int, array{name: string, total: int, complete: int, pct: int}> $highestProvinces
     * @param array<int, array{id: string, name: string, doctors: int, complete: int, incomplete: int, completionRate: float}> $provincesData
     * @param array<int, array{name: string, doctors: int, completionRate: float}> $topProvinces
     * @param array<int, array{id: string, name: string, count: int}> $scopeOptions
     * @param array<int, array{actor: string, action: string, details: string, timestamp: string, category: string}> $recentActivities
     */
    public function __construct(
        public int $totalDoctors,
        public int $avgProgressPct,
        public int $completeCount,
        public int $incompleteCount,
        public int $pendingCount,
        public float $avgPelayananPct,
        public float $avgPengabdianPct,
        public float $avgPembelajaranPct,
        public float $avgAccumulationPct,
        public array $lowestProvinces,
        public array $highestProvinces,
        public array $provincesData,
        public array $topProvinces,
        public array $scopeOptions,
        public array $recentActivities,
        public string $activeScope = 'all',
        public string $activeScopeLabel = 'Semua Wilayah Nasional (38 Provinsi)',
        public bool $isFromLiveDatabase = true,
    ) {}

    public function completePercentage(): int
    {
        return $this->totalDoctors > 0 ? (int) round(($this->completeCount / $this->totalDoctors) * 100) : 0;
    }

    public function incompletePercentage(): int
    {
        return $this->totalDoctors > 0 ? (int) round(($this->incompleteCount / $this->totalDoctors) * 100) : 0;
    }

    public function toArray(): array
    {
        return [
            'totalDoctors' => $this->totalDoctors,
            'avgProgressPct' => $this->avgProgressPct,
            'completeCount' => $this->completeCount,
            'incompleteCount' => $this->incompleteCount,
            'pendingCount' => $this->pendingCount,
            'completePct' => $this->completePercentage(),
            'incompletePct' => $this->incompletePercentage(),
            'avgPelayananPct' => $this->avgPelayananPct,
            'avgPengabdianPct' => $this->avgPengabdianPct,
            'avgPembelajaranPct' => $this->avgPembelajaranPct,
            'avgAccumulationPct' => $this->avgAccumulationPct,
            'lowestProvinces' => $this->lowestProvinces,
            'highestProvinces' => $this->highestProvinces,
            'provincesData' => $this->provincesData,
            'topProvinces' => $this->topProvinces,
            'scopeOptions' => $this->scopeOptions,
            'recentActivities' => $this->recentActivities,
            'activeScope' => $this->activeScope,
            'activeScopeLabel' => $this->activeScopeLabel,
            'isFromLiveDatabase' => $this->isFromLiveDatabase,
        ];
    }
}
