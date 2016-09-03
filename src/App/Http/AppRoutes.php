<?php

namespace App\Http;

use App\Http\Controllers\AdminController;
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
        $router->get('/test', [
            'uses' => SeasonsController::class . '@test'
        ]);
        $router->get('/', [
            'uses' => StatsController::class . '@index'
        ]);
        $router->get('stats/players', [
            'as' => 'player-stats',
            'uses' => StatsController::class . '@playerStats'
        ]);

        $router->get('stats/goalies', [
            'as' => 'goalie-stats',
            'uses' => StatsController::class . '@goalieStats'
        ]);

        $router->get('stats/players/season/{season}/playoff/{playoff}', [
            'as' => 'player-stats-specific',
            'uses' => StatsController::class . '@playerStatsSelected'
        ]);

        $router->get('stats/goalies/season/{season}/playoff/{playoff}', [
            'as' => 'goalie-stats-specific',
            'uses' => StatsController::class . '@goalieStatsSelected'
        ]);


        $router->get('stats', [
            'as' => 'stats-index',
            'uses' => StatsController::class . '@index'
        ]);


        $router->get('records', [
            'as' => 'records',
            'uses' => RecordsController::class . '@index'
        ]);

        $router->get('players/{playerId}/stats/player', [
            'as' => 'player-page-player-stats',
            'uses' => PlayerController::class . '@playerStats'
        ]);

        $router->get('players/{playerId}/stats/goalie', [
            'as' => 'player-page-goalie-stats',
            'uses' => PlayerController::class . '@goalieStats'
        ]);

        $router->get('players/{playerId}/records', [
            'as' => 'player-page-records',
            'uses' => PlayerController::class . '@records'
        ]);

        $router->get('games/{gameId}', [
            'as' => 'game-page',
            'uses' => GamesController::class . '@index'
        ]);

        $router->get('seasons/{season}', [
            'as' => 'season-page',
            'uses' => SeasonsController::class . '@index'
        ]);
    }
}