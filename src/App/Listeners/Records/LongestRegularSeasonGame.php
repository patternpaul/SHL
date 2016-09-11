<?php

namespace App\Listeners\Records;

use App\Aggregates\Game;
use App\Events\Game\GameAdded;
use App\Events\Game\GameCompleted;
use App\Events\Game\GameEdited;
use App\Events\Game\GameUnCompleted;
use App\Events\Game\PointAdded;
use App\Events\Game\TeamPlayerAdded;
use App\Events\Game\TeamPlayerRemoved;
use App\Infrastructure\Database\IRedisDB;
use App\Listeners\Listener;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;

class LongestRegularSeasonGame extends Listener
{
    private $redis;
    private $recordStore;

    CONST BASE_KEY = 'longestRegularSeasonGame';
    private $baseKey = LongestRegularSeasonGame::BASE_KEY;

    public function __construct(IRedisDB $redis, RecordStore $recordStore)
    {
        $this->redis = $redis;
        $this->recordStore = $recordStore;
    }

    public function onTeamPlayerAdded(TeamPlayerAdded $event)
    {
        if ($event->position == Game::GOALIE) {
            $obj = [];
            $obj[$event->teamColour."Goalie"] = $event->playerId;

            $this->redis->hmset($this->getBaseKey() . ':game:' . $event->getAggregateId(), $obj);
        }
    }

    public function onTeamPlayerRemoved(TeamPlayerRemoved $event)
    {
        if ($event->position == Game::GOALIE) {
            $this->redis->hdel($this->getBaseKey() . ':game:' . $event->getAggregateId(), $event->teamColour."Goalie");
        }
    }


    public function onGameAdded(GameAdded $event)
    {

        $startTime = Carbon::parse($event->gameDate.' '.$event->start);
        $endTime = Carbon::parse($event->gameDate.' '.$event->end);
        $minDiff = $startTime->diffInMinutes($endTime);

        $obj = [];
        $obj["season"] = $event->season;
        $obj["gameNumber"] = $event->gameNumber;
        $obj['gameTime'] = $minDiff;
        $obj['id'] = $event->gameId;
        $obj['playoff'] = $event->playoff;

        $this->redis->hmset($this->getBaseKey() . ':game:' . $event->getAggregateId(), $obj);
        $this->redis->hset($this->getBaseKey().':seasons:', $event->season, $event->season);
    }

    public function onGameEdited(GameEdited $event)
    {

        $startTime = Carbon::parse($event->gameDate.' '.$event->start);
        $endTime = Carbon::parse($event->gameDate.' '.$event->end);
        $minDiff = $startTime->diffInMinutes($endTime);

        $obj = [];
        $obj["season"] = $event->season;
        $obj["gameNumber"] = $event->gameNumber;
        $obj['gameTime'] = $minDiff;
        $obj['id'] = $event->gameId;
        $obj['playoff'] = $event->playoff;

        $this->redis->hmset($this->getBaseKey() . ':game:' . $event->getAggregateId(), $obj);
        $this->redis->hset($this->getBaseKey().':seasons:', $event->season, $event->season);
    }


    public function onGameCompleted(GameCompleted $event) {

        $game = $this->redis->hgetall($this->getBaseKey() . ':game:' . $event->gameId);
        $obj = $this->redis->hgetall('longestRegularSeasonGame');

        $this->redis->hset('longestRegularSeasonGameGameList',$game['id'], $game['gameTime']);

        if ($game['gameTime'] > $this->getOrDefault($obj, 'gameTime')) {
            $this->redis->del('longestRegularSeasonGame:gameIds');
            $this->redis->hset('longestRegularSeasonGame','gameTime', $game['gameTime']);
        }

        if ($game['gameTime'] >= $this->getOrDefault($obj, 'gameTime')) {
            $this->redis->hset('longestRegularSeasonGame:gameIds', $event->gameId, $event->gameId);
        }


        $obj = [];
        $obj[Game::BLACK_TEAM.'Points'] = $event->blackPointTotal;
        $obj[Game::WHITE_TEAM.'Points'] = $event->whitePointTotal;
        $obj["winningTeam"] = $event->winningTeam;

        $this->redis->hmset($this->getBaseKey() . ':game:' . $event->gameId, $obj);


        $this->storeRecord();
    }

    public function onGameUnCompleted(GameUnCompleted $event) {
        $game = $this->redis->hgetall($this->getBaseKey() . ':game:' . $event->gameId);

        if ($game['playoff'] == 0) {
            $this->redis->hdel($this->getBaseKey() . ':game:' . $event->gameId, Game::BLACK_TEAM.'Points');
            $this->redis->hdel($this->getBaseKey() . ':game:' . $event->gameId, Game::WHITE_TEAM.'Points');
            $this->redis->hdel($this->getBaseKey() . ':game:' . $event->gameId, "winningTeam");
            $this->redis->hdel('longestRegularSeasonGameGameList', $event->gameId);
            $this->redis->del('longestRegularSeasonGame:gameIds');
            $gameTimes = $this->redis->hvals('longestRegularSeasonGameGameList');
            sort($gameTimes);

            if (count($gameTimes) > 0) {

                $this->redis->hset('longestRegularSeasonGame','gameTime', $gameTimes[0]);

                foreach ($this->redis->hgetall('longestRegularSeasonGameGameList') as $gameId => $gameTime) {
                    if ($gameTimes[0] == $gameTime) {
                        $this->redis->hset('longestRegularSeasonGame:gameIds', $gameId, $gameId);
                    }
                }
            } else {
                $this->redis->hdel('longestRegularSeasonGame','gameTime');
            }

            $this->storeRecord();
        }
    }

    private function storeRecord()
    {
        $longestGameTime = $this->redis->hgetall('longestRegularSeasonGame');
        $games = $this->redis->hvals('longestRegularSeasonGame:gameIds');

        $recordEntries = [];
        foreach ($games as $gameId) {
            $game = $this->redis->hgetall($this->getBaseKey() . ':game:' . $gameId);

            $score = $game[Game::BLACK_TEAM.'Points'] . '-' . $game[Game::WHITE_TEAM.'Points'] . ' for Colored Team';
            if ($game[Game::BLACK_TEAM.'Points'] < $game[Game::WHITE_TEAM.'Points']) {
                $score = $game[Game::WHITE_TEAM.'Points'] . '-' . $game[Game::BLACK_TEAM.'Points'] . ' for Colored Team';
            }

            $recordEntries[] = [
                'entry' =>   RecordStore::seasonEncode($game['season']).": ".RecordStore::gameEncode($gameId,$game["gameNumber"])." - " .
                $longestGameTime['gameTime'] . ' minutes. '. $score . '. Goalies: ('.RecordStore::playerEncode($game[Game::WHITE_TEAM."Goalie"]).
                    ' white vs. '.RecordStore::playerEncode($game[Game::BLACK_TEAM."Goalie"]).' colored)',
                'playerKeys' => [$game[Game::WHITE_TEAM."Goalie"], $game[Game::BLACK_TEAM."Goalie"]]
            ];
        }
        $this->recordStore->setRecord($this->baseKey, 'Longest Regular Season Game', $recordEntries);
    }


    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            [
                GameCompleted::class,
                GameAdded::class,
                TeamPlayerAdded::class,
                GameUnCompleted::class,
                GameEdited::class,
                TeamPlayerRemoved::class
            ],
            LongestRegularSeasonGame::class . '@handleEvent'
        );
    }

}
