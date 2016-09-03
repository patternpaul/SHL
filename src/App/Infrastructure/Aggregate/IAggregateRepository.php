<?php

namespace App\Infrastructure\Aggregate;

interface IAggregateRepository
{
    public function save(AggregateRoot $aggregateRoot);

    public function get($aggregateId);

    public function getAll();

    public function getAllDomainEvents();
}
