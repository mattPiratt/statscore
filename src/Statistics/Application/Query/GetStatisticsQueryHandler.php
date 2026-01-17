<?php

namespace App\Statistics\Application\Query;

use App\Statistics\Domain\Repository\StatisticsStoreInterface;

class GetStatisticsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly StatisticsStoreInterface $statisticsStore
    ) {
    }

    public function ask(QueryInterface $query): array
    {
        if ($query->teamId !== null) {
            return [
                'match_id' => $query->matchId,
                'team_id' => $query->teamId,
                'statistics' => $this->statisticsStore->getTeamStatistics($query->matchId, $query->teamId)
            ];
        }

        return [
            'match_id' => $query->matchId,
            'statistics' => $this->statisticsStore->getMatchStatistics($query->matchId)
        ];
    }
}
