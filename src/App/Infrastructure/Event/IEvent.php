<?php

namespace App\Infrastructure\Event;

interface IEvent
{
    public function getAggregateId();

    public function setAggregateId($aggregateId);

    public function getAggregateVersion();

    public function setAggregateVersion($version);
}
