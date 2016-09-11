<?php

namespace App\Listeners\Records;

use App\Aggregates\Game;
use App\Events\Game\GameAdded;
use App\Events\Game\GameEdited;
use App\Events\Game\PointAdded;
use App\Events\Game\PointRemoved;
use App\Events\Game\TeamPlayerAdded;
use App\Events\Game\TeamPlayerRemoved;
use App\Infrastructure\Database\IRedisDB;
use App\Listeners\Listener;
use Illuminate\Contracts\Events\Dispatcher;

class MostGoalsInARegularSeason extends Listener
{
    private $redis;
    private $recordStore;

    CONST BASE_KEY = 'mostGoalsInARegularSeason';
    private $baseKey = MostGoalsInARegularSeason::BASE_KEY;

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

    public function onTeamPlayerRemoved(TeamPlayerRemoved $event)
    {
        $this->redis->hdel(
            $this->getBaseKey() . ':game:' . $event->gameId.':player-positions',
            $event->playerId
        );

        if ($event->position == Game::PLAYER) {

            $obj = $this->redis->hgetall($this->getBaseKey().':gamecount');
            $obj[$event->playerId] = $this->getOrDefault($obj, $event->playerId) - 1;
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
    public function onGameEdited(GameEdited $event)
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
            $playerPosition = $this->redis->hget($this->getBaseKey() . ':game:' . $event->gameId.':player-positions', $event->goalPlayerId);

            if ($playerPosition == Game::PLAYER) {
                $obj = $this->redis->hgetall($this->baseKey.':player:'.$event->goalPlayerId);
                $pointCount = $this->getOrDefault($obj, $game['season']) + 1;
                $obj[$game['season']] = $pointCount;
                $this->redis->hmset($this->baseKey.':player:'.$event->goalPlayerId, $obj);
                $this->redis->hset($this->baseKey.':playerList:', $event->goalPlayerId.':'.$game['season'], $pointCount);

                if ($pointCount > $currentMost) {
                    $this->redis->del($this->getBaseKey() . ':record:holders');
                }
                if ($pointCount >= $currentMost) {
                    $this->redis->hset($this->getBaseKey() . ':record', 'currentMost', $pointCount);
                    $this->redis->hset($this->getBaseKey() . ':record:holders', $event->goalPlayerId.':'.$game['season'], $pointCount);
                    $this->storeRecord();
                }
            }
        }
    }

    public function onPointRemoved(PointRemoved $event)
    {
        $game = $this->redis->hgetall($this->getBaseKey() . ':game:' . $event->gameId);

        if ($game["playoff"] == 0) {
            $playerPosition = $this->redis->hget($this->getBaseKey() . ':game:' . $event->gameId.':player-positions', $event->goalPlayerId);

            if ($playerPosition == Game::PLAYER) {
                $obj = $this->redis->hgetall($this->baseKey.':player:'.$event->goalPlayerId);
                $pointCount = $this->getOrDefault($obj, $game['season']) - 1;
                $obj[$game['season']] = $pointCount;
                $this->redis->hmset($this->baseKey.':player:'.$event->goalPlayerId, $obj);
                $this->redis->hset($this->baseKey.':playerList:', $event->goalPlayerId.':'.$game['season'], $pointCount);

                $this->redis->del($this->getBaseKey() . ':record:holders');

                $pointList = $this->redis->hvals($this->baseKey.':playerList:');
                sort($pointList);
                $maxGoal = end($pointList);
                $this->redis->hset($this->getBaseKey() . ':record', 'currentMost', $maxGoal);

                foreach ($this->redis->hgetall($this->baseKey.':playerList:') as $identifier => $count) {
                    if ($count == $maxGoal) {
                        $this->redis->hset($this->getBaseKey() . ':record:holders', $identifier, $maxGoal);
                    }
                }

                $this->storeRecord();
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
                'entry' =>   RecordStore::seasonEncode($playerData[1])." ".RecordStore::playerEncode($playerData[0])." ".$currentMost." Goals.",
                'playerKeys' => [$playerData[0]]
            ];
        }
        $this->recordStore->setRecord($this->baseKey, 'Most Goals Overall By A Player In One Season', $recordEntries);
    }


    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            [
                TeamPlayerAdded::class,
                GameAdded::class,
                PointAdded::class,
                GameEdited::class,
                PointRemoved::class,
                TeamPlayerRemoved::class
            ],
            MostGoalsInARegularSeason::class . '@handleEvent'
        );
    }

}
