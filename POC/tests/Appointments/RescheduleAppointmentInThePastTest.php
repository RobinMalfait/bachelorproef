<?php namespace Test\Appointments;

use Command;
use Skedify\Appointments\AppointmentCanNotBeScheduledInThePast;
use Skedify\Appointments\AppointmentRepository;
use Skedify\Appointments\Commands\RescheduleAppointment;
use Skedify\Appointments\Commands\RescheduleAppointmentHandler;
use Skedify\Appointments\Events\AppointmentWasScheduled;
use Skedify\Appointments\VO\AgentId;
use Skedify\Appointments\VO\AppointmentId;
use Skedify\Appointments\VO\CustomerId;
use Skedify\Appointments\VO\Period;
use Skedify\Appointments\VO\SubjectId;
use Specification;

class RescheduleAppointmentInThePastTest extends Specification
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
            Period::fromTimestamps(time() - 1000, time() - 2000) // Reschedule to the past
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
    public function none_events_have_been_produced()
    {
        $this->assertCount(0, $this->producedEvents);
    }

    /** @test */
    public function an_AppointmentCanNotBeScheduledInThePast_exception_was_thrown()
    {
        $this->throws(AppointmentCanNotBeScheduledInThePast::class);
    }
}
