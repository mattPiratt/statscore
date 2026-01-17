<?php

namespace App\Statistics\Domain\ValueObject;

enum StatType: string
{
    case GOALS = 'goals';
    case ASSISTS = 'assists';
    case FOULS = 'fouls';
}
