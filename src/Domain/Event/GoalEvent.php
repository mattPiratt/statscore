<?php

namespace App\Domain\Event;

use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\Player;
use App\Domain\ValueObject\TeamId;
use DateTimeImmutable;

class GoalEvent implements GameEventInterface
{
    private DateTimeImmutable $occurredAt;

    public function __construct(
        private readonly MatchId $matchId,
        private readonly TeamId $teamId,
        private readonly Player $scorer,
        private readonly ?Player $assistant,
        private readonly int $minute,
        private readonly ?int $second = null
    ) {
        $this->occurredAt = new DateTimeImmutable();
    }

    public function type(): EventType
    {
        return EventType::GOAL;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function matchId(): MatchId
    {
        return $this->matchId;
    }

    public function teamId(): TeamId
    {
        return $this->teamId;
    }

    public function scorer(): Player
    {
        return $this->scorer;
    }

    public function assistant(): ?Player
    {
        return $this->assistant;
    }

    public function minute(): int
    {
        return $this->minute;
    }

    public function second(): ?int
    {
        return $this->second;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type()->value,
            'match_id' => $this->matchId->value(),
            'team_id' => $this->teamId->value(),
            'player' => $this->scorer->value(),
            'assistant' => $this->assistant?->value(),
            'minute' => $this->minute,
            'second' => $this->second,
            'occurred_at' => $this->occurredAt->format(DateTimeImmutable::ATOM)
        ];
    }
}
