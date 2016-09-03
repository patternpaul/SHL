<?php

class PlayerTest extends \App\Infrastructure\Test\TestCaseCore
{

    public function test_regex()
    {
        //[playerId=f0925102-a7d7-42f1-bfb4-d19c0254f4ea]: 1000 Points in 262 games.
        preg_match('(\[playerId=([a-zA-Z0-9\-]+)\])', "[playerId=f0925102-a7d7-42f1-bfb4-d19c0254f4ea]: 1000 Points in 262 games.", $matches);
        $this->assertEquals('f0925102-a7d7-42f1-bfb4-d19c0254f4ea', $matches[1]);
    }


    public function test_add_player()
    {
        $firstName = "FirstName";
        $lastName = "LastName";

        $command = new \App\Commands\Player\AddPlayer($firstName, $lastName);
        $playerId = $this->dispatch($command);

        /** @var \App\Aggregates\Player $playerAgg */
        $playerAgg = $this->aggregateRepository->get($playerId);
        $player = $this->players->getById($playerId);

        $this->assertEquals($firstName, $player["firstName"]);
        $this->assertEquals($lastName, $player["lastName"]);

        $this->assertEquals($firstName, $playerAgg->getFirstName());
        $this->assertEquals($lastName, $playerAgg->getLastName());
    }


    public function test_edit_player()
    {
        $firstName = "FirstName";
        $lastName = "LastName";

        $command = new \App\Commands\Player\AddPlayer($firstName, $lastName);
        $playerId = $this->dispatch($command);


        $firstName = "FirstNamez";
        $lastName = "LastNamez";

        $command = new \App\Commands\Player\EditPlayer($playerId, $firstName, $lastName);
        $playerId = $this->dispatch($command);


        /** @var \App\Aggregates\Player $playerAgg */
        $playerAgg = $this->aggregateRepository->get($playerId);
        $player = $this->players->getById($playerId);

        $this->assertEquals($firstName, $player["firstName"]);
        $this->assertEquals($lastName, $player["lastName"]);

        $this->assertEquals($firstName, $playerAgg->getFirstName());
        $this->assertEquals($lastName, $playerAgg->getLastName());
    }
}