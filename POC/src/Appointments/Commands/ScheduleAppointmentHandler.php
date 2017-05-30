<?php namespace Skedify\Appointments\Commands;

use Skedify\Appointments\Appointment;
use Skedify\Appointments\AppointmentRepository;

final class ScheduleAppointmentHandler
{

    /** @var \Skedify\Appointments\AppointmentRepository */
    private $repository;

    public function __construct(AppointmentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(ScheduleAppointment $command)
    {
        $appointment = Appointment::schedule(
            $command->getAppointmentId(),
            $command->getCustomerId(),
            $command->getAgentId(),
            $command->getSubjectId(),
            $command->getPeriod()
        );

        $this->repository->save($appointment);
    }
}
