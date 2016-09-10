<?php

namespace App\Aggregates;

use App\Events\Game\GameAdded;
use App\Events\Game\GameCompleted;
use App\Events\Game\GameEdited;
use App\Events\Game\PointAdded;
use App\Events\Game\PointRemoved;
use App\Events\Game\TeamPlayerAdded;
use App\Events\Game\TeamPlayerRemoved;
use App\Infrastructure\Aggregate\AggregateException;
use App\Infrastructure\Aggregate\AggregateRoot;

class Game extends AggregateRoot
{
    private $gameDate;
    private $start;
    private $end;
    private $playoff;
    private $season;
    private $gameNumber;

    private $players = [];
    private $points = [];
    private $blackGoalie;
    private $whiteGoalie;

    private $winningTeam;

    const BLACK_TEAM = "black";
    const WHITE_TEAM = "white";

    const GOALIE = "g";
    const PLAYER = "p";

    /**
     * @return mixed
     */
    public function getGameDate()
    {
        return $this->gameDate;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @return mixed
     */
    public function getPlayoff()
    {
        return $this->playoff;
    }

    /**
     * @return mixed
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * @return mixed
     */
    public function getGameNumber()
    {
        return $this->gameNumber;
    }



    public static function createGame($gameDate, $start, $end, $playoff, $season, $gameNumber)
    {
        $game = new Game();
        $gameId = $game->generateAggregateId();
        $game->apply(
            new GameAdded($gameId, $gameDate, $start, $end, $playoff, $season, $gameNumber)
        );
        return $game;
    }

    public function applyGameAdded(GameAdded $event)
    {
        $this->gameDate = $event->gameDate;
        $this->setAggregateId($event->getAggregateId());
        $this->start = $event->start;
        $this->end = $event->end;
        $this->playoff = $event->playoff;
        $this->season = $event->season;
        $this->gameNumber = $event->gameNumber;
        $this->players[Game::BLACK_TEAM] = [];
        $this->players[Game::WHITE_TEAM] = [];
        $this->points[Game::BLACK_TEAM] = [];
        $this->points[Game::WHITE_TEAM] = [];
    }

    public function editGame($gameDate, $start, $end, $playoff, $season, $gameNumber)
    {

        foreach ($this->points as $teamColor => $points) {
            foreach ($points as $pointNumber => $point) {
                $this->apply(
                    new PointRemoved($this->getAggregateId(), $teamColor, $pointNumber, $point['g'], $point['a'])
                );
            }
        }
        foreach ($this->players as $color => $players) {
            foreach ($players as $playerId) {
                $this->apply(
                    new TeamPlayerRemoved($this->getAggregateId(), $color, $playerId, Game::PLAYER)
                );
            }
        }



        $this->apply(
            new TeamPlayerRemoved($this->getAggregateId(), Game::BLACK_TEAM, $this->blackGoalie, Game::GOALIE)
        );

        $this->apply(
            new TeamPlayerRemoved($this->getAggregateId(), Game::WHITE_TEAM, $this->whiteGoalie, Game::GOALIE)
        );

        $this->apply(
            new GameEdited($this->getAggregateId(), $gameDate, $start, $end, $playoff, $season, $gameNumber)
        );
    }

    public function applyGameEdited(GameEdited $event)
    {
        $this->gameDate = $event->gameDate;
        $this->start = $event->start;
        $this->end = $event->end;
        $this->playoff = $event->playoff;
        $this->season = $event->season;
        $this->gameNumber = $event->gameNumber;
        $this->players[Game::BLACK_TEAM] = [];
        $this->players[Game::WHITE_TEAM] = [];
        $this->points[Game::BLACK_TEAM] = [];
        $this->points[Game::WHITE_TEAM] = [];
    }

    public function addBlackPlayer($playerId)
    {
        //todo check if player exists
        $this->apply(
            new TeamPlayerAdded($this->getAggregateId(), Game::BLACK_TEAM, $playerId, Game::PLAYER)
        );
    }

    public function addWhitePlayer($playerId)
    {
        //todo check if player exists
        $this->apply(
            new TeamPlayerAdded($this->getAggregateId(), Game::WHITE_TEAM, $playerId, Game::PLAYER)
        );
    }

    public function addBlackGoalie($playerId)
    {
        if (is_null($playerId)) {
            throw new AggregateException("You have not selected a Black Goalie");
        }
        $this->apply(
            new TeamPlayerAdded($this->getAggregateId(), Game::BLACK_TEAM, $playerId, Game::GOALIE)
        );
    }

    public function addWhiteGoalie($playerId)
    {
        if (is_null($playerId)) {
            throw new AggregateException("You have not selected a White Goalie");
        }
        $this->apply(
            new TeamPlayerAdded($this->getAggregateId(), Game::WHITE_TEAM, $playerId, Game::GOALIE)
        );
    }

    public function applyTeamPlayerAdded(TeamPlayerAdded $event)
    {
        $this->players[$event->teamColour][] = $event->playerId;
        if ($event->position === Game::GOALIE) {
            if ($event->teamColour === Game::BLACK_TEAM) {
                $this->blackGoalie = $event->playerId;
            } else {
                $this->whiteGoalie = $event->playerId;
            }
        }
    }

    public function applyTeamPlayerRemoved(TeamPlayerRemoved $event)
    {

        $foundKey = '';
        foreach ($this->players[$event->teamColour] as $key => $player) {
            if ($player == $event->playerId) {
                $foundKey = $key;
            }
        }
        unset($this->players[$event->teamColour][$foundKey]);
        if ($event->position === Game::GOALIE) {
            if ($event->teamColour === Game::BLACK_TEAM) {
                $this->blackGoalie = null;
            } else {
                $this->whiteGoalie = null;
            }
        }
    }

    public function addBlackPoint($pointNumber, $goalPlayerId, $assistPlayerId)
    {
        if (!in_array($goalPlayerId, $this->players[Game::BLACK_TEAM])) {
            throw new AggregateException("The goal player " . $goalPlayerId . " has not been added to the Black team.");
        } elseif ((trim($assistPlayerId) != "") && !in_array($assistPlayerId, $this->players[Game::BLACK_TEAM])) {
            throw new AggregateException("The assist player has not been added to the Black team.");
        } else {
            $this->apply(
                new PointAdded($this->getAggregateId(), Game::BLACK_TEAM, $pointNumber, $goalPlayerId, $assistPlayerId)
            );
        }
    }
    public function addWhitePoint($pointNumber, $goalPlayerId, $assistPlayerId)
    {
        if (!in_array($goalPlayerId, $this->players[Game::WHITE_TEAM])) {
            throw new AggregateException("The goal player " . $goalPlayerId . " has not been added to the White team.");
        } elseif ((trim($assistPlayerId) != "") && !in_array($assistPlayerId, $this->players[Game::WHITE_TEAM])) {
            throw new AggregateException("The assist player has not been added to the White team.");
        } else {
            $this->apply(
                new PointAdded($this->getAggregateId(), Game::WHITE_TEAM, $pointNumber, $goalPlayerId, $assistPlayerId)
            );
        }
    }

    public function completeGame()
    {
        $blackPoints = $this->getBlackPointTotal();
        $whitePointTotal = $this->getWhitePointTotal();
        $winningTeam = GAME::BLACK_TEAM;
        $winningPoint = $blackPoints;
        if ($whitePointTotal > $blackPoints) {
            $winningTeam = GAME::WHITE_TEAM;
            $winningPoint = $whitePointTotal;
        }

        if (is_null($this->blackGoalie)) {
            throw new AggregateException('You have not set a black goalie');
        }

        if (is_null($this->whiteGoalie)) {
            throw new AggregateException('You have not set a white goalie');
        }

        if (($this->season == 1) && ($this->gameNumber == 3)){
            /**
             * Why is this here? Game 3 of season 1 (which would have been the first meeting of SHL) did not complete the
             * 10 goal rule (First to 10, must win by 2 goals). I believe we enforced the rule soon after that.
             */
        } else {
            if ($winningPoint < 10) {
                throw new AggregateException('No team has won the game by getting at least 10 goals.');
            }
        }

        $this->apply(new GameCompleted($this->getAggregateId(), $winningTeam, $blackPoints, $whitePointTotal, $winningPoint));
    }

    public function applyGameCompleted(GameCompleted $event)
    {
        $this->winningTeam = $event->winningTeam;
    }

    public function applyPointAdded(PointAdded $event)
    {
        $this->points[$event->teamColour][$event->pointNumber] = ['g' => $event->goalPlayerId, 'a' => $event->assistPlayerId];
    }

    public function applyPointRemoved(PointRemoved $event)
    {
        unset($this->points[$event->teamColour][$event->pointNumber]);
    }

    public function getBlackPointTotal()
    {
        return $this->getPointTotal(Game::BLACK_TEAM);
    }

    public function getWhitePointTotal()
    {
        return $this->getPointTotal(Game::WHITE_TEAM);
    }

    private function getPointTotal($teamColour)
    {
        return count($this->points[$teamColour]);
    }

    /**
     * @return mixed
     */
    public function getWinningTeam()
    {
        return $this->winningTeam;
    }


}
