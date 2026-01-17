<?php

namespace Tests\Unit\Statistics\Domain\Model;

use App\Statistics\Domain\Model\TeamStatistics;
use App\Statistics\Domain\ValueObject\MatchId;
use App\Statistics\Domain\ValueObject\TeamId;
use PHPUnit\Framework\TestCase;

class TeamStatisticsTest extends TestCase
{
    public function testInitialState(): void
    {
        $matchId = new MatchId('m1');
        $teamId = new TeamId('t1');
        $stats = new TeamStatistics($matchId, $teamId);

        $this->assertSame($matchId, $stats->matchId());
        $this->assertSame($teamId, $stats->teamId());
        $this->assertEquals([], $stats->toArray());
    }

    public function testHydrationFromData(): void
    {
        $matchId = new MatchId('m1');
        $teamId = new TeamId('t1');
        $data = [
            'goals' => 2,
            'assists' => 1,
            'fouls' => 3
        ];

        $stats = new TeamStatistics($matchId, $teamId, $data);

        $this->assertEquals($data, $stats->toArray());
    }

    public function testRecordGoal(): void
    {
        $stats = new TeamStatistics(new MatchId('m1'), new TeamId('t1'));
        $stats->recordGoal();

        $result = $stats->toArray();
        $this->assertEquals(1, $result['goals']);
        $this->assertArrayNotHasKey('assists', $result);
        $this->assertArrayNotHasKey('fouls', $result);

        $stats->recordGoal();
        $this->assertEquals(2, $stats->toArray()['goals']);
    }

    public function testRecordAssist(): void
    {
        $stats = new TeamStatistics(new MatchId('m1'), new TeamId('t1'));
        $stats->recordAssist();

        $result = $stats->toArray();
        $this->assertEquals(1, $result['assists']);
        $this->assertArrayNotHasKey('goals', $result);
        $this->assertArrayNotHasKey('fouls', $result);
    }

    public function testRecordFoul(): void
    {
        $stats = new TeamStatistics(new MatchId('m1'), new TeamId('t1'));
        $stats->recordFoul();

        $result = $stats->toArray();
        $this->assertEquals(1, $result['fouls']);
        $this->assertArrayNotHasKey('goals', $result);
        $this->assertArrayNotHasKey('assists', $result);
    }

    public function testToArrayOnlyIncludesNonZeroValues(): void
    {
        $stats = new TeamStatistics(new MatchId('m1'), new TeamId('t1'), [
            'goals' => 1,
            'assists' => 0,
            'fouls' => 0
        ]);

        $this->assertEquals(['goals' => 1], $stats->toArray());
    }
}
