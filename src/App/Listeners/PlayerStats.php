<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 2016-08-03
 * Time: 6:25 PM
 */
namespace App\Listeners;

use App\Aggregates\Game;
use App\Events\Game\GameAdded;
use App\Events\Game\GameCompleted;
use App\Events\Game\PointAdded;
use App\Events\Game\PointRemoved;
use App\Events\Game\TeamPlayerAdded;
use App\Events\Game\TeamPlayerRemoved;
use App\Events\Player\PlayerAdded;
use App\Events\Player\PlayerEdited;
use App\Infrastructure\Database\IRedisDB;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;

class PlayerStats extends Listener
{
    private $redis;

    public function __construct(IRedisDB $redis)
    {
        $this->redis = $redis;
    }

    public function getById($key)
    {
        $obj = $this->redis->hgetall('player:' . $key);

        return $obj;
    }

    private function addRows($firstRow, $secondRow)
    {
        $newRow = [];

        foreach ($firstRow as $key => $data) {
            if(isset($secondRow[$key])) {
                $newRow[$key] = $firstRow[$key] + $secondRow[$key];
            } else {
                $newRow[$key] = $firstRow[$key];
            }
        }

        return $newRow;
    }

    public function getCalcStatLines($selectedSeason, $selectedPlayoff)
    {
        $seasons = $this->redis->hgetall('stats:seasons:');
        $statLines = [];
        $calcLines = [];
        foreach ($seasons as $season) {
            if(($selectedSeason != 'all') && (intval($selectedSeason) != $season)) {
                continue;
            }


            if(($selectedPlayoff === 'all') || ($selectedPlayoff === 0) || ($selectedPlayoff === '0')) {
                $seasonPlayers = array_unique($this->redis->hvals('stats:seasons:'.$season.":playoffs:0:players"));
                foreach ($seasonPlayers as $seasonPlayer) {
                    $seasonPlayerData = $this->getPlayerStatLine($seasonPlayer, $season, 0);
                    if(isset($statLines[$seasonPlayer])) {
                        $statLines[$seasonPlayer] = $this->addRows($seasonPlayerData, $statLines[$seasonPlayer]);
                    } else {
                        $statLines[$seasonPlayer] = $seasonPlayerData;
                    }
                }
            }


            if(($selectedPlayoff === 'all') || ($selectedPlayoff === 1) || ($selectedPlayoff === '1')) {

                $seasonPlayers = array_unique($this->redis->hvals('stats:seasons:'.$season.":playoffs:1:players"));
                foreach ($seasonPlayers as $seasonPlayer) {
                    $seasonPlayerData = $this->getPlayerStatLine($seasonPlayer, $season, 1);
                    if(isset($statLines[$seasonPlayer])) {
                        $statLines[$seasonPlayer] = $this->addRows($seasonPlayerData, $statLines[$seasonPlayer]);
                    } else {
                        $statLines[$seasonPlayer] = $seasonPlayerData;
                    }
                }
            }
        }

        foreach ($statLines as $key => $statLine) {
            $player = $this->redis->hgetall($this->getBaseKey() . ':player:' . $key);
            $statData = $this->calcStats($statLine);
            $statData['playerId'] = $player['id'];
            $statData['firstName'] = $player['firstName'];
            $statData['lastName'] = $player['lastName'];
            $statData['shortName'] = substr($statData['firstName'],0,1).'.'.$player['lastName'];
            $calcLines[$key] = $statData;
        }

        $calcLines = array_sort($calcLines, function ($value) {
            return $value['shortName'];
        });


        return $calcLines;
    }



    public function getCalcPlayerStatLine($playerId, $season, $playoff)
    {
        return $this->calcStats($this->getPlayerStatLine($playerId, $season, $playoff));
    }

