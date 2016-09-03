<?php

namespace App\Listeners\Records;

use App\Aggregates\Game;
use App\Events\Game\GameAdded;
use App\Events\Game\GameCompleted;
use App\Events\Game\PointAdded;
use App\Events\Game\TeamPlayerAdded;
use App\Infrastructure\Database\IRedisDB;
use App\Listeners\Listener;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;

class RegularSeasonShutOutClub extends Listener
{
    private $redis;
    private $recordStore;

    CONST BASE_KEY = 'regularSeasonShutOutClub';
    private $baseKey = RegularSeasonShutOutClub::BASE_KEY;

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


    public function onGameAdded(GameAdded $event)
    {
        $obj = [];
        $obj["season"] = $event->season;
        $obj["gameNumber"] = $event->gameNumber;

        $this->redis->hmset($this->getBaseKey() . ':game:' . $event->getAggregateId(), $obj);
        $this->redis->hset($this->getBaseKey().':seasons:', $event->season, $event->season);
    }


    public function onGameCompleted(GameCompleted $event) {

        if ($event->blackPointTotal === 0 || $event->whitePointTotal === 0) {
            $this->redis->hset($this->baseKey, $event->gameId, $event->winningTeam);

            $this->storeRecord();
        }
    }


    private function storeRecord()
    {
        $games = $this->redis->hgetall($this->baseKey);

        $recordEntries = [];
        foreach ($games as $gameId => $winningTeam) {
            $game = $this->redis->hgetall($this->getBaseKey() . ':game:' . $gameId);

            /** Season 2: Paul Rajotte (game 22) */

            $recordEntries[] = [
                'entry' =>   RecordStore::seasonEncode($game['season']).": ".RecordStore::playerEncode($game[$winningTeam."Goalie"])." (".RecordStore::gameEncode($gameId,$game["gameNumber"]).")",
                'playerKeys' => [$game[$winningTeam."Goalie"]]
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
                TeamPlayerAdded::class
            ],
            RegularSeasonShutOutClub::class . '@handleEvent'
        );
    }

}
