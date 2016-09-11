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

class RegularSeasonOvertimeGames extends Listener
{
    private $redis;
    private $recordStore;

    CONST BASE_KEY = 'regularSeasonOvertimeGames';
    private $baseKey = RegularSeasonOvertimeGames::BASE_KEY;

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

        if ($event->winningPoint > 10) {
            $game = $this->redis->hgetall($this->getBaseKey() . ':game:' . $event->gameId);

            $obj = $this->redis->hgetall('regularSeasonOvertimeGames:seasons:');

            $overtimeCount = $this->getOrDefault($obj, $game["season"]) + 1;
            $obj[$game["season"]] = $overtimeCount;
            $this->redis->hmset('regularSeasonOvertimeGames:seasons:', $obj);

            $this->storeRecord();
        }
    }

    public function onGameUnCompleted(GameUnCompleted $event) {

        if ($event->winningPoint > 10) {
            $game = $this->redis->hgetall($this->getBaseKey() . ':game:' . $event->gameId);

            $obj = $this->redis->hgetall('regularSeasonOvertimeGames:seasons:');

            $overtimeCount = $this->getOrDefault($obj, $game["season"]) - 1;
            $obj[$game["season"]] = $overtimeCount;
            $this->redis->hmset('regularSeasonOvertimeGames:seasons:', $obj);

            $this->storeRecord();
        }
    }


    private function storeRecord()
    {
        $seasons = $this->redis->hgetall('regularSeasonOvertimeGames:seasons:');

        $recordEntries = [];
        foreach ($seasons as $season => $games) {
            $recordEntries[] = [
                'entry' =>   RecordStore::seasonEncode($season).": " . $games ." Overtime Games.",
                'playerKeys' => []
            ];
        }
        $this->recordStore->setRecord($this->baseKey, 'Regular Season Overtime Games', $recordEntries);
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
            RegularSeasonOvertimeGames::class . '@handleEvent'
        );
    }

}
