<?php

namespace App\Statistics\Domain\Service;

use App\Statistics\Domain\Event\GameEventInterface;

interface RealTimeNotifierInterface
{
    public function notify(GameEventInterface $event): void;
}
