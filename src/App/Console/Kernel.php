<?php

namespace App\Console;

use App\Console\Commands\ConsumeData;
use App\Console\Commands\CreateUser;
use App\Console\Commands\RefreshRedis;
use App\Console\Commands\SHL;
use App\Console\Commands\XDebugOff;
use App\Console\Commands\XDebugOn;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        XDebugOff::class,
        XDebugOn::class,
        RefreshRedis::class,
        CreateUser::class,
        ConsumeData::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }
}
