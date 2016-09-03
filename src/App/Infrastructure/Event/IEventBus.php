<?php

namespace App\Infrastructure\Event;

interface IEventBus
{
    public function fire($event);
}