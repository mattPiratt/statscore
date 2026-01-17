<?php

namespace App\Statistics\Application\Query;

use App\Statistics\Domain\Repository\StatisticsStoreInterface;
use App\Statistics\Domain\ValueObject\MatchId;
use App\Statistics\Domain\ValueObject\TeamId;

class GetStatisticsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly StatisticsStoreInterface $statisticsStore
    ) {
    }

    public function ask(QueryInterface $query): array
    {
        $matchId = new MatchId($query->matchId);

        if ($query->teamId !== null) {
            $teamId = new TeamId($query->teamId);
            return [
                'match_id' => $query->matchId,
                'team_id' => $query->teamId,
                'statistics' => $this->statisticsStore->getTeamStatistics($matchId, $teamId)->toArray()
            ];
        }

        return [
            'match_id' => $query->matchId,
            'statistics' => $this->statisticsStore->getMatchStatistics($matchId)
        ];
    }
}
