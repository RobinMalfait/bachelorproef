class MyAppointmentsForToday {
  private UserId userId;
  private Date today;

  public MyAppointmentsForToday(UserId userId) {
    this.userId = userId;
    this.today = new Date();
  }
}