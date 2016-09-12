<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 2015-12-05
 * Time: 12:45 PM
 */
namespace App\Http;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Routing\Router;

class AuthRoutes
{
    public static function routes(Router $router)
    {

        $router->group(['middleware' => ['web']], function (Router $router) {
            $router->get('auth/login', ['as' => 'login-form', 'uses' => AuthController::class . '@getLogin']);
            $router->post('auth/login', AuthController::class . '@postLogin');
            $router->get('auth/logout', AuthController::class . '@getLogout');
        });

    }
}
