<?php

namespace App\Statistics\Domain\Event;

use App\Statistics\Domain\ValueObject\MatchId;
use App\Statistics\Domain\ValueObject\TeamId;
use DateTimeImmutable;

interface GameEventInterface
{
    public function type(): EventType;

    public function occurredAt(): DateTimeImmutable;

    public function matchId(): MatchId;

    public function teamId(): TeamId;

    public function toArray(): array;
}