    public function getPlayerStats($playerId, $playoff)
    {
        $seasons = $this->redis->hgetall('stats:seasons:');
        $statLines = [];
        $calcLines = [];
        $allLine = [];

        foreach ($seasons as $season) {
            $seasonPlayers = array_unique($this->redis->hvals('stats:seasons:'.$season.":playoffs:".$playoff.":players"));
            foreach ($seasonPlayers as $seasonPlayer) {
                if ($seasonPlayer == $playerId) {
                    $seasonPlayerData = $this->getPlayerStatLine($seasonPlayer, $season, $playoff);
                    $allLine = $this->addRows($seasonPlayerData, $allLine);
                    $seasonPlayerData['playerId'] = $seasonPlayer;
                    $seasonPlayerData['season'] = $season;
                    $statLines[$season] = $seasonPlayerData;
                }
            }
        }

        foreach ($statLines as $key => $statLine) {
            $statData = $this->calcStats($statLine);
            $calcLines[$key] = $statData;
        }


        $calcLines = array_sort($calcLines, function ($value) {
            return str_pad($value['season'], 2, "0", STR_PAD_LEFT);
        });

        if (count($allLine) > 0) {
            $calcLines['all'] = $this->calcStats($allLine);
        }

        return $calcLines;
    }

    private function calcStats($statObj)
    {
        $statObj['points'] = $statObj['goals'] + $statObj['assists'];
        $statObj['goalsPerGame'] = round ($statObj['goals']/$statObj['gamesPlayed'], 2);
        $statObj['assistsPerGame'] = round ($statObj['assists']/$statObj['gamesPlayed'], 2);
        $statObj['pointsPerGame'] = round ($statObj['points']/$statObj['gamesPlayed'], 2);
        $statObj['teamGoalsPercentage'] = 0;
        if ($statObj['teamGoals'] > 0) {
            $statObj['teamGoalsPercentage'] = round ($statObj['goals']/$statObj['teamGoals']*100, 2);
        }
        $statObj['plusMinus'] = $statObj['wins'] - $statObj['losses'];
        $statObj['winPercentage'] = round($statObj['wins']/($statObj['wins'] + $statObj['losses'])*100, 2);


        return $statObj;
    }

    public function onPlayerEdited(PlayerEdited $event)
    {
        $obj = [];
        $obj["firstName"] = $event->firstName;
        $obj["lastName"] = $event->lastName;

        $this->redis->hmset($this->getBaseKey() . ':player:' . $event->getAggregateId(), $obj);
    }

    public function onPlayerAdded(PlayerAdded $event)
    {
        $obj = [];
        $obj["id"] = $event->getAggregateId();
        $obj["firstName"] = $event->firstName;
        $obj["lastName"] = $event->lastName;

        $this->redis->hmset($this->getBaseKey() . ':player:' . $event->getAggregateId(), $obj);
    }

    public function onTeamPlayerAdded(TeamPlayerAdded $event)
    {
        $game = $this->redis->hgetall($this->getBaseKey() . ':game:' . $event->gameId);

        $this->redis->hset(
            $this->getBaseKey() . ':game:' . $event->gameId.':player-positions',
            $event->playerId,
            $event->position
        );



        if ($event->position == Game::PLAYER) {

            $this->redis->hset(
                'stats:seasons:'.$game['season'].":playoffs:".$game['playoff'].":players",
                $game['season'].$game['playoff'].$game['gameNumber'].$event->playerId,
                $event->playerId
            );

            $this->redis->hset(
                'stats:seasons:'.$game['season'].":playoffs:all:players",
                $game['season'].$game['playoff'].$game['gameNumber'].$event->playerId,
                $event->playerId
            );


            $this->redis->hset(
                $this->getBaseKey() . ':game:' . $event->gameId.':players',
                $event->playerId,
                $event->teamColour
            );

            $obj = $this->getPlayerStatLine($event->playerId, $game['season'], $game['playoff']);
            $obj['gamesPlayed'] = $obj['gamesPlayed'] + 1;
            $this->storePlayerStatLine($event->playerId, $game['season'], $game['playoff'], $obj);

        }
    }


