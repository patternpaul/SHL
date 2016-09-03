<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 2016-08-03
 * Time: 6:04 PM
 */
namespace App\Commands\Player;

use App\Aggregates\Player;
use App\Commands\Command;
use App\Infrastructure\Aggregate\IAggregateRepository;

class EditPlayer extends Command
{
    private $playerId;
    private $firstName;
    private $lastName;

    public function __construct($playerId, $firstName, $lastName)
    {
        $this->playerId = $playerId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }


    public function handle(IAggregateRepository $aggregateRepository)
    {
        /** @var Player $player */
        $player = $aggregateRepository->get($this->playerId);
        $player->editPlayer($this->firstName, $this->lastName);
        $aggregateRepository->save($player);

        return $player->getAggregateId();
    }
}
