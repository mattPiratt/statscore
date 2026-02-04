<?php

namespace App\Statistics\Application\Query;

use App\Shared\CQRS\QueryInterface;

class GetStatisticsQuery implements QueryInterface
{
    public function __construct(
        public readonly string $matchId,
        public readonly ?string $teamId = null
    ) {
    }
}
