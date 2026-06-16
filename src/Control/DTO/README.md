## DTO (Data Transfer Object)

I **DTO** sono oggetti passivi, privi di logica, utilizzati esclusivamente per trasferire dati in modo sicuro tra lo strato di presentazione (**View**) e la logica dell'applicazione (**Control**).

### Regole
* **Contratto del Control:** Ogni DTO rappresenta il "modulo d'ordine" che un metodo del Controller esige per essere eseguito. Di conseguenza, i DTO sono definiti all'interno della cartella `src/Control/DTO/`.
* **Immutabilità:** In PHP 8+, tutte le proprietà dei DTO sono marcate come `public readonly`. Una volta istanziati dalla View, i dati non possono più essere modificati, garantendo l'integrità delle informazioni durante tutto il flusso.
* **Separazione delle responsabilità:** 
  * La **View** estrae i dati grezzi dalla richiesta (`$_POST`, `JSON`, `CLI`), valida il formato e istanzia il DTO.
  * Il **Controller** riceve il DTO già tipizzato e pulito, ignorando totalmente la sorgente originaria dei dati.

### Esempio di Struttura
Ogni azione del Controller che richiede input dall'esterno possiede il proprio DTO specifico (es. `RegistrazioneDTO`, `LoginDTO`), evitando l'uso di array associativi generici e prevenendo errori o dati mancanti.