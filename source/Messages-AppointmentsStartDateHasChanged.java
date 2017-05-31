class AppointmentsStartDateHasChanged {
  private AppointmentId appointmentId;
  private DateTime start;

  public AppointmentsStartDateHasChanged(
    AppointmentId appointmentId,
    DateTime start
  ) {
    this.appointmentId = appointmentId;
    this.start = start;
  }

  public AppointmentId getAppointmentId() {
    return this.appointmentId;
  }

  public DateTime getStart() {
    return this.start;
  }
}