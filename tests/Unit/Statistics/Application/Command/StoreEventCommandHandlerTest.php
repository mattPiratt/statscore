<?php

namespace Tests\Unit\Statistics\Application\Command;

use App\Shared\Infrastructure\SystemClock;
use App\Statistics\Application\Command\StoreEventCommand;
use App\Statistics\Application\Command\StoreEventCommandHandler;
use App\Statistics\Domain\Factory\GameEventFactory;
use App\Statistics\Domain\Repository\EventsStoreInterface;
use App\Statistics\Domain\Repository\StatisticsStoreInterface;
use App\Statistics\Domain\Strategy\FoulStatisticsUpdateStrategy;
use App\Statistics\Domain\Strategy\GoalStatisticsUpdateStrategy;
use App\Statistics\Infrastructure\Persistence\EventsStore;
use App\Statistics\Infrastructure\Persistence\StatisticsStore;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class StoreEventCommandHandlerTest extends TestCase
{
    private string $testFile;
    private string $testStatsFile;
    private EventsStoreInterface $eventsStore;
    private StatisticsStoreInterface $statisticsStore;
    private StoreEventCommandHandler $handler;

    protected function setUp(): void
    {
        $this->testFile = sys_get_temp_dir() . '/test_events_' . uniqid() . '.txt';
        $this->testStatsFile = sys_get_temp_dir() . '/test_stats_' . uniqid() . '.txt';

        $this->eventsStore = new EventsStore($this->testFile);
        $this->statisticsStore = new StatisticsStore($this->testStatsFile);
        $this->handler = new StoreEventCommandHandler(
            $this->eventsStore,
            $this->statisticsStore,
            new GameEventFactory(new SystemClock()),
            [
                new GoalStatisticsUpdateStrategy(),
                new FoulStatisticsUpdateStrategy(),
            ]
        );
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
        if (file_exists($this->testStatsFile)) {
            unlink($this->testStatsFile);
        }
    }

    public function testHandleGoalEvent(): void
    {
        $eventData = [
            'type' => 'goal',
            'player' => 'John Doe',
            'team_id' => 'team_a',
            'match_id' => 'match_1',
            'minute' => 23,
            'second' => 34
        ];

        $command = new StoreEventCommand($eventData);
        $result = $this->handler->handle($command);

        $this->assertEquals('goal', $result['type']);
        $this->assertEquals('match_1', $result['match_id']);
        $this->assertArrayHasKey('occurred_at', $result);
    }

    public function testHandleGoalEventUpdatesStatistics(): void
    {
        $eventData = [
            'type' => 'goal',
            'player' => 'John Doe',
            'assistant' => 'Jane Smith',
            'team_id' => 'team_a',
            'match_id' => 'match_1',
            'minute' => 23,
            'second' => 34
        ];

        $command = new StoreEventCommand($eventData);
        $this->handler->handle($command);

        $teamStats = $this->statisticsStore->getTeamStatistics('match_1', 'team_a');
        $this->assertArrayHasKey('goals', $teamStats);
        $this->assertEquals(1, $teamStats['goals']);
        $this->assertArrayHasKey('assists', $teamStats);
        $this->assertEquals(1, $teamStats['assists']);
    }


    public function testHandleGoalEventWithoutAssistant(): void
    {
        $eventData = [
            'type' => 'goal',
            'player' => 'John Doe',
            'team_id' => 'team_a',
            'match_id' => 'match_1',
            'minute' => 23,
            'second' => 34
        ];

        $command = new StoreEventCommand($eventData);
        $this->handler->handle($command);

        $teamStats = $this->statisticsStore->getTeamStatistics('match_1', 'team_a');
        $this->assertEquals(1, $teamStats['goals']);
        $this->assertArrayNotHasKey('assists', $teamStats);
    }

    public function testHandleGoalEventWithoutRequiredFields(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('match_id, team_id and player are required for goal events');

        $eventData = [
            'type' => 'goal',
            'team_id' => 'team_a',
            'match_id' => 'match_1'
            // Missing player
        ];

        $command = new StoreEventCommand($eventData);
        $this->handler->handle($command);
    }

    public function testHandleFoulEventUpdatesStatistics(): void
    {
        $eventData = [
            'type' => 'foul',
            'player' => 'William Saliba',
            'affected_player' => 'Erling Haaland',
            'team_id' => 'arsenal',
            'match_id' => 'm1',
            'minute' => 45,
            'second' => 34
        ];

        $command = new StoreEventCommand($eventData);
        $this->handler->handle($command);
        $teamStats = $this->statisticsStore->getTeamStatistics('m1', 'arsenal');

        $this->assertArrayHasKey('fouls', $teamStats);
        $this->assertEquals(1, $teamStats['fouls']);
    }

    public function testHandleFoulEventWithoutRequiredFields(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('match_id, team_id, player and affected_player are required for foul events');

        $eventData = [
            'type' => 'foul',
            'player' => 'William Saliba',
            'team_id' => 'arsenal',
            'match_id' => 'm1'
            // Missing affected_player
        ];

        $command = new StoreEventCommand($eventData);
        $this->handler->handle($command);
    }

    public function testHandleMultipleFoulEventsIncrementsStatistics(): void
    {
        $eventData1 = [
            'type' => 'foul',
            'player' => 'John Doe',
            'affected_player' => 'Affected 1',
            'team_id' => 'team_a',
            'match_id' => 'match_1',
            'minute' => 15,
            'second' => 34
        ];

        $eventData2 = [
            'type' => 'foul',
            'player' => 'Jane Smith',
            'affected_player' => 'Affected 2',
            'team_id' => 'team_a',
            'match_id' => 'match_1',
            'minute' => 30,
            'second' => 34
        ];

        $command1 = new StoreEventCommand($eventData1);
        $command2 = new StoreEventCommand($eventData2);
        $this->handler->handle($command1);
        $this->handler->handle($command2);

        // Check that statistics were incremented correctly
        $teamStats = $this->statisticsStore->getTeamStatistics('match_1', 'team_a');
        $this->assertEquals(2, $teamStats['fouls']);
    }

    public function testEventIsSavedToStorage(): void
    {
        $eventData = [
            'type' => 'goal',
            'player' => 'Jane Smith',
            'team_id' => 'team_a',
            'match_id' => 'match_1',
            'minute' => 10
        ];

        $command = new StoreEventCommand($eventData);
        $this->handler->handle($command);

        $content = file_get_contents($this->testFile);
        $savedEvents = array_filter(explode(PHP_EOL, trim($content)));
        $this->assertCount(1, $savedEvents);
        $this->assertStringContainsString('"type":"goal"', $savedEvents[0]);
    }
}
