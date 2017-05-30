<?php namespace Skedify\Appointments\Events;

use Skedify\Appointments\VO\AppointmentId;
use Skedify\Appointments\VO\CustomerId;
use Skedify\Appointments\VO\AgentId;
use Skedify\Appointments\VO\Period;
use Skedify\Appointments\VO\SubjectId;
use Skedify\EventSourcing\Events\DomainEvent;

final class AppointmentWasScheduled implements DomainEvent
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

    public function __construct(AppointmentId $id, CustomerId $customer, AgentId $agent, SubjectId $subject, Period $period)
    {
        $this->appointmentId = $id;
        $this->customerId = $customer;
        $this->agentId = $agent;
        $this->subjectId = $subject;
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

    public function getCustomerId()
    {
        return $this->customerId;
    }

    public function getAgentId()
    {
        return $this->agentId;
    }

    public function getSubjectId()
    {
        return $this->subjectId;
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
            'customer_id' => $this->customerId->toString(),
            'agent_id' => $this->agentId->toString(),
            'subject_id' => $this->subjectId->toString(),
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
            CustomerId::fromString($data['customer_id']),
            AgentId::fromString($data['agent_id']),
            SubjectId::fromString($data['subject_id']),
            Period::fromTimestamps($data['start'], $data['end'])
        );
    }
}