    public function onTeamPlayerRemoved(TeamPlayerRemoved $event)
    {
        $game = $this->redis->hgetall($this->getBaseKey() . ':game:' . $event->gameId);

        $this->redis->hdel(
            $this->getBaseKey() . ':game:' . $event->gameId.':player-positions',
            $event->playerId
        );



        if ($event->position == Game::PLAYER) {

            $game = $this->redis->hgetall($this->getBaseKey() . ':game:' . $event->gameId);
            $blackPoints = $game[Game::BLACK_TEAM.'TotalPoints'];
            $whitePoints = $game[Game::WHITE_TEAM.'TotalPoints'];

            $obj = $this->getPlayerStatLine($event->playerId, $game['season'], $game['playoff']);


            if (Game::WHITE_TEAM == $event->teamColour) {
                $obj['teamGoals'] = $obj['teamGoals'] - $whitePoints;
            } else {
                $obj['teamGoals'] = $obj['teamGoals'] - $blackPoints;
            }


            if ($event->teamColour == $game['winner']) {
                $obj['wins'] = $obj['wins'] - 1;
            } else {
                $obj['losses'] = $obj['losses'] - 1;
            }
            $obj['gamesPlayed'] = $obj['gamesPlayed'] - 1;

            $this->storePlayerStatLine($event->playerId, $game['season'], $game['playoff'], $obj);


            $this->redis->hdel(
                'stats:seasons:'.$game['season'].":playoffs:".$game['playoff'].":players",
                $game['season'].$game['playoff'].$game['gameNumber'].$event->playerId
            );

            $this->redis->hdel(
                'stats:seasons:'.$game['season'].":playoffs:all:players",
                $game['season'].$game['playoff'].$game['gameNumber'].$event->playerId
            );



            $this->redis->hdel(
                $this->getBaseKey() . ':game:' . $event->gameId.':players',
                $event->playerId
            );
        }
    }

    public function onPointRemoved(PointRemoved $event)
    {
        $game = $this->redis->hgetall($this->getBaseKey() . ':game:' . $event->gameId);

        $goalPosition = $this->redis->hget($this->getBaseKey() . ':game:' . $event->gameId.':player-positions', $event->goalPlayerId);


        if ($goalPosition === Game::PLAYER) {
            $obj = $this->getPlayerStatLine($event->goalPlayerId, $game['season'], $game['playoff']);
            $obj['goals'] = $obj['goals'] - 1;
            $this->storePlayerStatLine($event->goalPlayerId, $game['season'], $game['playoff'], $obj);

            if ($game['winningPoint'] == $event->pointNumber && $game['winner'] == $event->teamColour) {
                $obj = $this->getPlayerStatLine($event->goalPlayerId, $game['season'], $game['playoff']);
                $obj['gameWinningGoals'] = $obj['gameWinningGoals'] - 1;
                $this->storePlayerStatLine($event->goalPlayerId, $game['season'], $game['playoff'], $obj);
            }

        }



        $assistPosition = $this->redis->hget($this->getBaseKey() . ':game:' . $event->gameId.':player-positions', $event->assistPlayerId);
        if ($assistPosition == Game::PLAYER) {
            $obj = $this->getPlayerStatLine($event->assistPlayerId, $game['season'], $game['playoff']);
            $obj['assists'] = $obj['assists'] - 1;
            $this->storePlayerStatLine($event->assistPlayerId, $game['season'], $game['playoff'], $obj);
        }

        $this->redis->del($this->getBaseKey().':games:'.$event->getAggregateId().":color:".$event->teamColour.":point:".$event->pointNumber);
    }


    private function getPlayerStatLine($playerId, $season, $playoff)
    {
        $defaultOptions = [];
        $defaultOptions['goals'] = 0;
        $defaultOptions['assists'] = 0;
        $defaultOptions['gamesPlayed'] = 0;
        $defaultOptions['teamGoals'] = 0;
        $defaultOptions['wins'] = 0;
        $defaultOptions['losses'] = 0;
        $defaultOptions['gameWinningGoals'] = 0;

        $obj = $this->redis->hgetall('statline:players:'.$playerId.':seasons:'.$season.':playoff:'.$playoff);

        $options = array_merge($defaultOptions, $obj);
        return $options;
    }

    private function storePlayerStatLine($playerId, $season, $playoff, $obj)
    {
        $this->redis->hmset('statline:players:'.$playerId.':seasons:'.$season.':playoff:'.$playoff, $obj);
    }

