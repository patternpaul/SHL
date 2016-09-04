<?php

namespace App\Jobs;

use App\Infrastructure\Logger;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PurgeUrl extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $url;


    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Logger::info("Attempting to purge url" . $this->url);
        $client = new Client(
            [
                'base_uri' => 'http://'. config('app.cache_bust_ip')
            ]
        );
        $response = $client->request('GET', $this->url,
            [
                'http_errors' => false,
                'headers' => [
                    'PunchCache' => '1',
                    'Host' => config('app.cache_bust_host')
                ]
            ]
        );
        $code = $response->getStatusCode();
        if ($code != '200') {
            Logger::error('Purging origin url ' . $this->url . ' was not 200: ' . $response->getBody());
        }

        Logger::info("Completed purging url" . $this->url);
    }
}
