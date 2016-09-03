<?php

namespace App\Console\Commands;

use App\Infrastructure\Aggregate\AggregateRepository;
use App\Infrastructure\Aggregate\DomainEvent;
use App\Infrastructure\Event\EventStore\IEventStore;
use App\Infrastructure\Logger;
use App\Listeners\ops\CacheBust;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Event;
use OpenCloud\Common\Transport\Utils;
use GuzzleHttp\Psr7\Stream;

class ConsumeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consumedata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'consumedata';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(IEventStore $eventStore)
    {
        $this->comment(PHP_EOL . "Starting to get data from core site" . PHP_EOL);
        ini_set('memory_limit', '-1');
        CacheBust::$SHOULD_PROCESS = false;
        $client = new Client(
            [
                'base_uri' => 'http://test.shl-wpg.ca/'
            ]
        );
        $response = $client->request('GET', '/test',
            [
                'http_errors' => false
            ]
        );
        $responseData = $response->getBody();

        $this->comment(PHP_EOL . "Received JSON data. Decoding data" . PHP_EOL);
        $events = json_decode($responseData,true);
        $this->comment(PHP_EOL . "Decoded data. Inserting into core event DB." . PHP_EOL);
        $eventStore->add($events);
        $this->comment(PHP_EOL . "Done getting data from root site." . PHP_EOL);

        $this->comment(PHP_EOL . "Run php artisan refreshredis to process all events." . PHP_EOL);
    }
}
