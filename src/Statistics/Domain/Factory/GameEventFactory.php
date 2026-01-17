<?php

namespace App\Statistics\Domain\Factory;

use App\Statistics\Domain\Event\FoulEvent;
use App\Statistics\Domain\Event\GameEventInterface;
use App\Statistics\Domain\Event\GoalEvent;
use App\Statistics\Domain\ValueObject\MatchId;
use App\Statistics\Domain\ValueObject\Player;
use App\Statistics\Domain\ValueObject\TeamId;
use InvalidArgumentException;
use Psr\Clock\ClockInterface;

class GameEventFactory implements GameEventFactoryInterface
{
    public function __construct(
        private readonly ClockInterface $clock
    ) {
    }

    public function create(array $data): GameEventInterface
    {
        if (!isset($data['type'])) {
            throw new InvalidArgumentException('Event type is required');
        }

        return match ($data['type']) {
            'goal' => $this->createGoalEvent($data),
            'foul' => $this->createFoulEvent($data),
            default => throw new InvalidArgumentException('Unsupported event type: ' . $data['type']),
        };
    }

    private function createGoalEvent(array $data): GoalEvent
    {
        if (!isset($data['match_id']) || !isset($data['team_id']) || !isset($data['player'])) {
            throw new InvalidArgumentException('match_id, team_id and player are required for goal events');
        }

        return new GoalEvent(
            new MatchId($data['match_id']),
            new TeamId($data['team_id']),
            new Player($data['player']),
            isset($data['assistant']) ? new Player($data['assistant']) : null,
            (int)($data['minute'] ?? 0),
            isset($data['second']) ? (int)$data['second'] : null,
            $this->clock->now()
        );
    }

    private function createFoulEvent(array $data): FoulEvent
    {
        if (!isset($data['match_id']) || !isset($data['team_id']) || !isset($data['player']) || !isset($data['affected_player'])) {
            throw new InvalidArgumentException(
                'match_id, team_id, player and affected_player are required for foul events'
            );
        }

        return new FoulEvent(
            new MatchId($data['match_id']),
            new TeamId($data['team_id']),
            new Player($data['player']),
            new Player($data['affected_player']),
            (int)($data['minute'] ?? 0),
            isset($data['second']) ? (int)$data['second'] : null,
            $this->clock->now()
        );
    }
}
