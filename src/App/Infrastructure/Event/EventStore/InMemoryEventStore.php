<?php
namespace App\Infrastructure\Event\EventStore;

class InMemoryEventStore implements IEventStore
{
    public $eventArray;
    public $allArray;

    public function __construct()
    {
        $this->eventArray = [];
        $this->allArray = [];
    }

    public function test_reset()
    {
        $this->eventArray = [];
        $this->allArray = [];
    }

    public function add($domainEventArray)
    {
        $returnCollection = [];
        if (count($domainEventArray) > 0) {
            //it's assumed all events are for the same aggregate id
            if (!isset($this->eventArray[$domainEventArray[0]["aggregate_id"]])) {
                $this->eventArray[$domainEventArray[0]["aggregate_id"]] = [];
            }
            foreach ($domainEventArray as $event) {
                $this->eventArray[$event["aggregate_id"]][] = $event;
                $event['id'] = count($this->allArray) + 1;
                $returnCollection[] = $event;
                $this->allArray[] = $event;
            }
        }

        return $returnCollection;
    }

    public function getVersion($aggregateId)
    {
        return count($this->get($aggregateId));
    }

    public function get($aggregateId)
    {
        if (empty($this->eventArray) || !isset($this->eventArray[$aggregateId])) {

            $this->eventArray[$aggregateId] = [];
        }
        return $this->eventArray[$aggregateId];
    }


    public function getSpecificVersion($aggregateId, $aggregateVersion)
    {
        if (empty($this->eventArray) || !isset($this->eventArray[$aggregateId])) {

            $this->eventArray[$aggregateId] = [];
        }

        if (count($this->eventArray[$aggregateId]) >= $aggregateVersion) {
            return $this->eventArray[$aggregateId][$aggregateVersion-1];
        } else {
            return [];
        }
    }
    

    public function getAll()
    {
        return $this->allArray;
    }
}
