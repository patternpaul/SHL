<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 2016-09-24
 * Time: 5:58 PM
 */
namespace App\Http;

use App\Http\Controllers\ServerController;
use Illuminate\Routing\Router;

class ServerRoutes
{
    public static function routes(Router $router)
    {
        $router->get('/server/stats', ['uses' => ServerController::class . '@stats']);
    }
}
