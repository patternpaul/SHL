<?php

namespace App\Infrastructure\Event\EventStore;

interface IEventStore
{
    public function add($domainEventArray);

    public function get($aggregateId);

    public function getSpecificVersion($aggregateId, $aggregateVersion);

    public function getVersion($aggregateId);

    public function getAll();
}
