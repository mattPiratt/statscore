<?php

namespace App\Statistics\Domain\Factory;

use App\Statistics\Domain\Event\GameEventInterface;

interface GameEventFactoryInterface
{
    public function create(array $data): GameEventInterface;
}
