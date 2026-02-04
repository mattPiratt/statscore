<?php

namespace App\Shared\CQRS;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): mixed;
}
