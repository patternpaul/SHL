<?php

namespace App\Events\Player;

use App\Events\Event;

class PlayerEdited extends Event
{
    public $playerId;
    public $firstName;
    public $lastName;

    public function __construct($playerId, $firstName, $lastName)
    {
        $this->playerId = $playerId;
        $this->setAggregateId($this->playerId);
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}
