<?php

namespace App\Providers;

use App\Listeners\Games;
use App\Listeners\GoalieStats;
use App\Listeners\ops\CacheBust;
use App\Listeners\Players;
use App\Listeners\PlayerStats;
use App\Listeners\Records\AssistClub;
use App\Listeners\Records\CupWinners;
use App\Listeners\Records\GoalClub;
use App\Listeners\Records\LeastGoalsInARegularSeasonGame;
use App\Listeners\Records\LongestRegularSeasonGame;
use App\Listeners\Records\MostAssistsInARegularSeason;
use App\Listeners\Records\MostGoalsInARegularSeason;
use App\Listeners\Records\MostGoalsInARegularSeasonGame;
use App\Listeners\Records\MostPointsInARegularSeason;
use App\Listeners\Records\PointClub;
use App\Listeners\Records\RegularSeasonOvertimeGames;
use App\Listeners\Records\RegularSeasonShutOutClub;
use App\Listeners\Records\ShortestRegularSeasonGame;
use App\Listeners\Records\TeamRegularSeasonRecord;
use App\Listeners\Stats;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
    ];

    protected $subscribe = [
        Players::class,
        Games::class,
        PlayerStats::class,
        GoalieStats::class,
        GoalClub::class,
        AssistClub::class,
        PointClub::class,
        TeamRegularSeasonRecord::class,
        RegularSeasonOvertimeGames::class,
        LongestRegularSeasonGame::class,
        ShortestRegularSeasonGame::class,
        MostGoalsInARegularSeasonGame::class,
        LeastGoalsInARegularSeasonGame::class,
        MostPointsInARegularSeason::class,
        MostGoalsInARegularSeason::class,
        MostAssistsInARegularSeason::class,
        RegularSeasonShutOutClub::class,
        CupWinners::class,
        CacheBust::class
    ];


    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