    public function onPointAdded(PointAdded $event)
    {
        $game = $this->redis->hgetall($this->getBaseKey() . ':game:' . $event->gameId);

        $goalPosition = $this->redis->hget($this->getBaseKey() . ':game:' . $event->gameId.':player-positions', $event->goalPlayerId);

        if ($goalPosition === GAME::PLAYER) {
            $obj = $this->getPlayerStatLine($event->goalPlayerId, $game['season'], $game['playoff']);
            $obj['goals'] = $obj['goals'] + 1;
            $this->storePlayerStatLine($event->goalPlayerId, $game['season'], $game['playoff'], $obj);
        }



        $assistPosition = $this->redis->hget($this->getBaseKey() . ':game:' . $event->gameId.':player-positions', $event->assistPlayerId);
        if ($assistPosition == Game::PLAYER) {
            $obj = $this->getPlayerStatLine($event->assistPlayerId, $game['season'], $game['playoff']);
            $obj['assists'] = $obj['assists'] + 1;
            $this->storePlayerStatLine($event->assistPlayerId, $game['season'], $game['playoff'], $obj);
        }

        $pointObj = [];
        $pointObj['teamColour'] = $event->teamColour;
        $pointObj['pointNumber'] = $event->pointNumber;
        $pointObj['goalPlayerId'] = $event->goalPlayerId;
        $pointObj['assistPlayerId'] = $event->assistPlayerId;
        $this->redis->hmset($this->getBaseKey() . ':games:'.$event->getAggregateId().":color:".$event->teamColour.":point:".$event->pointNumber, $pointObj);
    }



    public function onGameCompleted(GameCompleted $event)
    {
        $game = $this->redis->hgetall($this->getBaseKey() . ':game:' . $event->gameId);
        $teamPlayers = $this->redis->hgetall($this->getBaseKey() . ':game:' . $event->gameId.':players');
        $winningPoint = $this->redis->hgetall($this->getBaseKey() . ':games:'.$event->gameId.":color:".$event->winningTeam.":point:".$event->winningPoint);

        foreach ($teamPlayers as $playerId => $teamColour) {
            $obj = $this->getPlayerStatLine($playerId, $game['season'], $game['playoff']);


            if (Game::WHITE_TEAM == $teamColour) {
                $obj['teamGoals'] = $obj['teamGoals'] + $event->whitePointTotal;
            } else {
                $obj['teamGoals'] = $obj['teamGoals'] + $event->blackPointTotal;
            }

            if ($playerId == $winningPoint['goalPlayerId']) {
                $obj['gameWinningGoals'] = $obj['gameWinningGoals'] + 1;
            }

            if ($teamColour == $event->winningTeam) {
                $obj['wins'] = $obj['wins'] + 1;
            } else {
                $obj['losses'] = $obj['losses'] + 1;
            }

            $this->storePlayerStatLine($playerId, $game['season'], $game['playoff'], $obj);
        }
        $this->redis->hset($this->getBaseKey() . ':game:' . $event->getAggregateId(), 'winner', $event->winningTeam);
        $this->redis->hset($this->getBaseKey() . ':game:' . $event->getAggregateId(), 'winningPoint', $event->winningPoint);
        $this->redis->hset($this->getBaseKey() . ':game:' . $event->getAggregateId(), Game::BLACK_TEAM.'TotalPoints', $event->blackPointTotal);
        $this->redis->hset($this->getBaseKey() . ':game:' . $event->getAggregateId(), Game::WHITE_TEAM.'TotalPoints', $event->whitePointTotal);
    }


    public function onGameAdded(GameAdded $event)
    {
        $obj = [];
        $obj["id"] = $event->getAggregateId();
        //TODO: FIGURE OUT THE TIME
        $obj["playoff"] = $event->playoff;
        $obj["season"] = $event->season;
        $obj["gameNumber"] = $event->gameNumber;
        $startTime = Carbon::parse($event->gameDate.' '.$event->start);
        $endTime = Carbon::parse($event->gameDate.' '.$event->end);
        $minDiff = $startTime->diffInMinutes($endTime);
        $obj['gameTime'] = $minDiff;
        $obj['gameDate'] = $event->gameDate;
        $obj['start'] = $event->end;
        $obj['end'] = $event->start;

        $this->redis->hset(
            'stats:seasons:',
            $event->season,
            $event->season
        );


        $this->redis->hmset($this->getBaseKey() . ':game:' . $event->getAggregateId(), $obj);
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            [
                PlayerAdded::class,
                PlayerEdited::class,
                TeamPlayerAdded::class,
                PointAdded::class,
                GameCompleted::class,
                GameAdded::class,
                TeamPlayerRemoved::class,
                PointRemoved::class
            ],
            PlayerStats::class . '@handleEvent'
        );
    }

}
