<?php

namespace App\Presentation\Http;

use App\Shared\Infrastructure\HttpJsonController;
use App\Statistics\Application\Command\StoreEventCommand;
use App\Statistics\Application\Command\StoreEventCommandHandler;
use App\Statistics\Application\Query\GetStatisticsQuery;
use App\Statistics\Application\Query\GetStatisticsQueryHandler;
use App\Statistics\Domain\Factory\GameEventFactoryInterface;
use App\Statistics\Domain\Repository\EventsStoreInterface;
use App\Statistics\Domain\Repository\StatisticsStoreInterface;
use App\Statistics\Domain\Strategy\StatisticsUpdateStrategyInterface;
use Exception;

class GameController extends HttpJsonController
{
    /**
     * @var StatisticsUpdateStrategyInterface[] $strategies
     */
    public function __construct(
        private readonly EventsStoreInterface $eventsStore,
        private readonly StatisticsStoreInterface $statsStore,
        private readonly GameEventFactoryInterface $eventFactory,
        private readonly array $strategies = []
    ) {
    }

    public function handleEvent(): void
    {
        try {
            $data = $this->getJsonData();

            $handler = new StoreEventCommandHandler(
                eventsStore: $this->eventsStore,
                statisticsStore: $this->statsStore,
                eventFactory: $this->eventFactory,
                strategies: $this->strategies
            );
            $command = new StoreEventCommand($data);

            $result = $handler->handle($command);

            $this->sendResponse(201, [
                'status' => 'success',
                'message' => 'Event saved successfully',
                'event' => $result
            ]);
        } catch (Exception $e) {
            $this->sendResponse(400, ['error' => $e->getMessage()]);
        }
    }

    public function handleStatistics(?string $matchId, ?string $teamId): void
    {
        if (!$matchId) {
            $this->sendResponse(400, ['error' => 'match_id is required']);
            return;
        }

        try {
            $queryHandler = new GetStatisticsQueryHandler($this->statsStore);
            $query = new GetStatisticsQuery($matchId, $teamId);
            $result = $queryHandler->ask($query);

            $this->sendResponse(200, $result);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => $e->getMessage()]);
        }
    }
}
