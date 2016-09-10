<?php

namespace App\Listeners\Records;

use App\Aggregates\Game;
use App\Events\Game\PointAdded;
use App\Events\Game\PointRemoved;
use App\Events\Game\TeamPlayerAdded;
use App\Events\Game\TeamPlayerRemoved;
use App\Infrastructure\Database\IRedisDB;
use App\Listeners\Listener;
use Illuminate\Contracts\Events\Dispatcher;

class AssistClub extends Listener
{
    private $redis;
    private $recordStore;

    CONST BASE_KEY = 'clubAssist';
    private $baseKey = AssistClub::BASE_KEY;
    private $clubValues = [250,500,1000];

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

            $obj = $this->redis->hgetall($this->baseKey.':gamecount');
            $obj[$event->playerId] = $this->getOrDefault($obj, $event->playerId) + 1;
            $this->redis->hmset($this->baseKey.':gamecount', $obj);
        }
    }

    public function onTeamPlayerRemoved(TeamPlayerRemoved $event)
    {
        $this->redis->hdel(
            $this->getBaseKey() . ':game:' . $event->gameId.':player-positions',
            $event->playerId
        );

        if ($event->position == Game::PLAYER) {

            $obj = $this->redis->hgetall($this->baseKey.':gamecount');
            $obj[$event->playerId] = $this->getOrDefault($obj, $event->playerId) - 1;
            $this->redis->hmset($this->baseKey.':gamecount', $obj);
        }
    }


    public function onPointAdded(PointAdded $event)
    {
        $playerPosition = $this->redis->hget($this->getBaseKey() . ':game:' . $event->gameId.':player-positions', $event->assistPlayerId);

        if ($playerPosition == Game::PLAYER) {

            $obj = $this->redis->hgetall($this->baseKey.':assistcount');
            $assistCount = $this->getOrDefault($obj, $event->assistPlayerId) + 1;
            $obj[$event->assistPlayerId] = $assistCount;
            $this->redis->hmset($this->baseKey.':assistcount', $obj);

            foreach ($this->clubValues as $clubValue) {
                if ($assistCount == $clubValue) {
                    $gameCount = $this->redis->hgetall($this->baseKey.':gamecount');


                    $obj = $this->redis->hgetall($this->baseKey.':'.$clubValue.':recordholders');
                    $obj[$event->assistPlayerId] = $gameCount[$event->assistPlayerId];
                    $this->redis->hmset($this->baseKey.':'.$clubValue.':recordholders', $obj);
                    $this->storeRecord();
                }
            }

        }
    }
    public function onPointRemoved(PointRemoved $event)
    {
        $playerPosition = $this->redis->hget($this->getBaseKey() . ':game:' . $event->gameId.':player-positions', $event->assistPlayerId);

        if ($playerPosition == Game::PLAYER) {

            $obj = $this->redis->hgetall($this->baseKey.':assistcount');
            $priorValue = $this->getOrDefault($obj, $event->assistPlayerId);
            $assistCount = $priorValue - 1;
            $obj[$event->assistPlayerId] = $assistCount;
            $this->redis->hmset($this->baseKey.':assistcount', $obj);

            foreach ($this->clubValues as $clubValue) {
                if ($priorValue == $clubValue) {
                    $this->redis->hdel($this->baseKey.':'.$clubValue.':recordholders', $event->assistPlayerId);
                    $this->storeRecord();
                }
            }
        }
    }
    private function storeRecord()
    {
        foreach ($this->clubValues as $clubValue) {
            $recordEntries = [];

            $obj = $this->redis->hgetall($this->baseKey.':'.$clubValue.':recordholders');

            foreach ($obj as $playerId => $gameCount) {
                $recordEntries[] = [
                    'entry' =>   RecordStore::playerEncode($playerId). ": ".$clubValue." Assists in " . $gameCount . " games.",
                    'playerKeys' => [$playerId]
                ];
            }

            $this->recordStore->setRecord($this->baseKey.':'.str_pad($clubValue, 4, "0", STR_PAD_LEFT), $clubValue.' Assist Club', $recordEntries);
        }

    }






    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            [
                TeamPlayerAdded::class,
                PointAdded::class,
                TeamPlayerRemoved::class,
                PointRemoved::class
            ],
            AssistClub::class . '@handleEvent'
        );
    }

}
