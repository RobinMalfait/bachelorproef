<?php namespace Skedify\Appointments\Projectors;

use Skedify\Appointments\Events\AppointmentWasRescheduled;
use Skedify\Appointments\Events\AppointmentWasScheduled;
use Skedify\Storages\JsonDatabase;

final class AppointmentProjector
{
    protected $jsonDatabase;

    public function __construct(JsonDatabase $jsonDatabase)
    {
        $this->jsonDatabase = $jsonDatabase;
    }

    public function projectAppointmentWasScheduled(AppointmentWasScheduled $event)
    {
        $this->jsonDatabase->insert([
            'id' => $event->getAppointmentId()->toString(),
            'subject_id' => $event->getSubjectId()->toString(),
            'agent_id' => $event->getAgentId()->toString(),
            'customer_id' => $event->getCustomerId()->toString(),
            'start_at' => $event->getPeriod()->getStartDate()->format("d-m-Y h:i:s"),
            'end_at' => $event->getPeriod()->getEndDate()->format("d-m-Y h:i:s"),
        ]);
    }

    public function projectAppointmentWasRescheduled(AppointmentWasRescheduled $event)
    {
        $this->jsonDatabase->update($event, function ($row) use ($event) {
            $row['start_at'] = $event->getPeriod()->getStartDate()->format("d-m-Y h:i:s");
            $row['end_at'] = $event->getPeriod()->getEndDate()->format("d-m-Y h:i:s");

            return $row;
        });
    }
}
