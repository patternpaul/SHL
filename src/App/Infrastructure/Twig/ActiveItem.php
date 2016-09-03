<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 15-10-29
 * Time: 7:09 PM
 */

namespace App\Infrastructure\Twig;

class ActiveItem extends \Twig_Extension
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    public function getFunctions()
    {
        return [
            'classname' => new \Twig_SimpleFunction('isActive', [$this, 'isActive'])
        ];
    }

    public function getName()
    {
        return 'active_item_twig_extension';
    }

    public function isActive($active, $returnClass = "active")
    {
        return ($active == true) ? $returnClass : '';
    }
}
