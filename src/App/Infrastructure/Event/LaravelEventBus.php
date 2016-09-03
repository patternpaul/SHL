<?php
namespace App\Infrastructure\Event;

use Illuminate\Support\Facades\Event;

class LaravelEventBus implements IEventBus
{
    public function fire($event)
    {
        Event::fire($event);
    }
}
