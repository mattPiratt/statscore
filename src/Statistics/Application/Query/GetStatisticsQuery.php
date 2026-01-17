<?php

namespace App\Statistics\Application\Query;

class GetStatisticsQuery implements QueryInterface
{
    public function __construct(
        public readonly string $matchId,
        public readonly ?string $teamId = null
    ) {
    }
}
