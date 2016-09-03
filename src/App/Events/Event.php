<?php
/**
 * Created by PhpStorm.
 * User: Paul.Everton
 * Date: 4/4/2015
 * Time: 4:42 PM
 */

namespace App\Events;

use App\Infrastructure\AggregateFactory;
use App\Infrastructure\Event\IEvent;
use App\Infrastructure\Util\Encrypt\Encrypt;
use App\Infrastructure\Util\Encrypt\IKeyRepo;
use Exception;
use Illuminate\Queue\SerializesModels;

class Event implements IEvent
{
    use SerializesModels;

    protected $aggregateId;
    protected $childAggregateId;
    protected $aggregateVersion;


    public function getAggregateId()
    {
        if (is_null($this->aggregateId)) {
            throw new Exception("The Aggregate ID has not been set for this event.");
        }
        return $this->aggregateId;
    }


    public function setAggregateId($aggregateId)
    {
        if (!is_null($this->aggregateId)) {
            //don't let the aggregateId be set twice
            return;
        }
        $this->aggregateId = $aggregateId;
    }

    public function getChildAggregateId()
    {
        if (is_null($this->childAggregateId)) {
            throw new Exception("The Child Aggregate ID has not been set for this event.");
        }
        return $this->childAggregateId;
    }

    public function setChildAggregateId($childAggregateId)
    {
        if (!is_null($this->childAggregateId)) {
            //don't let the childAggregateId be set twice
            return;
        }
        $this->childAggregateId = $childAggregateId;
    }

    public function isChildAggregateIdSet()
    {
        return is_null($this->childAggregateId);
    }

    public function getAggregateVersion()
    {
        if (is_null($this->aggregateVersion)) {
            throw new Exception("The Aggregate Version has not been set for this event.");
        }
        return $this->aggregateVersion;
    }

    public function setAggregateVersion($version)
    {
        if (!is_null($this->aggregateVersion)) {
            //don't let the aggregateId be set twice
            return;
        }
        $this->aggregateVersion = $version;
    }

    //these are here because calling functions that don't exist is really slow.
    //http://php.net/manual/en/oop4.magic-functions.php
    public function __wakeup()
    {
    }
}
