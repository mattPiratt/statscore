<?php

namespace App\Statistics\Domain\Strategy;

use App\Statistics\Domain\Event\GameEventInterface;
use App\Statistics\Domain\Event\GoalEvent;
use App\Statistics\Domain\Repository\StatisticsStoreInterface;
use App\Statistics\Domain\ValueObject\StatType;

class GoalStatisticsUpdateStrategy implements StatisticsUpdateStrategyInterface
{
    public function canHandle(GameEventInterface $event): bool
    {
        return $event instanceof GoalEvent;
    }

    /**
     * @param GoalEvent $event
     */
    public function update(GameEventInterface $event, StatisticsStoreInterface $statisticsStore): void
    {
        $statisticsStore->updateTeamStatistics(
            $event->matchId()->value(),
            $event->teamId()->value(),
            StatType::GOALS
        );

        if ($event->assistant()) {
            $statisticsStore->updateTeamStatistics(
                $event->matchId()->value(),
                $event->teamId()->value(),
                StatType::ASSISTS
            );
        }
    }
}
