# Guida al Popolamento del Database (Fixtures) - GymFly

Questa guida descrive il funzionamento dello script `popola_db_interfacce.php` utilizzato per popolare la base dati di **GymFly** con i dati dimostrativi di test per le presentazioni e il collaudo del sistema.

---

## 1. Come Eseguire lo Script

### Ambiente Locale (XAMPP / MySQL - Branch `main`)
Importare lo script dal branch `Test_server` ed eseguirlo da terminale:
```bash
git checkout origin/Test_server -- popola_db_interfacce.php
php popola_db_interfacce.php
```

### Ambiente Docker (PostgreSQL / Render - Branch `Test_server`)
A container avviato, eseguire lo script direttamente nel container:
```bash
docker exec -it gymfly_app php popola_db_interfacce.php
```
*(Oppure dalla Shell di Render sul Web Service).*

---

## 2. Entità e Dati Inseriti dallo Script

Lo script utilizza il layer di astrazione ad oggetti **Doctrine ORM** e popola il database con i seguenti elementi:

### 1. Palestra
* **Nome**: GymFly Central
* **Indirizzo**: Via delle Palestre 10, Milano
* **Email**: `info@gymflycentral.com` | **Telefono**: `0212345678`
* **Titolare / Amministratore**: Mario Rossi

### 2. Utenti e Credenziali di Accesso

| Ruolo | Nome e Cognome | Email | Password | Dettagli Profilo |
| :--- | :--- | :--- | :--- | :--- |
| **Amministratore** | Mario Rossi | `admin@gymfly.com` | `PasswordSicura123!` | CF: `RSSMRA80A01H501U`, Via Roma 1, Milano |
| **Allenatore** | Luigi Verdi | `luigi.verdi@gymfly.com` | `AllenatorePass88!` | CF: `VRDLGU85B02H501Z`, Tel: `372-148-2574`, Via Milano 2, Torino |
| **Cliente** | Chiara Bianchi | `chiara.bianchi@gymfly.com` | `ClientePass123!` | CF: `BNCCHR90A41H501Y`, Nata a Roma il 15/05/1990, Pagamento: Carta di Credito |

### 3. Attività e Corsi
* **Attività**: *Pilates* (Corso di pilates per tonificazione e flessibilità)
* **Allenatore abilitato**: Luigi Verdi

### 4. Certificato Medico, Abbonamento e Iscrizione
* **Certificato Medico**: Rilasciato dal Dr. Roberto Bianchi a Chiara Bianchi (valido per 1 anno).
* **Iscrizione**: Iscrizione annuale attiva per Chiara Bianchi alla palestra GymFly Central.
* **Abbonamento Attivo**: Tipologia *Mensile Open* (categoria *Fitness*, durata 30 giorni), attivo per Chiara Bianchi.

### 5. Scheda di Allenamento
* **Nome Scheda**: *Scheda Forza e Cardio*
* **Obiettivo**: Aumento forza e resistenza cardiovascolare
* **Cliente**: Chiara Bianchi | **Allenatore**: Luigi Verdi
* **Allenamento A**: Focus parte superiore/inferiore
  - *Panca Piana*: 4 serie da 8 ripetizioni, carico 50 kg
  - *Squat*: 3 serie da 10 ripetizioni, carico 60 kg
* **Allenamento B**: Focus cardiovascolare
  - *Corsa su Tapis Roulant*: 1 serie, durata 20 minuti (`20m`)

### 6. Storico dei Progressi Biometrici
Vengono inserite misurazioni e carichi storici a intervalli regolari (-14gg, -10gg, -5gg, -1gg) per alimentare i grafici biometrici di Chiara Bianchi:
* Progressione di carico e ripetizioni su Panca Piana e Squat.
* Progressione della durata su Tapis Roulant (da 15 a 25 minuti).

### 7. Comunicazioni
* Messaggio di benvenuto inviato dal personal trainer Luigi Verdi alla cliente Chiara Bianchi (*"Benvenuta nel team!"*).
