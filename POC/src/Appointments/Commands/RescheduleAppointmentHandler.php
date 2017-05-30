<?php namespace Skedify\Appointments\Commands;

use Skedify\Appointments\AppointmentRepository;

final class RescheduleAppointmentHandler
{

    /** @var \Skedify\Appointments\AppointmentRepository */
    private $repository;

    public function __construct(AppointmentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(RescheduleAppointment $command)
    {
        $appointment = $this->repository->load($command->getAppointmentId());

        $appointment->reschedule($command->getPeriod());

        $this->repository->save($appointment);
    }
}
