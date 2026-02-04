<?php

namespace App\Shared\CQRS;

interface QueryHandlerInterface
{
    public function ask(QueryInterface $query): mixed;
}
