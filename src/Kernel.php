<?php

namespace App;

use App\Presentation\Http\GameController;
use App\Shared\Infrastructure\SystemClock;
use App\Statistics\Domain\Factory\GameEventFactory;
use App\Statistics\Domain\Strategy\FoulStatisticsUpdateStrategy;
use App\Statistics\Domain\Strategy\GoalStatisticsUpdateStrategy;
use App\Statistics\Infrastructure\Persistence\EventsStore;
use App\Statistics\Infrastructure\Persistence\StatisticsStore;

class Kernel
{
    private GameController $controller;

    public function __construct(string $baseDir)
    {
        // TODO: use config package to handle paths
        $eventsPath = $baseDir . '/storage/events.txt';
        $statsPath = $baseDir . '/storage/statistics.txt';

        // TODO: use php-di/php-di package to handle cleanly inversion of controll
        $eventsStore = new EventsStore($eventsPath);
        $statsStore = new StatisticsStore($statsPath);
        $gameEventFactory = new GameEventFactory(new SystemClock());
        $statisticsStrategies = [
            new GoalStatisticsUpdateStrategy(),
            new FoulStatisticsUpdateStrategy(),
        ];

        $this->controller = new GameController(
            eventsStore: $eventsStore,
            statsStore: $statsStore,
            eventFactory: $gameEventFactory,
            strategies: $statisticsStrategies
        );
    }

    public function handleRequest(): void
    {
        header('Content-Type: application/json');

        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if ($method === 'POST' && $path === '/event') {
            $this->controller->handleEvent();
        } elseif ($method === 'GET' && $path === '/statistics') {
            $this->controller->handleStatistics(
                matchId: $_GET['match_id'] ?? null,
                teamId: $_GET['team_id'] ?? null
            );
        } else {
            $this->controller->sendNotFound();
        }
    }
}
