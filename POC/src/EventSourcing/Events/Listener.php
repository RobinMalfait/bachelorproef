<?php namespace Skedify\EventSourcing\Events;

interface Listener
{
    public function handle(DomainEvent $event);
}
