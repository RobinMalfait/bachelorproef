<?php namespace Skedify\EventSourcing\Events;

use Skedify\EventSourcing\Serialization\Serializable;

interface DomainEvent extends Serializable
{
    public function getAggregateId();
}
