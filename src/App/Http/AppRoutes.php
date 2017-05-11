<?php

namespace App\Http;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DumpController;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\RecordsController;
use App\Http\Controllers\SeasonsController;
use App\Http\Controllers\StatsController;
use Illuminate\Routing\Router;

class AppRoutes
{
    public static function routes(Router $router)
    {
        $router->get('/dump', [
            'uses' => DumpController::class . '@dump'
        ]);

        $router->get('/list', [
            'uses' => DumpController::class . '@list'  
        ]);

        $router->get('/', [
            'uses' => StatsController::class . '@index'
        ]);

        AppRoutes::core($router);
    }

    public static function core(Router $router, $adminprefix = '')
    {
        $router->get('stats/players', [
            'as' => $adminprefix.'player-stats',
            'uses' => StatsController::class . '@playerStats'
        ]);

        $router->get('stats/goalies', [
            'as' => $adminprefix.'goalie-stats',
            'uses' => StatsController::class . '@goalieStats'
        ]);

        $router->get('stats/players/season/{season}/playoff/{playoff}', [
            'as' => $adminprefix.'player-stats-specific',
            'uses' => StatsController::class . '@playerStatsSelected'
        ]);

        $router->get('stats/goalies/season/{season}/playoff/{playoff}', [
            'as' => $adminprefix.'goalie-stats-specific',
            'uses' => StatsController::class . '@goalieStatsSelected'
        ]);


        $router->get('stats', [
            'as' => $adminprefix.'stats-index',
            'uses' => StatsController::class . '@index'
        ]);


        $router->get('records', [
            'as' => $adminprefix.'records',
            'uses' => RecordsController::class . '@index'
        ]);

        $router->get('players/{playerId}/stats/player', [
            'as' => $adminprefix.'player-page-player-stats',
            'uses' => PlayerController::class . '@playerStats'
        ]);

        $router->get('players/{playerId}/stats/goalie', [
            'as' => $adminprefix.'player-page-goalie-stats',
            'uses' => PlayerController::class . '@goalieStats'
        ]);

        $router->get('players/{playerId}/records', [
            'as' => $adminprefix.'player-page-records',
            'uses' => PlayerController::class . '@records'
        ]);

        $router->get('games/{gameId}', [
            'as' => $adminprefix.'game-page',
            'uses' => GamesController::class . '@index'
        ]);

        $router->get('seasons/{season}', [
            'as' => $adminprefix.'season-page',
            'uses' => SeasonsController::class . '@index'
        ]);
    }
}