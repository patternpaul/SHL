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
use App\Events\Game\TeamPlayerAdded;
use App\Events\Player\PlayerAdded;
use App\Events\Player\PlayerEdited;
use App\Infrastructure\Database\IRedisDB;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;

class GoalieStats extends Listener
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
                $seasonPlayers = $this->redis->hgetall('stats:seasons:'.$season.":playoffs:0:goalies");
                foreach ($seasonPlayers as $seasonPlayer) {
                    $seasonPlayerData = $this->getGoalieStatLine($seasonPlayer, $season, 0);
                    if(isset($statLines[$seasonPlayer])) {
                        $statLines[$seasonPlayer] = $this->addRows($seasonPlayerData, $statLines[$seasonPlayer]);
                    } else {
                        $statLines[$seasonPlayer] = $seasonPlayerData;
                    }
                }
            }


            if(($selectedPlayoff === 'all') || ($selectedPlayoff === 1) || ($selectedPlayoff === '1')) {
                $seasonPlayers = $this->redis->hgetall('stats:seasons:'.$season.":playoffs:1:goalies");
                foreach ($seasonPlayers as $seasonPlayer) {
                    $seasonPlayerData = $this->getGoalieStatLine($seasonPlayer, $season, 1);
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



    public function getCalcGoalieStatLine($playerId, $season, $playoff)
    {
        return $this->calcStats($this->getGoalieStatLine($playerId, $season, $playoff));
    }

    private function calcStats($statObj)
    {
        $statObj['goalsAgainstAverage'] = round($statObj['goalsAgainst']/$statObj['gamesPlayed'], 2);
        $statObj['goalsPerMinute'] = round($statObj['goalsAgainst']/$statObj['minutesPlayed'], 2);
        $statObj['plusMinus'] = $statObj['wins'] - $statObj['losses'];
        $statObj['winPercentage'] = round($statObj['wins']/($statObj['wins'] + $statObj['losses'])*100, 2);
        $statObj['points'] = $statObj['goals'] + $statObj['assists'];
        return $statObj;
    }

    public function getGoalieStats($playerId, $playoff)
    {
        $seasons = $this->redis->hgetall('stats:seasons:');
        $statLines = [];
        $calcLines = [];
        $allLine = [];

        foreach ($seasons as $season) {
            $seasonPlayers = $this->redis->hgetall('stats:seasons:'.$season.":playoffs:".$playoff.":goalies");
            foreach ($seasonPlayers as $seasonPlayer) {
                if ($seasonPlayer == $playerId) {
                    $seasonPlayerData = $this->getCalcGoalieStatLine($seasonPlayer, $season, $playoff);
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


    private function getGoalieStatLine($playerId, $season, $playoff)
    {
        $defaultOptions = [];
        $defaultOptions['goalsAgainst'] = 0;
        $defaultOptions['gamesPlayed'] = 0;
        $defaultOptions['wins'] = 0;
        $defaultOptions['losses'] = 0;
        $defaultOptions['shutOuts'] = 0;
        $defaultOptions['goals'] = 0;
        $defaultOptions['assists'] = 0;
        $defaultOptions['minutesPlayed'] = 0;

        $obj = $this->redis->hgetall('statline:goalies:'.$playerId.':seasons:'.$season.':playoff:'.$playoff);

        $options = array_merge($defaultOptions, $obj);
        return $options;
    }

    private function storeGoalieStatLine($playerId, $season, $playoff, $obj)
    {
        $this->redis->hmset('statline:goalies:'.$playerId.':seasons:'.$season.':playoff:'.$playoff, $obj);
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



        if ($event->position == Game::GOALIE) {
            
            $this->redis->hset(
                'stats:seasons:'.$game['season'].":playoffs:".$game['playoff'].":goalies",
                $event->playerId,
                $event->playerId
            );

            $this->redis->hset(
                'stats:seasons:'.$game['season'].":playoffs:all:goalies",
                $event->playerId,
                $event->playerId
            );


            //TODO: Will need to check player type due to goalies not tracking against player time
            $this->redis->hset(
                'stats:goalies:'.$event->playerId.":playoffs:".$game['playoff'].":seasons",
                $game['season'],
                $game['season']
            );


            $this->redis->hset(
                $this->getBaseKey() . ':game:' . $event->gameId.':goalies',
                $event->teamColour,
                $event->playerId
            );

            $obj = $this->getGoalieStatLine($event->playerId, $game['season'], $game['playoff']);
            $obj['gamesPlayed'] = $obj['gamesPlayed'] + 1;
            $obj['minutesPlayed'] = $obj['minutesPlayed'] + $game['gameTime'];
            $this->storeGoalieStatLine($event->playerId, $game['season'], $game['playoff'], $obj);

        }
    }



    public function onPointAdded(PointAdded $event)
    {
        $game = $this->redis->hgetall($this->getBaseKey() . ':game:' . $event->gameId);
        $teamGoalie = Game::BLACK_TEAM;
        $scoringGoalie = Game::WHITE_TEAM;
        if ($event->teamColour == Game::BLACK_TEAM) {
            $teamGoalie = Game::WHITE_TEAM;
            $scoringGoalie = Game::BLACK_TEAM;
        }

        $goaliePlayerId = $this->redis->hget($this->getBaseKey() . ':game:' . $event->gameId.':goalies', $teamGoalie);
        $scoringGoaliePlayerId = $this->redis->hget($this->getBaseKey() . ':game:' . $event->gameId.':goalies', $scoringGoalie);

        $goalieStats = $this->getGoalieStatLine($goaliePlayerId, $game['season'], $game['playoff']);
        $goalieStats['goalsAgainst'] = $goalieStats['goalsAgainst'] + 1;
        $scoringGoalieStats = $this->getGoalieStatLine($scoringGoaliePlayerId, $game['season'], $game['playoff']);
        if ($event->goalPlayerId === $scoringGoaliePlayerId) {
            $scoringGoalieStats['goals'] = $scoringGoalieStats['goals'] + 1;
        }

        if ($event->assistPlayerId === $scoringGoaliePlayerId) {
            $scoringGoalieStats['assists'] = $scoringGoalieStats['assists'] + 1;
        }

        $this->storeGoalieStatLine($goaliePlayerId, $game['season'], $game['playoff'], $goalieStats);
        $this->storeGoalieStatLine($scoringGoaliePlayerId, $game['season'], $game['playoff'], $scoringGoalieStats);



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
        $loosingGoalie = Game::BLACK_TEAM;
        $winningGoalie = Game::WHITE_TEAM;
        if ($event->winningTeam == Game::BLACK_TEAM) {
            $loosingGoalie = Game::WHITE_TEAM;
            $winningGoalie = Game::BLACK_TEAM;
        }

        $loosingGoaliePlayerId = $this->redis->hget($this->getBaseKey() . ':game:' . $event->gameId.':goalies', $loosingGoalie);
        $winningGoaliePlayerId = $this->redis->hget($this->getBaseKey() . ':game:' . $event->gameId.':goalies', $winningGoalie);
        $loosingGoalieStats = $this->getGoalieStatLine($loosingGoaliePlayerId, $game['season'], $game['playoff']);
        $winningGoalieStats = $this->getGoalieStatLine($winningGoaliePlayerId, $game['season'], $game['playoff']);

        $loosingGoalieStats['losses'] = $loosingGoalieStats['losses'] + 1;


        $winningGoalieStats['wins'] = $winningGoalieStats['wins'] + 1;
        if (($event->blackPointTotal === 0) || ($event->whitePointTotal === 0)) {
            $winningGoalieStats['shutOuts'] = $winningGoalieStats['shutOuts'] + 1;
        }

        $this->storeGoalieStatLine($loosingGoaliePlayerId, $game['season'], $game['playoff'], $loosingGoalieStats);
        $this->storeGoalieStatLine($winningGoaliePlayerId, $game['season'], $game['playoff'], $winningGoalieStats);
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
                GameAdded::class
            ],
            GoalieStats::class . '@handleEvent'
        );
    }

}
