<?php

namespace App\Statistics\Infrastructure\Persistence;

use App\Statistics\Domain\Model\TeamStatistics;
use App\Statistics\Domain\Repository\StatisticsStoreInterface;
use App\Statistics\Domain\ValueObject\MatchId;
use App\Statistics\Domain\ValueObject\TeamId;
use App\Statistics\Infrastructure\File\FileStorage;

class StatisticsStore implements StatisticsStoreInterface
{
    private FileStorage $storage;

    public function __construct(string $statsFile)
    {
        $this->storage = new FileStorage($statsFile);
    }

    public function save(TeamStatistics $statistics): void
    {
        $stats = $this->getStatistics();

        $matchId = $statistics->matchId()->value();
        $teamId = $statistics->teamId()->value();

        if (!isset($stats[$matchId])) {
            $stats[$matchId] = [];
        }

        $stats[$matchId][$teamId] = $statistics->toArray();

        $this->saveStatistics($stats);
    }

    public function getTeamStatistics(MatchId $matchId, TeamId $teamId): TeamStatistics
    {
        $stats = $this->getStatistics();
        $data = $stats[$matchId->value()][$teamId->value()] ?? [];

        return new TeamStatistics($matchId, $teamId, $data);
    }

    public function getMatchStatistics(MatchId $matchId): array
    {
        $stats = $this->getStatistics();
        return $stats[$matchId->value()] ?? [];
    }

    private function getStatistics(): array
    {
        $content = $this->storage->getContent();
        if ($content === null) {
            return [];
        }

        return json_decode($content, true) ?? [];
    }

    private function saveStatistics(array $stats): void
    {
        $this->storage->overwrite($stats);
    }
}
