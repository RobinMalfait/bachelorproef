<?php namespace Test\Appointments;

use Command;
use Skedify\Appointments\AppointmentCanNotBeScheduledInThePast;
use Skedify\Appointments\AppointmentRepository;
use Skedify\Appointments\Commands\ScheduleAppointment;
use Skedify\Appointments\Commands\ScheduleAppointmentHandler;
use Skedify\Appointments\VO\AgentId;
use Skedify\Appointments\VO\AppointmentId;
use Skedify\Appointments\VO\CustomerId;
use Skedify\Appointments\VO\Period;
use Skedify\Appointments\VO\SubjectId;
use Specification;

class ScheduleAppointmentInThePastTest extends Specification
{
    /**
     * Given events to build the aggregate
     *
     * @return array
     */
    public function given()
    {
        return [];
    }

    /**
     * Command to fire
     *
     * @return Command
     */
    public function when()
    {
        return ScheduleAppointment::with(
            AppointmentId::generate(),
            CustomerId::generate(),
            AgentId::generate(),
            SubjectId::generate(),
            Period::fromTimestamps(time() - 1000, time() - 2000)
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
        return new ScheduleAppointmentHandler(new AppointmentRepository($repository));
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
