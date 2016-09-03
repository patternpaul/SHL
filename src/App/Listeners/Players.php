<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 2016-08-03
 * Time: 6:25 PM
 */
namespace App\Listeners;

use App\Events\Player\PlayerAdded;
use App\Events\Player\PlayerEdited;
use App\Infrastructure\Database\IRedisDB;
use Illuminate\Contracts\Events\Dispatcher;

class Players extends Listener
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

    public function getAll()
    {
        $keys = $this->redis->hgetall('players:');

        $players = $this->redis->pipeline(
            function ($pipeline) use ($keys) {
                foreach ($keys as $key => $value) {
                    $pipeline->hgetall('player:' . $key);
                }
            }
        );

        $playerReturn = [];
        foreach ($players as $player) {
            $playerReturn[$player['id']] = $player;
        }

        $playerReturn = array_sort($playerReturn, function ($value) {
            return $value['fullName'];
        });


        return $playerReturn;
    }

 

    public function onPlayerEdited(PlayerEdited $event)
    {
        $obj = [];
        $obj["firstName"] = $event->firstName;
        $obj["lastName"] = $event->lastName;
        $obj["fullName"] = $event->firstName . ' ' . $event->lastName;
        $obj['shortName'] = substr($obj['firstName'],0,1).'.'.$obj['lastName'];

        $this->redis->hmset('player:' . $event->getAggregateId(), $obj);
    }

    public function onPlayerAdded(PlayerAdded $event)
    {
        $obj = [];
        $obj["id"] = $event->getAggregateId();
        $obj["firstName"] = $event->firstName;
        $obj["lastName"] = $event->lastName;
        $obj["fullName"] = $event->firstName . ' ' . $event->lastName;
        $obj['shortName'] = substr($obj['firstName'],0,1).'.'.$obj['lastName'];

        $this->redis->hmset('player:' . $event->getAggregateId(), $obj);

        $this->redis->hset(
            'players:',
            $event->getAggregateId(),
            $event->getAggregateId()
        );
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            [
                PlayerAdded::class,
                PlayerEdited::class
            ],
            Players::class . '@handleEvent'
        );
    }

}
