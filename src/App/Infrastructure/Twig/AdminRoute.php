<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 15-10-29
 * Time: 7:09 PM
 */

namespace App\Infrastructure\Twig;

use Illuminate\Support\Facades\Auth;

class AdminRoute extends \Twig_Extension
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    public function getFunctions()
    {
        return [
            'classname' => new \Twig_SimpleFunction('adminRoute', [$this, 'adminRoute'])
        ];
    }

    public function getName()
    {
        return 'admin_route_twig_extension';
    }

    public function adminRoute($routeName, $params = [])
    {
        $adminprefix = '';
        if (Auth::check()) {
            $adminprefix = 'admin-';
        }
        return route($adminprefix.$routeName, $params);
    }
}
