<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 2016-08-03
 * Time: 8:06 PM
 */
namespace App\Commands\Game;

use App\Aggregates\Game;
use App\Commands\Command;
use App\Infrastructure\Aggregate\IAggregateRepository;

class EditFullGame extends Command
{
    private $gameId;
    private $gameDate;
    private $start;
    private $end;
    private $playoff;
    private $season;
    private $gameNumber;

    private $blackPlayers = [];
    private $whitePlayers = [];

    private $blackGoalie;
    private $whiteGoalie;

    private $blackPoints = [];
    private $whitePoints = [];

    public function __construct($gameId, $gameDate, $start, $end, $playoff, $season, $gameNumber)
    {
        $this->gameId = $gameId;
        $this->gameDate = $gameDate;
        $this->start = $start;
        $this->end = $end;
        $this->playoff = $playoff;
        $this->season = $season;
        $this->gameNumber = $gameNumber;
    }

    public function addBlackPlayer($playerId)
    {
        $this->blackPlayers[] = $playerId;
    }

    public function addWhitePlayer($playerId)
    {
        $this->whitePlayers[] = $playerId;
    }

    public function addBlackGoalie($playerId)
    {
        $this->blackGoalie = $playerId;
    }

    public function addWhiteGoalie($playerId)
    {
        $this->whiteGoalie = $playerId;
    }


    public function addBlackPoint($pointNumber, $goalPlayerId, $assistPlayerId)
    {
        $this->blackPoints[$pointNumber] = [
            'g' => $goalPlayerId,
            'a' => $assistPlayerId
        ];
    }
    public function addWhitePoint($pointNumber, $goalPlayerId, $assistPlayerId)
    {
        $this->whitePoints[$pointNumber] = [
            'g' => $goalPlayerId,
            'a' => $assistPlayerId
        ];
    }

    public function handle(IAggregateRepository $aggregateRepository)
    {

        /** @var Game $game */
        $game = $aggregateRepository->get($this->gameId);
        $game->editGame(
            $this->gameDate,
            $this->start,
            $this->end,
            $this->playoff,
            $this->season,
            $this->gameNumber
        );



        foreach ($this->blackPlayers as $blackPlayer) {
            $game->addBlackPlayer($blackPlayer);
        }

        foreach ($this->whitePlayers as $whitePlayer) {
            $game->addWhitePlayer($whitePlayer);
        }
        
        $game->addWhiteGoalie($this->whiteGoalie);
        $game->addBlackGoalie($this->blackGoalie);
        

        foreach ($this->blackPoints as $index => $blackPoint) {
            $game->addBlackPoint($index, $blackPoint['g'], $blackPoint['a']);
        }

        foreach ($this->whitePoints as $index => $whitePoint) {
            $game->addWhitePoint($index, $whitePoint['g'], $whitePoint['a']);
        }

        $game->completeGame();

        $aggregateRepository->save($game);

        return $game->getAggregateId();
    }
}

