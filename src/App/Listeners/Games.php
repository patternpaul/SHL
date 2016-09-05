<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 2016-08-03
 * Time: 8:46 PM
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

class Games extends Listener
{
    private $redis;

    public function __construct(IRedisDB $redis)
    {
        $this->redis = $redis;
    }

    public function getById($key)
    {
        $obj = $this->redis->hgetall('game:' . $key);
        $obj[Game::BLACK_TEAM."AllPlayers"] = $this->getGameTeamPlayers($obj['id'], Game::BLACK_TEAM, true);
        $obj[Game::WHITE_TEAM."AllPlayers"] = $this->getGameTeamPlayers($obj['id'], Game::WHITE_TEAM, true);
        $obj[Game::BLACK_TEAM."Players"] = $this->getGameTeamPlayers($obj['id'], Game::BLACK_TEAM, false);
        $obj[Game::WHITE_TEAM."Players"] = $this->getGameTeamPlayers($obj['id'], Game::WHITE_TEAM, false);
        $obj[Game::WHITE_TEAM."Goals"] = $this->getGameTeamPoints($obj['id'], Game::WHITE_TEAM);
        $obj[Game::BLACK_TEAM."Goals"] = $this->getGameTeamPoints($obj['id'], Game::BLACK_TEAM);
        $obj[Game::BLACK_TEAM."Goalie"] = $this->redis->hgetall($this->getBaseKey() . ':player:' . $obj[Game::BLACK_TEAM."Goalie"]);
        $obj[Game::WHITE_TEAM."Goalie"] = $this->redis->hgetall($this->getBaseKey() . ':player:' . $obj[Game::WHITE_TEAM."Goalie"]);

        return $obj;
    }

    public function onGameAdded(GameAdded $event)
    {
        $startTime = Carbon::parse($event->gameDate.' '.$event->start);
        $endTime = Carbon::parse($event->gameDate.' '.$event->end);
        $minDiff = $startTime->diffInMinutes($endTime);


        $obj = [];
        $obj["id"] = $event->getAggregateId();
        $obj["start"] = $event->start;
        $obj["end"] = $event->end;
        $obj["playoff"] = $event->playoff;
        $obj['gameDate'] = $event->gameDate;
        $obj['gameTime'] = $minDiff;
        $obj["season"] = $event->season;
        $obj["gameNumber"] = $event->gameNumber;
        $obj[Game::BLACK_TEAM."Points"] = 0;
        $obj[Game::WHITE_TEAM."Points"] = 0;

        $this->redis->hmset('game:' . $event->getAggregateId(), $obj);

        $this->redis->hset(
            'games:',
            $event->getAggregateId(),
            $event->getAggregateId()
        );

        $this->redis->hset(
            'games:season:'.$event->season,
            $event->getAggregateId(),
            $event->getAggregateId()
        );

        $lastSeason = $this->redis->hget(
            'last-game:',
            'season'
        );

        $storeSeason = $lastSeason;
        if (!is_numeric($storeSeason) || ($event->season > $storeSeason)) {
            $storeSeason = $event->season;
        }

        $this->redis->hset(
            'last-game:',
            'season',
            $storeSeason
        );
        $lastGame = $this->redis->hget(
            'last-game:',
            'game'
        );

        $storeGame = $lastGame;
        if (!is_numeric($storeGame) || ($event->gameNumber > $storeGame && $storeSeason == $event->season) || ($lastSeason != $storeSeason)) {
            $storeGame = $event->gameNumber;
        }

        $this->redis->hset(
            'last-game:',
            'game',
            $storeGame
        );
    }

    public function getLatestSeasonGames()
    {
        $returnGames = [];
        $latestSeason = $this->redis->hget('last-game:', 'season');
        $seasonGames = $this->redis->hvals('games:season:'.$latestSeason);
        foreach ($seasonGames as $seasonGame) {
            $returnGames[] = $this->getById($seasonGame);
        }
        $seasonGames = $this->redis->hvals('games:season:'.$latestSeason-1);
        foreach ($seasonGames as $seasonGame) {
            $returnGames[] = $this->getById($seasonGame);
        }

        $returnGames = array_sort($returnGames, function ($value) {
            return str_pad($value['season'], 2, "0", STR_PAD_LEFT).$value['playoff'].str_pad($value['gameNumber'], 2, "0", STR_PAD_LEFT);
        });

        return $returnGames;
    }

