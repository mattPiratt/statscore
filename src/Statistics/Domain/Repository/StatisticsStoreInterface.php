<?php

namespace App\Statistics\Domain\Repository;

interface StatisticsStoreInterface
{
    public function updateTeamStatistics(string $matchId, string $teamId, string $statType, int $value = 1): void;

    public function getTeamStatistics(string $matchId, string $teamId): array;

    public function getMatchStatistics(string $matchId): array;
}
