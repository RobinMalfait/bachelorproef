<?php

use Skedify\Appointments\Appointment;
use Skedify\Appointments\Projectors\AppointmentProjector;
use Skedify\Appointments\Commands\RescheduleAppointment;
use Skedify\Appointments\Commands\ScheduleAppointment;
use Skedify\Appointments\Events\AppointmentWasRescheduled;
use Skedify\Appointments\Events\AppointmentWasScheduled;
use Skedify\Appointments\Listeners\WhenAppointmentWasRescheduled;
use Skedify\Appointments\Listeners\WhenAppointmentWasScheduled;
use Skedify\Appointments\VO\AgentId;
use Skedify\Appointments\VO\AppointmentId;
use Skedify\Appointments\VO\CustomerId;
use Skedify\Appointments\VO\Period;
use Skedify\Appointments\VO\SubjectId;
use Skedify\Storages\JsonDatabase;

require 'setup.php';

// Register DomainEvent Listeners
$eventDispatcher->addListener(AppointmentWasScheduled::class, new WhenAppointmentWasScheduled());
$eventDispatcher->addListener(AppointmentWasReScheduled::class, new WhenAppointmentWasRescheduled());

// Register projectors
$eventDispatcher->addProjector(Appointment::class, new AppointmentProjector(new JsonDatabase($appointments)));

/**
 * Appointment
 */
// Generate UUID
$appointmentId = AppointmentId::generate();
$customerId = CustomerId::generate();
$agentId = AgentId::generate();
$subjectId = SubjectId::generate();

$start = new DateTime();
$end = new DateTime();

// Schedule a new appointment
$period = Period::fromTimestamps($start->getTimestamp() + 20000, $end->getTimestamp() + 30000);
dispatch(ScheduleAppointment::with($appointmentId, $customerId, $agentId, $subjectId, $period));

// Reschedule the same appointment, later in the future
$period = Period::fromTimestamps($start->getTimestamp() + 50000, $end->getTimestamp() + 80000);
dispatch(RescheduleAppointment::with($appointmentId, $period));
