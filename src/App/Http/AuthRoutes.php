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

            // Registration routes...
            #$router->get('auth/register', AuthController::class.'@getRegister');
            #$router->post('auth/register', AuthController::class.'@postRegister');
            $router->get('auth/register', [
                'middleware' => 'auth',
                'uses' => AuthController::class . '@getRegister'
            ]);
            $router->post('auth/register', [
                'middleware' => 'auth',
                'uses' => AuthController::class . '@postRegister'
            ]);


            // Password reset link request routes...
            $router->get('password/email', PasswordController::class.'@getEmail');
            $router->post('password/email', PasswordController::class.'@postEmail');

            // Password reset routes...
            $router->get('password/reset/{token}', PasswordController::class.'@getReset');
            $router->post('password/reset', PasswordController::class.'@postReset');
/**

            $router->get('auth/password', [
                'as' => 'password-set',
                'uses' => UserSettingsController::class . '@show'
            ]);
            $router->post('auth/password', [
                'uses' => UserSettingsController::class . '@store'
            ]);
 * **/

        });

    }
}
