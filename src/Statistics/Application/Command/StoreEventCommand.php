<?php

namespace App\Statistics\Application\Command;

class StoreEventCommand implements CommandInterface
{
    public function __construct(
        public readonly array $data
    ) {
    }
}
