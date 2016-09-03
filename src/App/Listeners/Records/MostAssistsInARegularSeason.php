<?php

namespace App\Listeners\Records;

use App\Aggregates\Game;
use App\Events\Game\GameAdded;
use App\Events\Game\PointAdded;
use App\Events\Game\TeamPlayerAdded;
use App\Infrastructure\Database\IRedisDB;
use App\Listeners\Listener;
use Illuminate\Contracts\Events\Dispatcher;

class MostAssistsInARegularSeason extends Listener
{
    private $redis;
    private $recordStore;

    CONST BASE_KEY = 'mostAssistsInARegularSeason';
    private $baseKey = MostAssistsInARegularSeason::BASE_KEY;

    public function __construct(IRedisDB $redis, RecordStore $recordStore)
    {
        $this->redis = $redis;
        $this->recordStore = $recordStore;
    }

    public function onTeamPlayerAdded(TeamPlayerAdded $event)
    {
        $this->redis->hset(
            $this->getBaseKey() . ':game:' . $event->gameId.':player-positions',
            $event->playerId,
            $event->position
        );

        if ($event->position == Game::PLAYER) {

            $obj = $this->redis->hgetall($this->getBaseKey().':gamecount');
            $obj[$event->playerId] = $this->getOrDefault($obj, $event->playerId) + 1;
            $this->redis->hmset($this->getBaseKey().':gamecount', $obj);
        }
    }

    public function onGameAdded(GameAdded $event)
    {
        $obj = [];
        $obj["season"] = $event->season;
        $obj["gameNumber"] = $event->gameNumber;
        $obj["playoff"] = $event->playoff;


        $this->redis->hmset($this->getBaseKey() . ':game:' . $event->getAggregateId(), $obj);
        $this->redis->hset($this->getBaseKey().':seasons:', $event->season, $event->season);
    }



    public function onPointAdded(PointAdded $event)
    {
        $recordTracker = $this->redis->hgetall($this->getBaseKey() . ':record');
        $currentMost = $this->getOrDefault($recordTracker, 'currentMost');
        $game = $this->redis->hgetall($this->getBaseKey() . ':game:' . $event->gameId);

        if ($game["playoff"] == 0) {


            $playerPosition = $this->redis->hget($this->getBaseKey() . ':game:' . $event->gameId.':player-positions', $event->assistPlayerId);

            if ($playerPosition == Game::PLAYER) {
                $obj = $this->redis->hgetall($this->baseKey.':player:'.$event->assistPlayerId);
                $pointCount = $this->getOrDefault($obj, $game['season']) + 1;
                $obj[$game['season']] = $pointCount;
                $this->redis->hmset($this->baseKey.':player:'.$event->assistPlayerId, $obj);

                if ($pointCount > $currentMost) {
                    $this->redis->del($this->getBaseKey() . ':record:holders');
                }
                if ($pointCount >= $currentMost) {
                    $this->redis->hset($this->getBaseKey() . ':record', 'currentMost', $pointCount);
                    $this->redis->hset($this->getBaseKey() . ':record:holders', $event->assistPlayerId.':'.$game['season'], $pointCount);
                    $this->storeRecord();
                }
            }


        }
    }

    private function storeRecord()
    {
        $currentMost = $this->redis->hget($this->getBaseKey() . ':record', 'currentMost');
        $recordHolders = $this->redis->hkeys($this->getBaseKey() . ':record:holders');


        $recordEntries = [];
        foreach ($recordHolders as $recordHolder) {
            $playerData = explode(':', $recordHolder);
            $recordEntries[] = [
                'entry' =>   RecordStore::seasonEncode($playerData[1])." ".RecordStore::playerEncode($playerData[0])." ".$currentMost." Assists.",
                'playerKeys' => [$playerData[0]]
            ];
        }
        $this->recordStore->setRecord($this->baseKey, 'Most Assists Overall By A Player In One Season', $recordEntries);

    }



    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            [
                TeamPlayerAdded::class,
                GameAdded::class,
                PointAdded::class
            ],
            MostAssistsInARegularSeason::class . '@handleEvent'
        );
    }

}
