<?php

namespace App\Statistics\Domain\Repository;

use App\Statistics\Domain\Event\GameEventInterface;

interface EventsStoreInterface
{
    public function save(GameEventInterface $event): void;
}
