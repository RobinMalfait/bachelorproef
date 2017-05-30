<?php namespace Skedify\EventSourcing;

use Skedify\EventSourcing\Events\Dispatcher;
use Skedify\EventSourcing\Events\DomainEvent;
use Skedify\EventSourcing\Serialization\Deserializer;
use Skedify\EventSourcing\Serialization\Serializer;
use Skedify\Storages\EventStorage;
use ReflectionClass;

final class EventStore
{
    protected $storage;

    protected $dispatcher;

    public function __construct(EventStorage $storage, Dispatcher $dispatcher)
    {
        $this->storage = $storage;
        $this->dispatcher = $dispatcher;
    }

    public function save($aggregate)
    {
        $events = $aggregate->releaseEvents();

        /** @var $event DomainEvent */
        foreach ($events as $event) {
            $aggregate->applyAnEvent($event);

            $this->storage->storeEvent(
                $event->getAggregateId(),
                $aggregate->stream_version,
                Serializer::serialize($event)
            );
        }

        $this->dispatcher->dispatch((new ReflectionClass($aggregate))->getName(), $events);
    }

    /**
     * @param $id
     * @return array
     */
    public function getEventsFor($id)
    {
        return $this->storage->searchEventsFor($id, function ($event, $data) {
            return Serializer::deserialize($event, json_decode($data, true));
        });
    }
}