    public function getSpecificSeasonGames($season)
    {
        $returnGames = [];
        $seasonGames = $this->redis->hvals('games:season:'.$season);
        foreach ($seasonGames as $seasonGame) {
            $returnGames[] = $this->getById($seasonGame);
        }

        $returnGames = array_sort($returnGames, function ($value) {
            return str_pad($value['season'], 2, "0", STR_PAD_LEFT).$value['playoff'].str_pad($value['gameNumber'], 2, "0", STR_PAD_LEFT);
        });

        return $returnGames;
    }

    public function getLatestSeason()
    {
        return $this->redis->hget(
            'last-game:',
            'season'
        );
    }

    public function getLatestGame()
    {
        return $this->redis->hget(
            'last-game:',
            'game'
        );
    }

    public function onTeamPlayerAdded(TeamPlayerAdded $event)
    {
        $this->redis->hset(
            'games:'.$event->getAggregateId().":teamplayers:".$event->teamColour,
            $event->playerId,
            $event->playerId
        );

        if ($event->position === Game::PLAYER) {
            $this->redis->hset(
                'games:'.$event->getAggregateId().":teamplayers:players:".$event->teamColour,
                $event->playerId,
                $event->playerId
            );
        } else {
            $obj[$event->teamColour.'Goalie'] = $event->playerId;
            $this->redis->hmset('game:' . $event->getAggregateId(), $obj);
        }

    }

    private function getGameTeamPlayers($gameId, $teamColour, $all = true)
    {
        if ($all) {
            $keys = $this->redis->hgetall('games:'.$gameId.":teamplayers:".$teamColour);
        } else {
            $keys = $this->redis->hgetall('games:'.$gameId.":teamplayers:players:".$teamColour);
        }


        $teamPlayers = [];

        foreach ($keys as $key => $value) {
            $teamPlayers[$key] = $this->redis->hgetall($this->getBaseKey() . ':player:' . $key);
        }

        return $teamPlayers;
    }

    private function getGameTeamPoints($gameId, $teamColour)
    {
        $teamPlayers = $this->getGameTeamPlayers($gameId, $teamColour);

        $points = [];
        $keys = $this->redis->hgetall('games:'.$gameId.":color:".$teamColour.":points:");

        foreach ($keys as $key => $value) {
            $point = $this->redis->hgetall($value);

            $point['goalPlayer'] = $teamPlayers[$point['goalPlayerId']];
            if (trim($point['assistPlayerId']) != '') {
                $point['assistPlayer'] = $teamPlayers[$point['assistPlayerId']];
            }

            $points[$key] = $point;
        }

        /*
        $points = array_values(array_sort($points, function ($value) {
            return $value['pointNumber'];
        }));
        */
        return $points;
    }

    public function onPointAdded(PointAdded $event)
    {
        $obj = $this->redis->hgetall('game:' . $event->getAggregateId());
        $obj[$event->teamColour."Points"] = $obj[$event->teamColour."Points"] + 1;

        $this->redis->hmset('game:' . $event->getAggregateId(), $obj);

        $this->redis->hset(
            'games:'.$event->getAggregateId().":color:".$event->teamColour.":points:",
            $event->pointNumber,
            'games:'.$event->getAggregateId().":color:".$event->teamColour.":point:".$event->pointNumber
        );

        $pointObj = [];
        $pointObj['teamColour'] = $event->teamColour;
        $pointObj['pointNumber'] = $event->pointNumber;
        $pointObj['goalPlayerId'] = $event->goalPlayerId;
        $pointObj['assistPlayerId'] = $event->assistPlayerId;
        $this->redis->hmset('games:'.$event->getAggregateId().":color:".$event->teamColour.":point:".$event->pointNumber, $pointObj);
    }


    public function onPlayerAdded(PlayerAdded $event)
    {
        $obj = [];
        $obj["id"] = $event->getAggregateId();
        $obj["firstName"] = $event->firstName;
        $obj["lastName"] = $event->lastName;

        $this->redis->hmset($this->getBaseKey() . ':player:' . $event->getAggregateId(), $obj);
    }

    public function onPlayerEdited(PlayerEdited $event)
    {
        $obj = [];
        $obj["firstName"] = $event->firstName;
        $obj["lastName"] = $event->lastName;
        $this->redis->hmset($this->getBaseKey() . ':player:' . $event->getAggregateId(), $obj);
    }

    public function onGameCompleted(GameCompleted $event)
    {
        $obj = [];
        $obj["winningTeam"] = $event->winningTeam;
        
        $this->redis->hmset('game:' . $event->getAggregateId(), $obj);
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            [
                GameAdded::class,
                TeamPlayerAdded::class,
                PointAdded::class,
                PlayerAdded::class,
                GameCompleted::class
            ],
            Games::class . '@handleEvent'
        );
    }

}