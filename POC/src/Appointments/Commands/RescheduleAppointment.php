<?php namespace Skedify\Appointments\Commands;

use Skedify\Appointments\VO\AppointmentId;
use Skedify\Appointments\VO\Period;

final class RescheduleAppointment
{
    /** @var \Skedify\Appointments\VO\AppointmentId */
    private $appointmentId;

    /** @var \Skedify\Appointments\VO\Period */
    private $period;

    private function __construct(AppointmentId $appointmentId, Period $period)
    {
        $this->appointmentId = $appointmentId;
        $this->period = $period;
    }

    public static function with(AppointmentId $appointmentId, Period $period)
    {
        return new self($appointmentId, $period);
    }

    public function getAppointmentId()
    {
        return $this->appointmentId;
    }

    public function getPeriod()
    {
        return $this->period;
    }
}
