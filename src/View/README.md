# Livello: View (Presentazione / Interfaccia Utente)

Questo livello gestisce tutto ciò che viene mostrato graficamente all'utente finale (HTML, CSS, JavaScript, template).

## Responsabilità
- Presentare i dati all'utente in modo chiaro e strutturato.
- Contenere i form e i componenti di interfaccia per raccogliere l'input dell'utente.
- Mantenere la logica di presentazione minimale (es. cicli `foreach` per stampare tabelle o controlli `if` per mostrare un messaggio di errore), senza elaborare logica di business o fare query.

## Interazioni
- Riceve passivamente i dati o le variabili dal livello **Control**.
- Invia le azioni dell'utente (click, invio di form) al livello **Control** per l'elaborazione.
- Non ha alcun contatto diretto con il livello **Entity** (se non per leggerne le proprietà in sola stampa) o con il livello **Foundation**.