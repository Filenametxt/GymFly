# Documentazione di Installazione e Configurazione - Web App 'GymFly'

Questa guida illustra la configurazione, l'installazione e l'avvio dell'applicazione web **GymFly**.

Il repository `GymFly` è strutturato principalmente su due branch a seconda dell'ambiente di deploy/esecuzione desiderato:
- **`main`**: Configurazione standard per ambiente locale tradizionale basato su **XAMPP (Apache + MySQL)**.
- **`Test_server`**: Configurazione per **Docker** e ambienti cloud/web service (es. **Render**), con supporto a **PostgreSQL** tramite variabile d'ambiente `DATABASE_URL` e script di avvio automatico.

---

## 1. Installazione Standard tramite XAMPP (Branch `main`)

Questa modalità è ideale per lo sviluppo o l'esecuzione in locale utilizzando lo stack XAMPP con database MySQL.

### Requisiti Preliminari
* **XAMPP** installato con i moduli **Apache** e **MySQL** avviati.
* **PHP >= 8.2** installato e configurato nelle variabili d'ambiente di sistema (`PATH`) con estensioni `pdo_mysql` e `zip` abilitate.
* **Composer** installato a livello di sistema.
* **Git** per la gestione dei branch.

### Procedura di Installazione

1. **Posizionamento dei file e selezione del branch:**
   * Clonare o posizionare la cartella del progetto all'interno della directory `htdocs` di XAMPP:
     - Su Windows: `C:\xampp\htdocs\GymFly`
     - Su Linux: `/opt/lampp/htdocs/GymFly`
   * Aprire il terminale all'interno della cartella del progetto `GymFly` e assicurarsi di essere sul branch `main`:
     ```bash
     git checkout main
     ```

2. **Creazione del Database MySQL:**
   * L'applicazione si connette al database locale con nome `gymfly` (utente `root`, password vuota, porta standard `3306`), come configurato in `src/Foundation/Persistence/Config/EntityManagerFactory.php`.
   * Creare il database tramite terminale MySQL nel seguente modo:
     ```bash
     mysql -h 127.0.0.1 -u root -e "CREATE DATABASE IF NOT EXISTS gymfly CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
     ```
   * *In alternativa*, aprire **phpMyAdmin** (`http://localhost/phpmyadmin`) e creare manualmente un nuovo database vuoto denominato `gymfly`.

3. **Installazione delle dipendenze:**
   * Eseguire da terminale nella root del progetto:
     ```bash
     composer install
     ```

4. **Configurazione permessi di scrittura (Solo per Linux/macOS):**
   * Il motore di template Smarty necessita dei permessi di scrittura per compilare le viste. Aprire il terminale ed eseguire:
     ```bash
     sudo mkdir -p src/View/Templates_c
     sudo chmod -R 777 src/View/Templates_c
     ```

5. **Inizializzazione dello Schema del Database (Doctrine ORM):**
   * Generare la struttura delle tabelle eseguendo:
     ```bash
     php bin/console orm:schema-tool:create
     ```
   * *(Opzionale)* Per verificare il corretto mapping delle entità:
     ```bash
     php bin/console orm:info
     ```

6. **Popolamento del Database con Dati Dimostrativi (Facoltativo):**
   * Se si desidera popolare rapidamente il database con utenti di prova e schede dimostrative, importare lo script dal branch `Test_server` ed eseguirlo:
     ```bash
     git checkout Test_server -- popola_db_interfacce.php
     php popola_db_interfacce.php
     ```
   * Per maggiori dettagli sui dati inseriti, consultare la [Guida al Popolamento del Database](PopolamentoDB.md).

7. **Accesso all'applicazione:**
   * Aprire il browser web e collegarsi al seguente URL:
     ```text
     http://localhost/GymFly/public
     ```
   *(Nota: Il routing dell'applicazione è gestito da `public/.htaccess`, assicurarsi che il modulo `mod_rewrite` di Apache sia abilitato, come di default in XAMPP).*

---

## 2. Installazione e Avvio tramite Docker (Branch `Test_server`)

Questa modalità è concepita per l'esecuzione containerizzata con Docker o per il deploy su piattaforme cloud (es. Render). L'applicazione richiede un database **PostgreSQL** collegato tramite la variabile d'ambiente `DATABASE_URL`.

### Requisiti Preliminari
* **Docker** (o Podman) installato e in esecuzione.
* **Un'istanza di PostgreSQL attiva e raggiungibile** (in locale, tramite container Docker separato, o su cloud database come Render, Neon o Supabase).
* **Git** per selezionare il branch.

### Procedura di Avvio

1. **Selezione del branch:**
   Nel terminale, all'interno della cartella del progetto `GymFly`, passare al branch con la configurazione Docker:
   ```bash
   git checkout Test_server
   ```

2. **Costruzione dell'immagine Docker:**
   ```bash
   docker build -t gymfly .
   ```

3. **Avvio del container con connessione al Database:**
   Avviare il container passando la stringa di connessione della propria istanza PostgreSQL tramite il flag `-e DATABASE_URL`:
   ```bash
   docker run -d -p 8080:80 \
     -e DATABASE_URL="postgres://utente:password@host:5432/nomedatabase" \
     --name gymfly_app gymfly
   ```
   *(Nota: sostituire i valori segnaposto con le credenziali reali del proprio database PostgreSQL nel formato `postgres://utente:password@host:porta/nomedatabase`, oppure incollare direttamente l'URL di connessione fornito dal provider cloud).*

   *Nota sull'avvio:* Lo script `entrypoint.sh` configurato nell'immagine esegue automaticamente `php bin/console orm:schema-tool:create` prima di avviare il server Apache.

4. **Popolamento del Database nel Container Docker (Facoltativo):**
   A container avviato, eseguire lo script di seeding per inserire i dati dimostrativi:
   ```bash
   docker exec -it gymfly_app php popola_db_interfacce.php
   ```
   *Per approfondire i dati generati, consultare la [Guida al Popolamento del Database](PopolamentoDB.md).*

5. **Accesso all'applicazione:**
   Aprire il browser all'indirizzo:
   ```text
   http://localhost:8080
   ```
   *(Nota: Nel container Docker la DocumentRoot di Apache punta direttamente a `/public`, pertanto non è necessario includere `/GymFly/public` nel path dell'URL).**

---

## 3. Credenziali Predefinite di Test

Nel caso in cui sia stato eseguito il popolamento del database (o per consultare gli account dimostrativi previsti in `GymFly/Doc-Info/credenziali.csv`), di seguito sono riportate le credenziali di accesso:

| Ruolo | Nome e Cognome | Email | Password |
| :--- | :--- | :--- | :--- |
| **Amministratore** | Mario Rossi | `admin@gymfly.com` | `PasswordSicura123!` |
| **Allenatore** | Luigi Verdi | `luigi.verdi@gymfly.com` | `AllenatorePass88!` |
| **Allenatore** | Marco Neri | `marco.neri@gymfly.com` | `MarcoCoach99!` |
| **Cliente** | Chiara Bianchi | `chiara.bianchi@gymfly.com` | `ClientePass123!` |
| **Cliente** | Alessia Gialli | `alessia.gialli@gymfly.com` | `AlessiaPass456!` |
| **Cliente** | Davide Viola | `davide.viola@gymfly.com` | `DavidePass789!` |
| **Cliente** | Elena Verde | `elena.verde@gymfly.com` | `ElenaPass999!` |

Per tutti i dettagli sulla struttura delle fixture e sui dati simulati, fare riferimento a [PopolamentoDB.md](PopolamentoDB.md).
