<?php
namespace App\Infrastructure\Aggregate;

use App\Events\Organization\Disbursement\DisbursementInvoiced;
use App\Events\Organization\Expense\ExpenseInvoiced;
use App\Events\Organization\ProgressNote\ProgressNoteInvoiced;
use App\Infrastructure\Aggregate\Exceptions\AggregateDoesNotExistOrContainsNoEvents;
use App\Infrastructure\Event\EventStore\IEventStore;
use App\Infrastructure\Event\IEvent;
use App\Infrastructure\Event\IEventBus;
use App\Infrastructure\Event\IEventUser;
use App\Infrastructure\Util\metrics\Metrics;


class AggregateRepository implements IAggregateRepository
{
    //TODO: Interface this shit

    private $eventStore;
    private $eventBus;

    public function __construct(IEventStore $eventStore, IEventBus $eventBus)
    {
        $this->eventStore = $eventStore;
        $this->eventBus = $eventBus;
    }


    /**
     * @param AggregateRoot $aggregateRoot
     * @throws AggregateException
     * @throws \Exception
     */
    public function save(AggregateRoot $aggregateRoot)
    {
        Metrics::start('aggregateRepository.save');

        //TODO: Put logic in to verify version change. Can potentially enforce this through PK on aggID, version,
        // contract in 1 transaction
        //TODO: Track user that stored the data as well
        $events = $aggregateRoot->getUncommittedEvents();
        $currentVersion = $aggregateRoot->getCurrentVersion();
        $aggregateId = $aggregateRoot->getAggregateId();
        $aggregateContract = get_class($aggregateRoot);
        $domainEvents = [];
        $eventVersion = $currentVersion;
        $savedEvent = null;

        foreach ($events as $event) {
            $domainEvent = [];
            $eventVersion += 1;
            /* @var $event IEvent */
            $event->setAggregateVersion($eventVersion);

            $domainEvent['aggregate_id'] = $aggregateId;
            $domainEvent['aggregate_contract'] = $aggregateContract;
            $domainEvent['aggregate_version'] = $event->getAggregateVersion();
            $domainEvent['event_contract'] = get_class($event);
            $domainEvent['event_data'] = serialize($event);
            $domainEvents[] = $domainEvent;

        }

        //TODO: Primary key against aggId and AggVer should do this as well
        $storedVersion = $this->eventStore->getVersion($aggregateId);
        if (($storedVersion != $currentVersion) || ($storedVersion + count($domainEvents) != $eventVersion)) {
            throw new AggregateException("Someone has changed this before you were able to save. Please try again.");
        }


        $this->eventStore->add($domainEvents);

        Metrics::start('aggregateRepository.save.fire');
        foreach ($events as $event) {
            //send events
            $this->eventBus->fire($event);
        }
        Metrics::end('aggregateRepository.save.fire');

        Metrics::end('aggregateRepository.save');
    }

    /**
     * function to store aggregate events
     */
    public function get($aggregateId)
    {
        Metrics::start('aggregateRepository.get');
        $domainEvents = $this->eventStore->get($aggregateId);

        if (!(count($domainEvents) > 0)) {
            throw new AggregateDoesNotExistOrContainsNoEvents();
        }
        //assumption that all events are for the same aggregate.
        $aggregateContract = $domainEvents[0]["aggregate_contract"];

        $aggregate = new $aggregateContract;

        $events = [];

        foreach ($domainEvents as $domainEvent) {
            $events[] = unserialize($domainEvent["event_data"]);
        }

        Metrics::start('aggregateRepository.get.rehydrate');
        $aggregate->rehydrate($events);
        Metrics::end('aggregateRepository.get.rehydrate');

        Metrics::end('aggregateRepository.get');
        return $aggregate;
    }

    public function getAll()
    {
        $domainEvents = $this->eventStore->getAll();

        $events = [];

        foreach ($domainEvents as $domainEvent) {
            $events[] = unserialize($domainEvent["event_data"]);
        }


        return $events;
    }

    public function getAllDomainEvents()
    {
        $domainEvents = $this->eventStore->getAll();

        return $domainEvents;
    }
}
