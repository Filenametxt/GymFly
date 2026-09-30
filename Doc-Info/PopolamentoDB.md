# Guida al Popolamento del Database (Fixtures) - GymFly

Questa guida descrive il funzionamento dello script `popola_db_interfacce.php` utilizzato per popolare la base dati di **GymFly** con dati dimostrativi e realistici, ideali per sessioni di test e presentazioni.

---

## 1. Come Eseguire lo Script

### Ambiente Locale (XAMPP / MySQL - Branch `main`)
Importare temporaneamente lo script dal branch `Test_server` ed eseguirlo con PHP:
```bash
git checkout Test_server -- popola_db_interfacce.php
php popola_db_interfacce.php
```

### Ambiente Docker (PostgreSQL / Render - Branch `Test_server`)
A container avviato, eseguire lo script direttamente nel container:
```bash
docker exec -it gymfly_app php popola_db_interfacce.php
```

---

## 2. Entità e Dati Inseriti

Lo script utilizza il pattern di **Inversione delle Dipendenze** e le entità gestite da **Doctrine ORM**:

### 1. Palestra
* **Nome**: GymFly Club
* **Indirizzo**: Via Roma 10, L'Aquila
* **Email**: `info@gymflyclub.it` | **Telefono**: `0862123456`

### 2. Utenti e Credenziali di Accesso

| Ruolo | Nome e Cognome | Email | Password | Note |
| :--- | :--- | :--- | :--- | :--- |
| **Amministratore** | Mario Rossi | `admin@gymfly.com` | `PasswordSicura123!` | Titolare e gestore della palestra |
| **Allenatore** | Luigi Verdi | `luigi.verdi@gymfly.com` | `AllenatorePass88!` | Personal trainer assegnato a Chiara |
| **Allenatore** | Marco Neri | `marco.neri@gymfly.com` | `MarcoCoach99!` | Personal trainer della struttura |
| **Cliente** | Chiara Bianchi | `chiara.bianchi@gymfly.com` | `ClientePass123!` | Utente con scheda, progressi e abbonamento attivo |

### 3. Certificato Medico e Iscrizione
* **Certificato Medico**: Rilasciato dal Dr. Roberto Bianchi a Chiara Bianchi, valido per 1 anno.
* **Iscrizione**: Iscrizione annuale alla struttura GymFly Club.
* **Abbonamento Attivo**: Tipologia *Mensile Open* (durata 30 giorni), attivo e valido.

### 4. Scheda di Allenamento
* **Nome**: *Scheda Forza e Cardio*
* **Obiettivo**: Aumento forza e resistenza cardiovascolare
* **Allenamento A**: Focus parte superiore/inferiore
  - *Panca Piana*: 4 serie da 8 ripetizioni, carico 50 kg
  - *Squat*: 3 serie da 10 ripetizioni, carico 60 kg
* **Allenamento B**: Focus cardiovascolare
  - *Corsa su Tapis Roulant*: 1 serie, durata 20 minuti

### 5. Storico Progressi
Vengono inseriti progressi storici a intervalli regolari (-14gg, -10gg, -5gg, -1gg) per alimentare i grafici e il monitoraggio biometrico:
* Incremento graduale del carico e ripetizioni su Panca Piana e Squat.
* Incremento della durata della sessione cardio sul Tapis Roulant.

### 6. Comunicazioni
* Messaggio di benvenuto inviato dal personal trainer Luigi Verdi alla cliente Chiara Bianchi.
