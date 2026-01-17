<?php

namespace App\Statistics\Domain\Repository;

use App\Statistics\Domain\ValueObject\StatType;

interface StatisticsStoreInterface
{
    public function updateTeamStatistics(string $matchId, string $teamId, StatType $statType, int $value = 1): void;

    public function getTeamStatistics(string $matchId, string $teamId): array;

    public function getMatchStatistics(string $matchId): array;
}
