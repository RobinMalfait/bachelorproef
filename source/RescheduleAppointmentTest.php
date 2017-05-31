<?php namespace Test\Appointments;

use Skedify\Appointments\AppointmentRepository;
use Skedify\Appointments\Commands\RescheduleAppointment;
use Skedify\Appointments\Commands\RescheduleAppointmentHandler;
use Skedify\Appointments\Events\AppointmentWasRescheduled;
use Skedify\Appointments\Events\AppointmentWasScheduled;
use Skedify\Appointments\VO\AgentId;
use Skedify\Appointments\VO\AppointmentId;
use Skedify\Appointments\VO\CustomerId;
use Skedify\Appointments\VO\DateRange;
use Skedify\Appointments\VO\SubjectId;
use Specification;

class RescheduleAppointmentTest extends Specification
{
  /**
   * Given: een lijst van events
   *
   * @return array
   */
  public function given()
  {
    return [
      new AppointmentWasScheduled(
        AppointmentId::fromString("appointment_1"),
        CustomerId::fromString('customer_1'),
        AgentId::fromString('agent_1'),
        SubjectId::fromString('subject_1'),
        
        // time() retourneert de huidige timestamp
        DateRange::fromTimestamps(time(), time())
      )
    ];
  }

  /**
   * When: een command uitgevoerd wordt
   *
   * @return Command
   */
  public function when()
  {
    return RescheduleAppointment::with(
      // Moet dezelfde id hebben
      AppointmentId::fromString("appointment_id_1"),

      // Moet in de toekomst liggen
      DateRange::fromTimestamps(time() + 1000, time() + 2000)
    );
  }

  /**
   * Command handler, handelt de command af
   *
   * @param $repository
   * @return mixed
   */
  public function handler($repository)
  {
    return new RescheduleAppointmentHandler(
      new AppointmentRepository($repository)
    );
  }

  /**
   * Then: kunnen we valideren of er de nodige events zijn gegenereerd
   */

  /** @test */
  public function one_event_was_produced()
  {
    $this->assertCount(1, $this->producedEvents);
  }

  /** @test */
  public function an_AppointmentWasRescheduled_event_was_produced()
  {
    $this->assertInstanceOf(
      AppointmentWasRescheduled::class,
      $this->producedEvents[0]
    );
  }
}
