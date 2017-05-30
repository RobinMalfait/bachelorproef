<?php namespace Skedify\Appointments\Listeners;

use Skedify\EventSourcing\Events\DomainEvent;
use Skedify\EventSourcing\Events\Listener;

final class WhenAppointmentWasRescheduled implements Listener
{
    public function handle(DomainEvent $event)
    {
        var_dump("An appointment was rescheduled.");
    }
}
