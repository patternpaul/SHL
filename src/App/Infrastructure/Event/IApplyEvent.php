<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 15-09-15
 * Time: 7:37 PM
 */

namespace App\Infrastructure\Event;

interface IApplyEvent
{
    public function apply($event);
}
