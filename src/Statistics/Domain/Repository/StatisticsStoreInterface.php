<?php

namespace App\Statistics\Domain\Repository;

use App\Statistics\Domain\Model\TeamStatistics;
use App\Statistics\Domain\ValueObject\MatchId;
use App\Statistics\Domain\ValueObject\TeamId;

interface StatisticsStoreInterface
{
    public function save(TeamStatistics $statistics): void;

    public function getTeamStatistics(MatchId $matchId, TeamId $teamId): TeamStatistics;

    public function getMatchStatistics(MatchId $matchId): array;
}
