Il Router (chiamato anche Front Controller) si trova nel punto più esterno in assoluto della tua applicazione: è la porta d'ingresso principale di tutto il software.

Nella struttura delle cartelle, si trova solitamente nella cartella pubblica del progetto (spesso chiamata `public/` o direttamente nella cartella radice) ed è rappresentato dal file `index.php`.

Tutte le richieste del browser (sia che l'utente vada su `/registrazione`, `/login` o `/profilo`) vengono deviate dai server web (come Apache o Nginx) verso questo singolo file.

Ecco dove si posiziona visivamente nella struttura del tuo progetto:

```bash
mio-progetto/
├── public/                 <-- L'unica cartella visibile dal web
│   ├── .htaccess           <-- Dice al server: "Manda qualsiasi richiesta a index.php"
│   └── index.php           <-- IL ROUTER SI TROVA QUI!
│
└── src/                    <-- Il codice sorgente (protetto, non accessibile direttamente)
    ├── Control/
    │   ├── DTO/
    │   └── CGestioneUtenti.php
    └── View/
        └── Html/

```
