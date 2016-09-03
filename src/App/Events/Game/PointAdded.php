<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 2016-08-03
 * Time: 8:36 PM
 */
namespace App\Events\Game;

use App\Events\Event;

class PointAdded extends Event
{
    public $gameId;
    public $teamColour;
    public $pointNumber;
    public $goalPlayerId;
    public $assistPlayerId;
    
    public function __construct($gameId, $teamColour, $pointNumber, $goalPlayerId, $assistPlayerId)
    {
        $this->gameId = $gameId;
        $this->setAggregateId($this->gameId);
        $this->teamColour = $teamColour;
        $this->pointNumber = $pointNumber;
        $this->goalPlayerId = $goalPlayerId;
        $this->assistPlayerId = $assistPlayerId;
    }


}
