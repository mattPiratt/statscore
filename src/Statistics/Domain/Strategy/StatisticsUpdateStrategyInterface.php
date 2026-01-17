<?php

namespace App\Statistics\Domain\Strategy;

use App\Statistics\Domain\Event\GameEventInterface;
use App\Statistics\Domain\Model\TeamStatistics;

interface StatisticsUpdateStrategyInterface
{
    public function canHandle(GameEventInterface $event): bool;

    public function update(GameEventInterface $event, TeamStatistics $statistics): void;
}
