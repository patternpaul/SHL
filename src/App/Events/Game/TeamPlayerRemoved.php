<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 2016-08-03
 * Time: 8:23 PM
 */
namespace App\Events\Game;

use App\Aggregates\Game;
use App\Events\Event;

class TeamPlayerRemoved extends Event
{
    public $gameId;
    public $teamColour;
    public $playerId;
    public $position;

    public function __construct($gameId, $teamColour, $playerId, $position)
    {
        $this->gameId = $gameId;
        $this->setAggregateId($this->gameId);
        $this->teamColour = $teamColour;
        $this->playerId = $playerId;
        $this->position = $position;
    }
}
