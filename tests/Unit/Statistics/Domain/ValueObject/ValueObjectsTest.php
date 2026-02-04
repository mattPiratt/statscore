<?php

namespace Tests\Unit\Statistics\Domain\ValueObject;

use App\Statistics\Domain\ValueObject\MatchId;
use App\Statistics\Domain\ValueObject\PlayerName;
use App\Statistics\Domain\ValueObject\TeamId;
use PHPUnit\Framework\TestCase;

class ValueObjectsTest extends TestCase
{
    public function test_match_id(): void
    {
        $id = 'm1';
        $matchId = new MatchId($id);
        $this->assertEquals($id, $matchId->value());
    }

    public function test_team_id(): void
    {
        $id = 'arsenal';
        $teamId = new TeamId($id);
        $this->assertEquals($id, $teamId->value());
    }

    public function test_player(): void
    {
        $name = 'William Saliba';
        $player = new PlayerName($name);
        $this->assertEquals($name, $player->value());
    }
}
