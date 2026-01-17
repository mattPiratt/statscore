<?php

namespace App\Statistics\Domain\Event;

enum EventType: string
{
    case GOAL = 'goal';
    case FOUL = 'foul';
}
