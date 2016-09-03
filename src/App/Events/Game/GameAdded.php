<?php

namespace App\Events\Game;

use App\Events\Event;

class GameAdded extends Event
{
    public $gameId;
    public $gameDate;
    public $start;
    public $end;
    public $playoff;
    public $season;
    public $gameNumber;

    public function __construct($gameId, $gameDate, $start, $end, $playoff, $season, $gameNumber)
    {
        $this->gameId = $gameId;
        $this->setAggregateId($this->gameId);
        $this->gameDate = $gameDate;
        $this->start = $start;
        $this->end = $end;
        $this->playoff = $playoff;
        $this->season = $season;
        $this->gameNumber = $gameNumber;
    }
}
