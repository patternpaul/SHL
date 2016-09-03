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

class AddPlayer extends Command
{
    private $firstName;
    private $lastName;

    public function __construct($firstName, $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }


    public function handle(IAggregateRepository $aggregateRepository)
    {
        $player = Player::createPlayer(
            $this->firstName,
            $this->lastName
        );
        $aggregateRepository->save($player);

        return $player->getAggregateId();
    }
}
