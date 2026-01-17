<?php

namespace App\Shared\Infrastructure;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

class SystemClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
