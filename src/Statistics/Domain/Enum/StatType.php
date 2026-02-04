<?php

namespace App\Statistics\Domain\Enum;

enum StatType: string
{
    case GOALS = 'goals';
    case ASSISTS = 'assists';
    case FOULS = 'fouls';
}
