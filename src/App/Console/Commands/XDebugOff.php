<?php

namespace App\Console\Commands;

use App\Infrastructure\Logger;
use Carbon\Carbon;
use GuzzleHttp\HandlerStack;
use Illuminate\Console\Command;
use OpenCloud\Common\Transport\Utils;
use GuzzleHttp\Psr7\Stream;

class XDebugOff extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xdebugoff';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'xdebugoff';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        exec('valet start');
        exec('rm -rf /usr/local/etc/php/7.0/conf.d/ext-xdebug.ini');
        exec('touch /usr/local/etc/php/7.0/conf.d/ext-xdebug.ini');
    }
}
