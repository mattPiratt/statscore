<?php

namespace App\Statistics\Application\Query;

interface QueryHandlerInterface
{
    public function ask(QueryInterface $query): mixed;
}
