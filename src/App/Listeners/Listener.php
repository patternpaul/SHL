<?php

namespace App\Listeners;

use App\Infrastructure\Logger;
use App\Infrastructure\Util\Encrypt\Encrypt;

abstract class Listener
{
    protected $process_events = true;

    public function getBaseKey()
    {
        return get_class($this);
    }

    public function handleEvent($event)
    {
        if ($this->process_events) {
            $method = $this->getHandleMethod($event);
            if (method_exists($this, $method)) {
                $this->$method($event);
                return true;
            } else {
                throw new \Exception("Method " . $method . " does not exist");
            }
        }
    }

    private function getHandleMethod($event)
    {
        $classParts = explode('\\', get_class($event));
        return 'on' . end($classParts);
    }

    protected function getOrDefault($col, $key, $default = 0)
    {
        if (isset($col[$key])) {
            return $col[$key];
        } else {
            return $default;
        }
    }
}
