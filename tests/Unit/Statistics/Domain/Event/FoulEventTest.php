<?php

namespace App\Tests\Unit\Statistics\Domain\Event;

use App\Statistics\Domain\Event\EventType;
use App\Statistics\Domain\Event\FoulEvent;
use App\Statistics\Domain\ValueObject\MatchId;
use App\Statistics\Domain\ValueObject\Player;
use App\Statistics\Domain\ValueObject\TeamId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class FoulEventTest extends TestCase
{
    public function test_it_correctly_stores_data(): void
    {
        $matchId = new MatchId('m1');
        $teamId = new TeamId('arsenal');
        $playerAtFault = new Player('William Saliba');
        $affectedPlayer = new Player('Erling Haaland');
        $minute = 45;
        $second = 34;
        $occurredAt = new DateTimeImmutable();

        $event = new FoulEvent(
            $matchId,
            $teamId,
            $playerAtFault,
            $affectedPlayer,
            $minute,
            $second,
            $occurredAt
        );

        $this->assertEquals(EventType::FOUL, $event->type());
        $this->assertSame($matchId, $event->matchId());
        $this->assertSame($teamId, $event->teamId());
        $this->assertSame($playerAtFault, $event->playerAtFault());
        $this->assertSame($affectedPlayer, $event->affectedPlayer());
        $this->assertEquals($minute, $event->minute());
        $this->assertEquals($second, $event->second());
        $this->assertSame($occurredAt, $event->occurredAt());
    }

    public function test_to_array_returns_correct_format(): void
    {
        $matchId = new MatchId('m1');
        $teamId = new TeamId('arsenal');
        $playerAtFault = new Player('William Saliba');
        $affectedPlayer = new Player('Erling Haaland');
        $minute = 45;
        $second = 34;
        $occurredAt = new DateTimeImmutable('2024-01-01 12:00:00');

        $event = new FoulEvent(
            $matchId,
            $teamId,
            $playerAtFault,
            $affectedPlayer,
            $minute,
            $second,
            $occurredAt
        );

        $expected = [
            'type' => 'foul',
            'match_id' => 'm1',
            'team_id' => 'arsenal',
            'player' => 'William Saliba',
            'affected_player' => 'Erling Haaland',
            'minute' => 45,
            'second' => 34,
            'occurred_at' => $occurredAt->format(DateTimeImmutable::ATOM)
        ];

        $this->assertEquals($expected, $event->toArray());
    }
}
