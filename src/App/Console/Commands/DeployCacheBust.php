<?php

namespace App\Console\Commands;

use App\Infrastructure\Aggregate\AggregateRepository;
use App\Infrastructure\Aggregate\DomainEvent;
use App\Infrastructure\Logger;
use App\Jobs\PurgeUrl;
use App\Listeners\Games;
use App\Listeners\Players;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\HandlerStack;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Event;
use OpenCloud\Common\Transport\Utils;
use GuzzleHttp\Psr7\Stream;

class DeployCacheBust extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploycachebust';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'deploycachebust';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Games $games, Players $players)
    {
        $job = (new PurgeUrl('/stats/players'))->onQueue('purgeurl');
        $this->dispatch($job);
        $job = (new PurgeUrl('/stats/goalies'))->onQueue('purgeurl');
        $this->dispatch($job);
        $job = (new PurgeUrl('/records'))->onQueue('purgeurl');
        $this->dispatch($job);

        foreach ($players->getAll() as $player) {
            $job = (new PurgeUrl('/players/' . $player['id'] . '/stats/player'))->onQueue('purgeurl');
            $this->dispatch($job);
            $job = (new PurgeUrl('/players/' . $player['id'] . '/stats/goalie'))->onQueue('purgeurl');
            $this->dispatch($job);
            $job = (new PurgeUrl('/players/' . $player['id'] . '/records'))->onQueue('purgeurl');
            $this->dispatch($job);
        }


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

        for($i = 1; $i <= $games->getLatestSeason(); $i++) {
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


        foreach ($games->getAllBase() as $game) {
            $job = (new PurgeUrl('games/'.$game['id']))->onQueue('purgeurl');
            $this->dispatch($job);
        }
    }
}
