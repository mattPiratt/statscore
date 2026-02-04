<?php

namespace App\Statistics\Application\Command;

use App\Shared\CQRS\CommandInterface;

class StoreEventCommand implements CommandInterface
{
    public function __construct(
        public readonly array $data
    ) {
    }
}
