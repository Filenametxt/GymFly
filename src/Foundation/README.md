# Livello: Foundation (Persistenza / Data Access / Repositories)

Questo livello si occupa dell'accesso ai dati e dell'infrastruttura tecnologica. Isola il resto dell'applicazione dai dettagli fisici del database.

## Responsabilità
- Gestire le query complesse al database sfruttando l'**EntityManager** di Doctrine, il **QueryBuilder** o il linguaggio **DQL**.
- Fornire al livello *Control* metodi ad alto livello per cercare i dati (es. `trovaTuttiGliIscritti()`, `ricercaPerCodice()`) senza che il Controller debba conoscere l'SQL o la struttura delle tabelle.
- Gestire eventuali altre operazioni infrastrutturali.

## Interazioni
- Dialoga direttamente con **Doctrine ORM** e i file di configurazione del database (`bootstrap.php`).
- Restituisce oggetti o collezioni di **Entity** pronte all'uso al livello **Control**.
- Non conosce l'esistenza del livello *View*.