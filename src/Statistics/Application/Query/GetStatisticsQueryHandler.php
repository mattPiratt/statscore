<?php

namespace App\Statistics\Application\Query;

use App\Shared\CQRS\QueryHandlerInterface;
use App\Shared\CQRS\QueryInterface;
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
                'match_id' => $matchId->value(),
                'team_id' => $teamId->value(),
                'statistics' => $this->statisticsStore->getTeamStatistics($matchId, $teamId)->toArray()
            ];
        }

        return [
            'match_id' => $matchId->value(),
            'statistics' => $this->statisticsStore->getMatchStatistics($matchId)
        ];
    }
}
