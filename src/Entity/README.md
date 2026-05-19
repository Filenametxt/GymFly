# Livello: Entity (Dominio / Modello)

Questo livello rappresenta il cuore dei dati dell'applicazione. Contiene le classi di dominio pure (Entity) che mappano direttamente le tabelle del database relazionale tramite **Doctrine ORM**.

## Responsabilità
- Definire la struttura dei dati, i tipi di colonna e i vincoli del database utilizzando gli **Attributi PHP** (`#[ORM\Entity]`, `#[ORM\Column]`, ecc.).
- Definire le relazioni tra i dati (es. `#[ORM\ManyToOne]`, `#[ORM\OneToMany]`).
- Contenere esclusivamente lo stato dell'oggetto (proprietà) e i metodi di accesso sicuri (`getter` e `setter`), senza alcuna logica di business complessa o query al database.

## Interazioni
- Viene manipolato dal livello **Control** per leggere o modificare i dati.
- Viene mappato sul database dall'**EntityManager** di Doctrine.
- Non deve mai dipendere da file della View o della Foundation.