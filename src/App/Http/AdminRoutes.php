<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 2015-12-05
 * Time: 12:45 PM
 */
namespace App\Http;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Routing\Router;

class AdminRoutes
{
    public static function routes(Router $router)
    {



        $router->group(['prefix' => 'admin'], function (Router $router) {
            $router->get('/games/add', [
                'as' => 'admin-game-add',
                'uses' => AdminController::class . '@addGame'
            ]);
            $router->get('/games/{gameId}/edit', [
                'as' => 'admin-game-edit',
                'uses' => AdminController::class . '@editGame'
            ]);
            $router->post('/games/{gameId}', ['uses' => AdminController::class . '@updateGame']);
            $router->post('/games', ['uses' => AdminController::class . '@storeGame']);
            $router->get('/players/add', [
                'as' => 'admin-player-add',
                'uses' => AdminController::class . '@addPlayer'
            ]);
            $router->post('/players', ['uses' => AdminController::class . '@storePlayer']);
            $router->get('/players/{playerId}/edit', [
                'as' => 'admin-player-edit',
                'uses' => AdminController::class . '@editPlayer'
            ]);
            $router->post('/players/{playerId}', ['uses' => AdminController::class . '@updatePlayer']);
            AppRoutes::core($router, 'admin-');
        });
    }
}
