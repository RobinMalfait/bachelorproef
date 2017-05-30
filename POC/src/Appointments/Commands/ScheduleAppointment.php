<?php namespace Skedify\Appointments\Commands;

use Skedify\Appointments\VO\AppointmentId;
use Skedify\Appointments\VO\CustomerId;
use Skedify\Appointments\VO\AgentId;
use Skedify\Appointments\VO\Period;
use Skedify\Appointments\VO\SubjectId;

final class ScheduleAppointment
{
    /** @var \Skedify\Appointments\VO\AppointmentId */
    private $appointmentId;

    /** @var \Skedify\Appointments\VO\CustomerId */
    private $customerId;

    /** @var \Skedify\Appointments\VO\AgentId */
    private $agentId;

    /** @var \Skedify\Appointments\VO\SubjectId */
    private $subjectId;

    /** @var \Skedify\Appointments\VO\Period */
    private $period;

    private function __construct(AppointmentId $appointmentId, CustomerId $customerId, AgentId $agentId, SubjectId $subjectId, Period $period)
    {
        $this->appointmentId = $appointmentId;
        $this->customerId = $customerId;
        $this->agentId = $agentId;
        $this->subjectId = $subjectId;
        $this->period = $period;
    }

    public static function with(AppointmentId $appointmentId, CustomerId $customerId, AgentId $agentId, SubjectId $subjectId, Period $period)
    {
        return new self($appointmentId, $customerId, $agentId, $subjectId, $period);
    }

    public function getAppointmentId()
    {
        return $this->appointmentId;
    }

    public function getCustomerId()
    {
        return $this->customerId;
    }

    public function getAgentId()
    {
        return $this->agentId;
    }

    public function getsubjectId()
    {
        return $this->subjectId;
    }

    public function getPeriod()
    {
        return $this->period;
    }
}
