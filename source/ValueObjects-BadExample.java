// 1. slecht
class AddAppointment {
  private String person1;
  private String person2;
  private DateTime date;

  // Wanneer er een slechte naamkeuze wordt gemaakt in de naam
  // van de variabele, kan dit tot bugs lijden.
  public AddAppointment(String person1, String person2, DateTime date) {
    this.person1 = person1;
    this.person2 = person2;
    this.date = date;
  }
}

// 2. beter
class AddAppointment {
  private String agent;
  private String client;
  private DateTime date;

  // Dit is al beter leesbaar, het gevaar bestaat er in agen & client
  // Van plaats te verwisselen. Bij 2 geldige strings zal dit dus geen
  // Exceptie gooien
  public AddAppointment(String agent, String client, DateTime date) {
    this.agent = agent;
    this.client = client;
    this.date = date;
  }
}