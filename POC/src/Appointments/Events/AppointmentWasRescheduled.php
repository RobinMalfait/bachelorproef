<?php namespace Skedify\Appointments\Events;

use Skedify\Appointments\VO\AppointmentId;
use Skedify\Appointments\VO\Period;
use Skedify\EventSourcing\Events\DomainEvent;

final class AppointmentWasRescheduled implements DomainEvent
{

    /** @var \Skedify\Appointments\VO\AppointmentId */
    private $appointmentId;

    /** @var \Skedify\Appointments\VO\Period */
    private $period;

    public function __construct(AppointmentId $id, Period $period)
    {
        $this->appointmentId = $id;
        $this->period = $period;
    }

    public function getAggregateId()
    {
        return $this->appointmentId->toString();
    }

    public function getAppointmentId()
    {
        return $this->appointmentId;
    }

    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'appointment_id' => $this->appointmentId->toString(),
            'start' => $this->period->getStartTimestamp(),
            'end' => $this->period->getEndTimestamp(),
        ];
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public static function deserialize(array $data)
    {
        return new self(
            AppointmentId::fromString($data['appointment_id']),
            Period::fromTimestamps($data['start'], $data['end'])
        );
    }
}
