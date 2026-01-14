<?php

namespace App\Domain\Event;

use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\TeamId;
use DateTimeImmutable;

interface GameEventInterface
{
    public function type(): EventType;

    public function occurredAt(): DateTimeImmutable;

    public function matchId(): MatchId;

    public function teamId(): TeamId;

    public function toArray(): array;
}
