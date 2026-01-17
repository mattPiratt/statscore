<?php

namespace Tests\Unit\Statistics\Application\Query;

use App\Statistics\Application\Query\GetStatisticsQuery;
use App\Statistics\Application\Query\GetStatisticsQueryHandler;
use App\Statistics\Domain\Model\TeamStatistics;
use App\Statistics\Domain\Repository\StatisticsStoreInterface;
use App\Statistics\Domain\ValueObject\MatchId;
use App\Statistics\Domain\ValueObject\TeamId;
use PHPUnit\Framework\TestCase;

class GetStatisticsQueryHandlerTest extends TestCase
{
    private StatisticsStoreInterface $statisticsStore;
    private GetStatisticsQueryHandler $handler;

    protected function setUp(): void
    {
        $this->statisticsStore = $this->createMock(StatisticsStoreInterface::class);
        $this->handler = new GetStatisticsQueryHandler($this->statisticsStore);
    }

    public function testAskReturnsTeamStatisticsWhenTeamIdIsProvided(): void
    {
        $matchId = 'm1';
        $teamId = 'arsenal';
        $stats = ['fouls' => 2, 'goals' => 1];

        $query = new GetStatisticsQuery($matchId, $teamId);

        $teamStats = new TeamStatistics(new MatchId($matchId), new TeamId($teamId), $stats);

        $this->statisticsStore
            ->expects($this->once())
            ->method('getTeamStatistics')
            ->with(new MatchId($matchId), new TeamId($teamId))
            ->willReturn($teamStats);

        $result = $this->handler->ask($query);

        $expected = [
            'match_id' => $matchId,
            'team_id' => $teamId,
            'statistics' => $stats
        ];

        $this->assertEquals($expected, $result);
    }

    public function testAskReturnsMatchStatisticsWhenTeamIdIsNull(): void
    {
        $matchId = 'm1';
        $stats = [
            'arsenal' => ['fouls' => 2, 'goals' => 1],
            'chelsea' => ['fouls' => 1, 'goals' => 0]
        ];

        $query = new GetStatisticsQuery($matchId, null);

        $this->statisticsStore
            ->expects($this->once())
            ->method('getMatchStatistics')
            ->with(new MatchId($matchId))
            ->willReturn($stats);

        $result = $this->handler->ask($query);

        $expected = [
            'match_id' => $matchId,
            'statistics' => $stats
        ];

        $this->assertEquals($expected, $result);
    }
}
