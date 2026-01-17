<?php

namespace App\Statistics\Domain\Model;

use App\Statistics\Domain\ValueObject\MatchId;
use App\Statistics\Domain\ValueObject\TeamId;

class TeamStatistics
{
    private int $goals = 0;
    private int $assists = 0;
    private int $fouls = 0;

    public function __construct(
        private readonly MatchId $matchId,
        private readonly TeamId $teamId,
        array $data = []
    ) {
        $this->goals = $data['goals'] ?? 0;
        $this->assists = $data['assists'] ?? 0;
        $this->fouls = $data['fouls'] ?? 0;
    }

    public function matchId(): MatchId
    {
        return $this->matchId;
    }

    public function teamId(): TeamId
    {
        return $this->teamId;
    }

    public function recordGoal(): void
    {
        $this->goals++;
    }

    public function recordAssist(): void
    {
        $this->assists++;
    }

    public function recordFoul(): void
    {
        $this->fouls++;
    }

    public function toArray(): array
    {
        $result = [];
        if ($this->goals > 0) {
            $result['goals'] = $this->goals;
        }
        if ($this->assists > 0) {
            $result['assists'] = $this->assists;
        }
        if ($this->fouls > 0) {
            $result['fouls'] = $this->fouls;
        }
        return $result;
    }
}
