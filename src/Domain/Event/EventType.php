<?php

namespace App\Domain\Event;

enum EventType: string
{
    case GOAL = 'goal';
    case FOUL = 'foul';
}
