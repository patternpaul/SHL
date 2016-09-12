<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 2016-08-24
 * Time: 9:03 PM
 */
namespace App\Listeners\ops;

use App\Aggregates\Game;
use App\Events\Game\GameCompleted;
use App\Events\Player\PlayerEdited;
use App\Infrastructure\Database\IRedisDB;
use App\Jobs\PurgeUrl;
use App\Listeners\Games;
use App\Listeners\Listener;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;

class CacheBust extends Listener
{
    use DispatchesJobs;

    private $redis;
    private $games;
    
    public static $SHOULD_PROCESS = true;

    /**
     * CacheBust constructor.
     * @param $redis
     */
    public function __construct(IRedisDB $redis, Games $games)
    {
        $this->redis = $redis;
        $this->process_events = CacheBust::$SHOULD_PROCESS;
        $this->games = $games;
    }
    
    public function onGameCompleted(GameCompleted $event)
    {
        $game = $this->games->getById($event->gameId);

        $job = (new PurgeUrl('/stats/players/season/'.$game['season'].'/playoff/'.$game['playoff']))->onQueue('purgeurl');
        $this->dispatch($job);
        $job = (new PurgeUrl('/stats/goalies/season/'.$game['season'].'/playoff/'.$game['playoff']))->onQueue('purgeurl');
        $this->dispatch($job);
        $job = (new PurgeUrl('/stats/players'))->onQueue('purgeurl');
        $this->dispatch($job);
        $job = (new PurgeUrl('/stats/goalies'))->onQueue('purgeurl');
        $this->dispatch($job);
        
        foreach ($game[Game::BLACK_TEAM."AllPlayers"] as $player) {
            $job = (new PurgeUrl('/players/' . $player['id'] . '/stats/player'))->onQueue('purgeurl');
            $this->dispatch($job);
            $job = (new PurgeUrl('/players/' . $player['id'] . '/stats/goalie'))->onQueue('purgeurl');
            $this->dispatch($job);
            $job = (new PurgeUrl('/players/' . $player['id'] . '/records'))->onQueue('purgeurl');
            $this->dispatch($job);
        }
        foreach ($game[Game::WHITE_TEAM."AllPlayers"] as $player) {
            $job = (new PurgeUrl('/players/' . $player['id'] . '/stats/player'))->onQueue('purgeurl');
            $this->dispatch($job);
            $job = (new PurgeUrl('/players/' . $player['id'] . '/stats/goalie'))->onQueue('purgeurl');
            $this->dispatch($job);
            $job = (new PurgeUrl('/players/' . $player['id'] . '/records'))->onQueue('purgeurl');
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
                GameCompleted::class,
                PlayerEdited::class
            ],
            CacheBust::class . '@handleEvent'
        );
    }
}
