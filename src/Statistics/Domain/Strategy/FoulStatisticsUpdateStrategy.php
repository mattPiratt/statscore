<?php

namespace App\Statistics\Domain\Strategy;

use App\Statistics\Domain\Event\FoulEvent;
use App\Statistics\Domain\Event\GameEventInterface;
use App\Statistics\Domain\Model\TeamStatistics;

class FoulStatisticsUpdateStrategy implements StatisticsUpdateStrategyInterface
{
    public function canHandle(GameEventInterface $event): bool
    {
        return $event instanceof FoulEvent;
    }

    /**
     * @param FoulEvent $event
     */
    public function update(GameEventInterface $event, TeamStatistics $statistics): void
    {
        $statistics->recordFoul();
    }
}
