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

        $router->get('/admin/game/add', ['uses' => AdminController::class . '@addGame']);
        $router->post('/admin/game/add', ['uses' => AdminController::class . '@storeGame']);

    }
}
