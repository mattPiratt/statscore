<?php

namespace App\Statistics\Infrastructure\Persistence;

use App\Statistics\Domain\Repository\StatisticsStoreInterface;
use App\Statistics\Infrastructure\File\FileStorage;

class StatisticsStore implements StatisticsStoreInterface
{
    private FileStorage $storage;

    public function __construct(string $statsFile)
    {
        $this->storage = new FileStorage($statsFile);
    }

    public function updateTeamStatistics(string $matchId, string $teamId, string $statType, int $value = 1): void
    {
        $stats = $this->getStatistics();

        if (!isset($stats[$matchId])) {
            $stats[$matchId] = [];
        }

        if (!isset($stats[$matchId][$teamId])) {
            $stats[$matchId][$teamId] = [];
        }

        if (!isset($stats[$matchId][$teamId][$statType])) {
            $stats[$matchId][$teamId][$statType] = 0;
        }

        $stats[$matchId][$teamId][$statType] += $value;

        $this->saveStatistics($stats);
    }

    public function getTeamStatistics(string $matchId, string $teamId): array
    {
        $stats = $this->getStatistics();
        return $stats[$matchId][$teamId] ?? [];
    }

    public function getMatchStatistics(string $matchId): array
    {
        $stats = $this->getStatistics();
        return $stats[$matchId] ?? [];
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
