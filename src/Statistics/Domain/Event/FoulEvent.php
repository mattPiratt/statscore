<?php

namespace App\Statistics\Domain\Event;

use App\Statistics\Domain\ValueObject\MatchId;
use App\Statistics\Domain\ValueObject\Player;
use App\Statistics\Domain\ValueObject\TeamId;
use DateTimeImmutable;

class FoulEvent implements GameEventInterface
{
    public function __construct(
        private readonly MatchId $matchId,
        private readonly TeamId $teamId,
        private readonly Player $playerAtFault,
        private readonly Player $affectedPlayer,
        private readonly int $minute,
        private readonly ?int $second,
        private readonly DateTimeImmutable $occurredAt
    ) {
    }

    public function type(): EventType
    {
        return EventType::FOUL;
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

    public function playerAtFault(): Player
    {
        return $this->playerAtFault;
    }

    public function affectedPlayer(): Player
    {
        return $this->affectedPlayer;
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
            'player' => $this->playerAtFault->value(),
            'affected_player' => $this->affectedPlayer->value(),
            'minute' => $this->minute,
            'second' => $this->second,
            'occurred_at' => $this->occurredAt->format(DateTimeImmutable::ATOM)
        ];
    }
}
