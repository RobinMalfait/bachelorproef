<?php namespace Test\Appointments;

use Command;
use Skedify\Appointments\AppointmentRepository;
use Skedify\Appointments\Commands\RescheduleAppointment;
use Skedify\Appointments\Commands\RescheduleAppointmentHandler;
use Skedify\Appointments\Events\AppointmentWasRescheduled;
use Skedify\Appointments\Events\AppointmentWasScheduled;
use Skedify\Appointments\VO\AgentId;
use Skedify\Appointments\VO\AppointmentId;
use Skedify\Appointments\VO\CustomerId;
use Skedify\Appointments\VO\Period;
use Skedify\Appointments\VO\SubjectId;
use Specification;

class RescheduleAppointmentTest extends Specification
{
    /**
     * Given events to build the aggregate
     *
     * @return array
     */
    public function given()
    {
        return [
            new AppointmentWasScheduled(
                AppointmentId::fromString("appointment_id_1"),
                CustomerId::generate(),  // It doesn't matter which id, for this test case
                AgentId::generate(),  // It doesn't matter which id, for this test case
                SubjectId::generate(), // It doesn't matter which id, for this test case
                Period::fromTimestamps(time(), time())
            )
        ];
    }

    /**
     * Command to fire
     *
     * @return Command
     */
    public function when()
    {
        return RescheduleAppointment::with(
            AppointmentId::fromString("appointment_id_1"),
            Period::fromTimestamps(time() + 1000, time() + 2000) // Must be in the future
        );
    }

    /**
     * The command handler
     *
     * @param $repository
     * @return mixed
     */
    public function handler($repository)
    {
        return new RescheduleAppointmentHandler(new AppointmentRepository($repository));
    }

    /** @test */
    public function one_event_has_been_produced()
    {
        $this->assertCount(1, $this->producedEvents);
    }

    /** @test */
    public function an_AppointmentWasRescheduled_event_was_produced()
    {
        $this->assertInstanceOf(AppointmentWasRescheduled::class, $this->producedEvents[0]);
    }
}
