<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 2016-08-24
 * Time: 9:03 PM
 */
namespace App\Listeners\ops;

use App\Events\Game\GameAdded;
use App\Events\Game\GameCompleted;
use App\Events\Game\PointAdded;
use App\Events\Game\TeamPlayerAdded;
use App\Events\Player\PlayerEdited;
use App\Infrastructure\Database\IRedisDB;
use App\Jobs\PurgeUrl;
use App\Listeners\Listener;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;

class CacheBust extends Listener
{
    use DispatchesJobs;

    private $redis;

    public static $SHOULD_PROCESS = true;

    /**
     * CacheBust constructor.
     * @param $redis
     */
    public function __construct(IRedisDB $redis)
    {
        $this->redis = $redis;
        $this->process_events = CacheBust::$SHOULD_PROCESS;
    }

    //could be smarter
    public function onGameAdded(GameAdded $event)
    {
        $this->redis->del('cachebust:players:');
        $this->redis->hset('cachebust:game:', 'season', $event->season);
        $this->redis->hset('cachebust:game:', 'playoff', $event->playoff);


        $lastSeason = $this->redis->hget('cachebust:last-game:', 'season');
        $storeSeason = $lastSeason;
        if (!is_numeric($storeSeason) || ($event->season > $storeSeason)) {
            $storeSeason = $event->season;
        }

        $this->redis->hset(
            'cachebust:last-game:',
            'season',
            $storeSeason
        );
    }

    public function onTeamPlayerAdded(TeamPlayerAdded $event)
    {
        $this->redis->hset(
            'cachebust:players:',
            $event->playerId,
            $event->playerId
        );
    }

    public function onPointAdded(PointAdded $event)
    {
        $this->redis->hset(
            'cachebust:players:',
            $event->goalPlayerId,
            $event->goalPlayerId
        );

        if ($event->assistPlayerId !== '') {
            $this->redis->hset(
                'cachebust:players:',
                $event->assistPlayerId,
                $event->assistPlayerId
            );
        }


    }
    public function onGameCompleted(GameCompleted $event)
    {
        $game = $this->redis->hgetall('cachebust:game:');

        $job = (new PurgeUrl('/stats/players/season/'.$game['season'].'/playoff/'.$game['playoff']))->onQueue('purgeurl');
        $this->dispatch($job);
        $job = (new PurgeUrl('/stats/goalies/season/'.$game['season'].'/playoff/'.$game['playoff']))->onQueue('purgeurl');
        $this->dispatch($job);
        $job = (new PurgeUrl('/stats/players'))->onQueue('purgeurl');
        $this->dispatch($job);
        $job = (new PurgeUrl('/stats/goalies'))->onQueue('purgeurl');
        $this->dispatch($job);
        $players = $this->redis->hgetall('cachebust:players:');
        foreach ($players as $key => $player) {
            $job = (new PurgeUrl('/players/' . $key . '/stats/player'))->onQueue('purgeurl');
            $this->dispatch($job);
            $job = (new PurgeUrl('/players/' . $key . '/stats/goalie'))->onQueue('purgeurl');
            $this->dispatch($job);
            $job = (new PurgeUrl('/players/' . $key . '/records'))->onQueue('purgeurl');
            $this->dispatch($job);
        }
    }

    public function onPlayerEdited(PlayerEdited $event)
    {
        $lastSeason = $this->redis->hget('cachebust:last-game:', 'season');

        $job = (new PurgeUrl('/stats/players/season/all/playoff/0'))->onQueue('purgeurl');
        $this->dispatch($job);
        $job = (new PurgeUrl('/stats/goalies/season/all/playoff/0'))->onQueue('purgeurl');
        $this->dispatch($job);
        $job = (new PurgeUrl('/stats/players/season/all/playoff/1'))->onQueue('purgeurl');
        $this->dispatch($job);
        $job = (new PurgeUrl('/stats/goalies/season/all/playoff/1'))->onQueue('purgeurl');
        $this->dispatch($job);
        $job = (new PurgeUrl('/stats/players/season/all/playoff/all'))->onQueue('purgeurl');
        $this->dispatch($job);
        $job = (new PurgeUrl('/stats/goalies/season/all/playoff/all'))->onQueue('purgeurl');
        $this->dispatch($job);

        for($i = 1; $i <= $lastSeason; $i++) {
            $job = (new PurgeUrl('/stats/players/season/'.$i.'/playoff/0'))->onQueue('purgeurl');
            $this->dispatch($job);
            $job = (new PurgeUrl('/stats/goalies/season/'.$i.'/playoff/0'))->onQueue('purgeurl');
            $this->dispatch($job);
            $job = (new PurgeUrl('/stats/players/season/'.$i.'/playoff/1'))->onQueue('purgeurl');
            $this->dispatch($job);
            $job = (new PurgeUrl('/stats/goalies/season/'.$i.'/playoff/1'))->onQueue('purgeurl');
            $this->dispatch($job);
            $job = (new PurgeUrl('/stats/players/season/'.$i.'/playoff/all'))->onQueue('purgeurl');
            $this->dispatch($job);
            $job = (new PurgeUrl('/stats/goalies/season/'.$i.'/playoff/all'))->onQueue('purgeurl');
            $this->dispatch($job);
        }

        $job = (new PurgeUrl('/stats/players'))->onQueue('purgeurl');
        $this->dispatch($job);
        $job = (new PurgeUrl('/stats/goalies'))->onQueue('purgeurl');
        $this->dispatch($job);

        $job = (new PurgeUrl('/players/' . $event->playerId . '/stats/player'))->onQueue('purgeurl');
        $this->dispatch($job);
        $job = (new PurgeUrl('/players/' . $event->playerId . '/stats/goalie'))->onQueue('purgeurl');
        $this->dispatch($job);
        $job = (new PurgeUrl('/players/' . $event->playerId . '/records'))->onQueue('purgeurl');
        $this->dispatch($job);
        $job = (new PurgeUrl('/records'))->onQueue('purgeurl');
        $this->dispatch($job);
    }


    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            [
                GameAdded::class,
                TeamPlayerAdded::class,
                PointAdded::class,
                GameCompleted::class,
                PlayerEdited::class
            ],
            CacheBust::class . '@handleEvent'
        );
    }
}
