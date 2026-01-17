<?php

namespace App;

use App\Presentation\Http\GameController;
use App\Shared\Infrastructure\SystemClock;
use App\Statistics\Infrastructure\Persistence\EventsStore;
use App\Statistics\Infrastructure\Persistence\StatisticsStore;

class Kernel
{
    private GameController $controller;

    public function __construct(string $baseDir)
    {
        $eventsPath = $baseDir . '/storage/events.txt';
        $statsPath = $baseDir . '/storage/statistics.txt';

        // TODO: use php-di/php-di package to handle cleanly inversion of controll
        $eventsStore = new EventsStore($eventsPath);
        $statsStore = new StatisticsStore($statsPath);
        $clock = new SystemClock();

        $this->controller = new GameController($eventsStore, $statsStore, $clock);
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
