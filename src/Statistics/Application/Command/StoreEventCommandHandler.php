<?php

namespace App\Statistics\Application\Command;

use App\Statistics\Domain\Event\FoulEvent;
use App\Statistics\Domain\Event\GoalEvent;
use App\Statistics\Domain\Repository\EventsStoreInterface;
use App\Statistics\Domain\Repository\StatisticsStoreInterface;
use App\Statistics\Domain\ValueObject\MatchId;
use App\Statistics\Domain\ValueObject\Player;
use App\Statistics\Domain\ValueObject\TeamId;
use InvalidArgumentException;
use Psr\Clock\ClockInterface;

class StoreEventCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly EventsStoreInterface $eventsStore,
        private readonly StatisticsStoreInterface $statisticsStore,
        private readonly ClockInterface $clock
    ) {
    }

    public function handle(CommandInterface $command): array
    {
        $data = $command->data;

        if (!isset($data['type'])) {
            throw new InvalidArgumentException('Event type is required');
        }

        // TODO: factory method or strategy pattern
        $event = match ($data['type']) {
            'goal' => $this->createGoalEvent($data),
            'foul' => $this->createFoulEvent($data),
            default => throw new InvalidArgumentException('Unsupported event type: ' . $data['type']),
        };
        
        $this->eventsStore->save($event);

        // TODO: move this logic to a separate domain event
        $this->updateStatistics($event);

        return $event->toArray();
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

    private function updateStatistics($event): void
    {
        // TODO: strategy pattern in the future
        if ($event instanceof GoalEvent) {
            $this->statisticsStore->updateTeamStatistics(
                $event->matchId()->value(),
                $event->teamId()->value(),
                'goals'
            );

            if ($event->assistant()) {
                $this->statisticsStore->updateTeamStatistics(
                    $event->matchId()->value(),
                    $event->teamId()->value(),
                    'assists'
                );
            }
        } elseif ($event instanceof FoulEvent) {
            $this->statisticsStore->updateTeamStatistics(
                $event->matchId()->value(),
                $event->teamId()->value(),
                'fouls'
            );
        }
    }
}
