<?php

namespace App\Listeners\Records;

use App\Aggregates\Game;
use App\Events\Game\GameAdded;
use App\Events\Game\GameCompleted;
use App\Events\Game\GameEdited;
use App\Events\Game\GameUnCompleted;
use App\Events\Game\PointAdded;
use App\Events\Game\TeamPlayerAdded;
use App\Infrastructure\Database\IRedisDB;
use App\Listeners\Listener;
use Illuminate\Contracts\Events\Dispatcher;

class TeamRegularSeasonRecord extends Listener
{
    private $redis;
    private $recordStore;

    CONST BASE_KEY = 'teamRegularSeasonRecord';
    private $baseKey = TeamRegularSeasonRecord::BASE_KEY;

    public function __construct(IRedisDB $redis, RecordStore $recordStore)
    {
        $this->redis = $redis;
        $this->recordStore = $recordStore;
    }
    
    public function onGameAdded(GameAdded $event)
    {
        $obj = [];
        $obj["season"] = $event->season;
        $obj["gameNumber"] = $event->gameNumber;

        $this->redis->hmset($this->getBaseKey() . ':game:' . $event->getAggregateId(), $obj);
        $this->redis->hset($this->getBaseKey().':seasons:', $event->season, $event->season);
    }

    public function onGameEdited(GameEdited $event)
    {
        $obj = [];
        $obj["season"] = $event->season;
        $obj["gameNumber"] = $event->gameNumber;

        $this->redis->hmset($this->getBaseKey() . ':game:' . $event->getAggregateId(), $obj);
        $this->redis->hset($this->getBaseKey().':seasons:', $event->season, $event->season);
    }

    public function onGameCompleted(GameCompleted $event) {
        $game = $this->redis->hgetall($this->getBaseKey() . ':game:' . $event->gameId);

        $obj = $this->redis->hgetall('teamRegularSeasonRecord:season:'.$game["season"]);

        $team = Game::BLACK_TEAM;
        if ($event->winningTeam == Game::WHITE_TEAM) {
            $team = Game::WHITE_TEAM;
        }

        $winCount = $this->getOrDefault($obj, $team) + 1;
        $obj[$team] = $winCount;
        $this->redis->hmset('teamRegularSeasonRecord:season:'.$game["season"], $obj);

        $this->storeRecord();
    }

    public function onGameUnCompleted(GameUnCompleted $event) {
        $game = $this->redis->hgetall($this->getBaseKey() . ':game:' . $event->gameId);

        $obj = $this->redis->hgetall('teamRegularSeasonRecord:season:'.$game["season"]);

        $team = Game::BLACK_TEAM;
        if ($event->winningTeam == Game::WHITE_TEAM) {
            $team = Game::WHITE_TEAM;
        }

        $winCount = $this->getOrDefault($obj, $team) - 1;
        $obj[$team] = $winCount;
        $this->redis->hmset('teamRegularSeasonRecord:season:'.$game["season"], $obj);

        $this->storeRecord();
    }

    private function storeRecord()
    {
        $seasons = $this->redis->hvals($this->getBaseKey().':seasons:');

        $recordEntries = [];
        foreach ($seasons as $season) {
            $obj = $this->redis->hgetall('teamRegularSeasonRecord:season:'.$season);
            $recordEntries[] = [
                'entry' =>   RecordStore::seasonEncode($season).": " . $this->getOrDefault($obj, Game::WHITE_TEAM) ."-".$this->getOrDefault($obj, Game::BLACK_TEAM),
                'playerKeys' => []
            ];
        }
        $this->recordStore->setRecord($this->baseKey, 'Team Regular Season Record (White vs Color)', $recordEntries);
    }


    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            [
                GameCompleted::class,
                GameAdded::class,
                GameEdited::class,
                GameUnCompleted::class
            ],
            TeamRegularSeasonRecord::class . '@handleEvent'
        );
    }

}
