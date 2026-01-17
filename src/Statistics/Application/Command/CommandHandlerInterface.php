<?php

namespace App\Statistics\Application\Command;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): mixed;
}
