<?php

namespace App\Events\Game;

use App\Events\Event;

class GameUnCompleted extends Event
{
    public $gameId;
    public $winningTeam;
    public $blackPointTotal;
    public $whitePointTotal;
    public $winningPoint;

    public function __construct($gameId, $winningTeam, $blackPointTotal, $whitePointTotal, $winningPoint)
    {
        $this->gameId = $gameId;
        $this->setAggregateId($this->gameId);
        $this->winningTeam = $winningTeam;
        $this->blackPointTotal = $blackPointTotal;
        $this->whitePointTotal = $whitePointTotal;
        $this->winningPoint = $winningPoint;
    }
}
