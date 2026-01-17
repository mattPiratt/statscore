<?php

namespace Tests\Unit\Statistics\Domain\Event;

use App\Statistics\Domain\Event\EventType;
use App\Statistics\Domain\Event\GoalEvent;
use App\Statistics\Domain\ValueObject\MatchId;
use App\Statistics\Domain\ValueObject\Player;
use App\Statistics\Domain\ValueObject\TeamId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class GoalEventTest extends TestCase
{
    public function test_it_correctly_stores_data(): void
    {
        $matchId = new MatchId('m1');
        $teamId = new TeamId('arsenal');
        $scorer = new Player('Bukayo Saka');
        $assistant = new Player('Martin Odegaard');
        $minute = 12;
        $second = 5;
        $occurredAt = new DateTimeImmutable();

        $event = new GoalEvent(
            $matchId,
            $teamId,
            $scorer,
            $assistant,
            $minute,
            $second,
            $occurredAt
        );

        $this->assertEquals(EventType::GOAL, $event->type());
        $this->assertSame($matchId, $event->matchId());
        $this->assertSame($teamId, $event->teamId());
        $this->assertSame($scorer, $event->scorer());
        $this->assertSame($assistant, $event->assistant());
        $this->assertEquals($minute, $event->minute());
        $this->assertEquals($second, $event->second());
        $this->assertSame($occurredAt, $event->occurredAt());
    }

    public function test_it_can_be_created_without_assistant(): void
    {
        $matchId = new MatchId('m1');
        $teamId = new TeamId('arsenal');
        $scorer = new Player('Bukayo Saka');
        $minute = 12;
        $second = null;
        $occurredAt = new DateTimeImmutable();

        $event = new GoalEvent(
            $matchId,
            $teamId,
            $scorer,
            null,
            $minute,
            $second,
            $occurredAt
        );

        $this->assertNull($event->assistant());
        $this->assertNull($event->second());
    }

    public function test_to_array_returns_correct_format(): void
    {
        $matchId = new MatchId('m1');
        $teamId = new TeamId('arsenal');
        $scorer = new Player('Bukayo Saka');
        $assistant = new Player('Martin Odegaard');
        $minute = 12;
        $second = 5;
        $occurredAt = new DateTimeImmutable('2024-01-01 12:00:00');

        $event = new GoalEvent(
            $matchId,
            $teamId,
            $scorer,
            $assistant,
            $minute,
            $second,
            $occurredAt
        );

        $expected = [
            'type' => 'goal',
            'match_id' => 'm1',
            'team_id' => 'arsenal',
            'player' => 'Bukayo Saka',
            'assistant' => 'Martin Odegaard',
            'minute' => 12,
            'second' => 5,
            'occurred_at' => $occurredAt->format(DateTimeImmutable::ATOM)
        ];

        $this->assertEquals($expected, $event->toArray());
    }
}
