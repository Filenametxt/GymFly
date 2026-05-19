# Livello: Control (Controller / Logica di Business)

Questo livello fa da ponte (mediatore) tra le richieste dell'utente (interfaccia o richieste HTTP) e la logica interna dell'applicazione.

## Responsabilità
- Ricevere l'input dell'utente dal livello **View** o dai punti di ingresso dell'applicazione.
- Coordinare le operazioni: richiede i dati al livello persistenza (**Foundation**), applica le regole di business necessarie e aggiorna le **Entity**.
- Determinare quale View mostrare o quale risposta restituire all'utente in base al risultato delle operazioni.

## Interazioni
- Chiede al livello **Foundation** di recuperare o salvare le entità.
- Modifica lo stato delle **Entity**.
- Passa i dati elaborati al livello **View** per la renderizzazione visiva.