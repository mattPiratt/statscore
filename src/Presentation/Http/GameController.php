<?php

namespace App\Presentation\Http;

use App\Shared\Infrastructure\HttpJsonController;
use App\Statistics\Application\Command\CommandHandlerInterface;
use App\Statistics\Application\Command\StoreEventCommand;
use App\Statistics\Application\Query\GetStatisticsQuery;
use App\Statistics\Application\Query\QueryHandlerInterface;
use Exception;

class GameController extends HttpJsonController
{
    public function __construct(
        private readonly CommandHandlerInterface $storeEventCommandHandler,
        private readonly QueryHandlerInterface $getStatisticsQueryHandler
    ) {
    }

    public function handleEvent(): void
    {
        try {
            $data = $this->getJsonData();
            // TODO: convert data into DTO

            $command = new StoreEventCommand($data);
            $result = $this->storeEventCommandHandler->handle($command);

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
            $query = new GetStatisticsQuery($matchId, $teamId);
            $result = $this->getStatisticsQueryHandler->ask($query);

            $this->sendResponse(200, $result);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => $e->getMessage()]);
        }
    }
}
