<?php

namespace App\Statistics\Domain\Strategy;

use App\Statistics\Domain\Event\GameEventInterface;
use App\Statistics\Domain\Event\GoalEvent;
use App\Statistics\Domain\Model\TeamStatistics;

class GoalStatisticsUpdateStrategy implements StatisticsUpdateStrategyInterface
{
    public function canHandle(GameEventInterface $event): bool
    {
        return $event instanceof GoalEvent;
    }

    /**
     * @param GoalEvent $event
     */
    public function update(GameEventInterface $event, TeamStatistics $statistics): void
    {
        $statistics->recordGoal();

        if ($event->assistant()) {
            $statistics->recordAssist();
        }
    }
}
