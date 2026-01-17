<?php

namespace App\Statistics\Application\Command;

use App\Statistics\Domain\Event\GameEventInterface;
use App\Statistics\Domain\Factory\GameEventFactoryInterface;
use App\Statistics\Domain\Model\TeamStatistics;
use App\Statistics\Domain\Repository\EventsStoreInterface;
use App\Statistics\Domain\Repository\StatisticsStoreInterface;
use App\Statistics\Domain\Strategy\StatisticsUpdateStrategyInterface;

class StoreEventCommandHandler implements CommandHandlerInterface
{

    /**
     * @param StatisticsUpdateStrategyInterface[] $strategies
     */
    public function __construct(
        private readonly EventsStoreInterface $eventsStore,
        private readonly StatisticsStoreInterface $statisticsStore,
        private readonly ?GameEventFactoryInterface $eventFactory,
        private readonly array $strategies = []
    ) {
    }

    public function handle(CommandInterface $command): array
    {
        $event = $this->eventFactory->create($command->data);

        $this->eventsStore->save($event);

        $teamStatistics = $this->statisticsStore->getTeamStatistics($event->matchId(), $event->teamId());

        $this->updateStatistics($event, $teamStatistics);

        $this->statisticsStore->save($teamStatistics);

        return $event->toArray();
    }

    private function updateStatistics(
        GameEventInterface $event,
        TeamStatistics $teamStatistics
    ): void {
        foreach ($this->strategies as $strategy) {
            if ($strategy->canHandle($event)) {
                $strategy->update($event, $teamStatistics);
            }
        }
    }
}
