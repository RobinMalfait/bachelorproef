<?php namespace Skedify\Appointments;

use Skedify\Appointments\Events\AppointmentWasScheduled;
use Skedify\Appointments\Events\AppointmentWasRescheduled;
use Skedify\Appointments\VO\AppointmentId;
use Skedify\Appointments\VO\CustomerId;
use Skedify\Appointments\VO\AgentId;
use Skedify\Appointments\VO\Period;
use Skedify\Appointments\VO\SubjectId;
use Skedify\Core\AggregateRoot;

final class Appointment extends AggregateRoot
{
    private $appointmentId;

    public static function schedule(AppointmentId $appointmentId, CustomerId $customerId, AgentId $agentId, SubjectId $subjectId, Period $period)
    {
        // Check invariant: appointment can not happen in the past
        if (time() >= $period->getStartTimestamp()) {
            throw new AppointmentCanNotBeScheduledInThePast("Appointment can not be scheduled in the past");
        }
        
        $me = new static();

        $me->apply(new AppointmentWasScheduled($appointmentId, $customerId, $agentId, $subjectId, $period));

        return $me;
    }

    public function reschedule(Period $period)
    {
        // Check invariant: appointment can not happen in the past
        if (time() >= $period->getStartTimestamp()) {
            throw new AppointmentCanNotBeScheduledInThePast("Appointment can not be scheduled in the past");
        }

        $this->apply(new AppointmentWasRescheduled($this->appointmentId, $period));
    }

    /* Respond to events */
    public function applyAppointmentWasScheduled(AppointmentWasScheduled $event)
    {
        $this->appointmentId = $event->getAppointmentId();
    }

    public function applyAppointmentWasRescheduled(AppointmentWasRescheduled $event)
    {
        //
    }
}
