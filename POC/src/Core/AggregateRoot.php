<?php namespace Skedify\Core;

use ReflectionClass;
use Skedify\EventSourcing\Events\DomainEvent;

abstract class AggregateRoot
{
    public $stream_version = -1;

    protected $recordedEvents;

    public function releaseEvents()
    {
        $events = $this->recordedEvents;

        $this->recordedEvents = [];

        return $events;
    }

    public function apply(DomainEvent $event)
    {
        $this->recordedEvents[] = $event;
    }

    public static function replayEvents($events)
    {
        return array_reduce($events, function ($me, $event) {
            return $me->applyAnEvent($event);
        }, new static);
    }

    public function applyAnEvent(DomainEvent $event)
    {
        $reflection = new ReflectionClass($event);
        $method = "apply" . $reflection->getShortName();

        call_user_func([$this, $method], $event);

        $this->stream_version++;

        return $this;
    }
}
