<?php

namespace App\Infrastructure\Event;

use App\Listeners\Listener;
use Illuminate\Events\Dispatcher;

class TestEventBus extends Dispatcher implements IEventBus
{

    private $myListeners;
    private $listenerClassMap;

    public function __construct()
    {
        $this->myListeners = [];
        $this->listenerClassMap = [];
    }

    public function register(Listener $listener)
    {
        //$this->myListeners[] = $listener;
        $this->listenerClassMap[get_class($listener)] = $listener;
        $listener->subscribe($this);
    }

    public function listen($events, $listener, $priority = 0)
    {
        $classPiece = explode("@", $listener)[0];

        foreach ($events as $event) {
            if (!isset($this->myListeners[$event])) {
                $this->myListeners[$event] = [];
            }

            $this->myListeners[$event][] = $classPiece;
        }
    }


    public function fire($event, $payload = [], $halt = false)
    {
        if (!isset($this->myListeners[get_class($event)])) {
            throw new \Exception("There are no listeners for " . get_class($event) . ". This could either be a missing listener registered to the TestEventBus or non of the Listeners handle this event.");
        }

        $eventListeners = $this->myListeners[get_class($event)];
        foreach ($eventListeners as $listener) {
            $this->listenerClassMap[$listener]->handleEvent($event);
        }
    }

    public function handleEvent($event, $listener)
    {
        $method = $this->getHandleMethod($event);
        if (method_exists($listener, $method)) {
            $listener->$method($event);
        }
    }

    private function getHandleMethod($event)
    {
        $classParts = explode('\\', get_class($event));
        return 'on' . end($classParts);
    }


}
