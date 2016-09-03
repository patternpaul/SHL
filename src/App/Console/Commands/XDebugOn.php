<?php

namespace App\Console\Commands;

use App\Infrastructure\Logger;
use Carbon\Carbon;
use GuzzleHttp\HandlerStack;
use Illuminate\Console\Command;
use OpenCloud\Common\Transport\Utils;
use GuzzleHttp\Psr7\Stream;

class XDebugOn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xdebugon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'xdebugon';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        exec('valet stop');
        exec('rm -rf /usr/local/etc/php/7.0/conf.d/ext-xdebug.ini');
        exec('echo "[xdebug]" >> /usr/local/etc/php/7.0/conf.d/ext-xdebug.ini');
        exec('echo "zend_extension="/usr/local/opt/php70-xdebug/xdebug.so"" >> /usr/local/etc/php/7.0/conf.d/ext-xdebug.ini');
        exec('echo "xdebug.profiler_enable = 0" >> /usr/local/etc/php/7.0/conf.d/ext-xdebug.ini');
        exec('echo "xdebug.profiler_enable_trigger = 0" >> /usr/local/etc/php/7.0/conf.d/ext-xdebug.ini');
    }
}
