<?php
namespace App\Infrastructure\Event\EventStore;

use App\Infrastructure\Aggregate\DomainEvent;
use Illuminate\Support\Facades\DB;

class EloquentEventStore implements IEventStore
{

    public $eventArray;

    public function __construct()
    {
        $this->eventArray = [];
    }


    public function add($domainEventArray)
    {
        $returnCollection = [];

        foreach ($domainEventArray as $item) {
            $item['id'] = DB::table('domain_events')->insertGetId($item);
            $returnCollection[] = $item;
        }
        
        return $returnCollection;
    }

    public function get($aggregateId)
    {

        return DomainEvent::where('aggregate_id', $aggregateId)->orderBy('aggregate_version')->get();
    }

    public function getSpecificVersion($aggregateId, $aggregateVersion)
    {

        return DomainEvent::where([['aggregate_id', $aggregateId],['aggregate_version',$aggregateVersion]])->orderBy('aggregate_version')->get();
    }

    public function getVersion($aggregateId)
    {
        $event = DomainEvent::where('aggregate_id', $aggregateId)->orderBy('aggregate_version', 'desc')->first();
        if (is_null($event) || (count($event) == 0)) {
            return 0;
        } else {
            return $event['aggregate_version'];
        }
    }

    public function getAll()
    {
        return DomainEvent::orderBy('id')->get();
    }
}
