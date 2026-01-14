<?php

namespace App;

use InvalidArgumentException;

class EventHandler
{
    private FileStorage $storage;
    private StatisticsManager $statisticsManager;

    public function __construct(string $storagePath, ?StatisticsManager $statisticsManager = null)
    {
        $this->storage = new FileStorage($storagePath);
        $this->statisticsManager = $statisticsManager ?? new StatisticsManager(__DIR__ . '/../storage/statistics.txt');
    }

    public function handleEvent(array $data): array
    {
        if (!isset($data['type'])) {
            throw new InvalidArgumentException('Event type is required');
        }

        $event = [
            'type' => $data['type'],
            'timestamp' => time(),
            'data' => $data
        ];

        $this->storage->save($event);

        // Update statistics for foul events
        if ($data['type'] === 'foul') {
            if (!isset($data['match_id']) || !isset($data['team_id']) || !isset($data['player']) || !isset($data['affected_player'])) {
                throw new InvalidArgumentException(
                    'match_id, team_id, player and affected_player are required for foul events'
                );
            }

            $this->statisticsManager->updateTeamStatistics(
                $data['match_id'],
                $data['team_id'],
                'fouls'
            );
        }

        // Update statistics for goal events
        if ($data['type'] === 'goal') {
            if (!isset($data['match_id']) || !isset($data['team_id']) || !isset($data['player'])) {
                throw new InvalidArgumentException('match_id, team_id and player are required for goal events');
            }

            $this->statisticsManager->updateTeamStatistics(
                $data['match_id'],
                $data['team_id'],
                'goals'
            );

            if (isset($data['assistant'])) {
                $this->statisticsManager->updateTeamStatistics(
                    $data['match_id'],
                    $data['team_id'],
                    'assists'
                );
            }
        }

        return [
            'status' => 'success',
            'message' => 'Event saved successfully',
            'event' => $event
        ];
    }
}