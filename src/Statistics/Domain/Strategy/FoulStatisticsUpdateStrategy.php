<?php

namespace App\Statistics\Domain\Strategy;

use App\Statistics\Domain\Event\FoulEvent;
use App\Statistics\Domain\Event\GameEventInterface;
use App\Statistics\Domain\Repository\StatisticsStoreInterface;
use App\Statistics\Domain\ValueObject\StatType;

class FoulStatisticsUpdateStrategy implements StatisticsUpdateStrategyInterface
{
    public function canHandle(GameEventInterface $event): bool
    {
        return $event instanceof FoulEvent;
    }

    /**
     * @param FoulEvent $event
     */
    public function update(GameEventInterface $event, StatisticsStoreInterface $statisticsStore): void
    {
        $statisticsStore->updateTeamStatistics(
            $event->matchId()->value(),
            $event->teamId()->value(),
            StatType::FOULS
        );
    }
}
