<?php

namespace App\Infrastructure\Aggregate;

use App\Infrastructure\Event\IApplyEvent;
use App\Infrastructure\Event\IEvent;
use Assert\Assertion;
use Exception;
use Rhumsaa\Uuid\Uuid;

class AggregateRoot implements IApplyEvent
{
    protected $uncommittedEvents = [];
    protected $aggregateId;
    protected $currentVersion = 0;
    protected $childAggregates = [];
    protected $childAggregateTypes = [];

    public function getAggregateId()
    {
        if (is_null($this->aggregateId)) {
            throw new \Exception("The Aggregate Id has not been set on " . get_class($this));
        }
        return $this->aggregateId;
    }

    public function getCurrentVersion()
    {
        return $this->currentVersion;
    }

    protected function setAggregateId($aggregateId)
    {
        $this->aggregateId = $aggregateId;
    }

    public function generateAggregateId()
    {
        $generatedId = Uuid::uuid4();
        return $generatedId->toString();
    }

    public function apply($event)
    {

        $properties = get_object_vars($event);
        foreach ($properties as $key => $value) {
            if (is_null($value)) {
                throw new Exception("The field " . $key . " on event " . get_class($event) . " is null.");
            }
        }

        $this->applyEvent($event);
        $this->uncommittedEvents[] = $event;
    }

    public function rehydrate($domainEvents)
    {
        if ($this->currentVersion != 0) {
            throw new Exception("This aggregate has already been re-hydrated");
        }
        //dd($domainEvents);
        foreach ($domainEvents as $event) {
            $this->currentVersion += 1;
            $this->applyEvent($event);
        }
    }

    public function getUncommittedEvents()
    {
        $returnEvents = $this->uncommittedEvents;
        $this->uncommittedEvents = [];
        return $returnEvents;
    }

    private function applyEvent(IEvent $event)
    {

        $method = $this->getApplyMethod($event);
        if (!method_exists($this, $method))
        {
            //TODO: Should this fail silently??
            throw new Exception("The Apply method " . $method . " for this event has not been defined on " . get_class($this));
        }

        $this->$method($event);
    }

    private function getApplyMethod(IEvent $event)
    {
        $classParts = explode('\\', get_class($event));
        return 'apply' . end($classParts);
    }
}
