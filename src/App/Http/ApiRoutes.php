<?php

namespace App\Http;

use App\Http\Controllers\Api\ApiStatsController;
use Illuminate\Routing\Router;

class ApiRoutes
{
    public static function routes(Router $router)
    {
        $router->group(['prefix' => 'api'], function (Router $router) {
            $router->get('stats/players/season/{season}/playoff/{playoff}', [
                'as' => 'api-player-stats-specific',
                'uses' => ApiStatsController::class . '@playerStatsSelected'
            ]);
        });
    }
}