<?php

namespace App\Console\Commands;

use App\Infrastructure\Aggregate\AggregateRepository;
use App\Infrastructure\Aggregate\DomainEvent;
use App\Infrastructure\Logger;
use App\Listeners\ops\CacheBust;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\HandlerStack;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Event;
use OpenCloud\Common\Transport\Utils;
use GuzzleHttp\Psr7\Stream;

class RefreshRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refreshredis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'refreshredis';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        CacheBust::$SHOULD_PROCESS = false;
        $this->comment(PHP_EOL . "Resetting all events into handlers." . PHP_EOL);

        $aggregateFactory = app(AggregateRepository::class);
        $events = $aggregateFactory->getAll();
        $redis = app('redis');
        $redis->flushdb();

        $eventCount = 0;
        foreach ($events as $event) {
            $eventCount++;
            /** @noinspection PhpUndefinedMethodInspection */
            Event::fire($event);
            $this->comment("Fired event ".$eventCount);
        }

        $this->comment(PHP_EOL . "Completed resetting all events into handlers." . PHP_EOL);
    }
}
