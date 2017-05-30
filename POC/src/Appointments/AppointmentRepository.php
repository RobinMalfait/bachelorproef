<?php namespace Skedify\Appointments;

use Skedify\EventSourcing\EventSourcingRepository;

final class AppointmentRepository
{
    private $repository;

    public function __construct(EventSourcingRepository $repository)
    {
        $this->repository = $repository;
        $this->repository->setAggregateClass(Appointment::class);
    }

    public function load($id)
    {
        return $this->repository->load($id);
    }

    public function save($aggregateRoot)
    {
        $this->repository->save($aggregateRoot);
    }
}
