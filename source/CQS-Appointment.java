class Appointment {
  private DateTime start;
  private DateTime end;
  private String subject;
  
  public DateTime getStart() {
    return this.start;
  }

  public DateTime getEnd() {
    return this.end;
  }

  public String getSubject() {
    return this.subject;
  }

  public void setStart(DateTime start) {
    this.start = start;
  }

  public void setEnd(DateTime end) {
    this.end = end;
  }

  public void setSubject(String subject) {
    this.subject = subject;
  }
}