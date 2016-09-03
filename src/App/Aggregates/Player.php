<?php

namespace App\Aggregates;

use App\Events\Player\PlayerAdded;
use App\Events\Player\PlayerEdited;
use App\Infrastructure\Aggregate\AggregateRoot;

class Player extends AggregateRoot
{
    private $firstName;
    private $lastName;

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    public function editPlayer($firstName, $lastName)
    {
        $this->apply(new PlayerEdited($this->getAggregateId(), $firstName, $lastName));
    }


    public function applyPlayerEdited(PlayerEdited $event)
    {
        $this->firstName = $event->firstName;
        $this->lastName = $event->lastName;
    }

    public function applyPlayerAdded(PlayerAdded $event)
    {
        $this->setAggregateId($event->getAggregateId());
        $this->firstName = $event->firstName;
        $this->lastName = $event->lastName;
    }


    public static function createPlayer($firstName, $lastName)
    {
        $player = new Player();
        $playerId = $player->generateAggregateId();
        $player->apply(new PlayerAdded($playerId, $firstName, $lastName));
        return $player;
    }
}
