class Id {
  private GUID id;

  public Id(GUID id) {
    this.id = id;
  }

  public GUID getId() {
    return this.id;
  }

  public void setId(GUID id) {
    this.id = id;
  }
}

class ClientId extends Id {}
class AgentId extends Id {}

class AddAppointment {
  private AgentId agent;
  private ClientId client;
  private DateTime date;

  public AddAppointment(AgentId agent, ClientId client, DateTime date) {
    this.agent = agent;
    this.client = client;
    this.date = date;
  }
}