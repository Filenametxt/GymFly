# Documentazione di Installazione e Configurazione - Web App 'GymFly'

Questa guida illustra la configurazione, l'installazione e l'avvio dell'applicazione web **GymFly**.

Il repository `GymFly` è strutturato su due branch separati in base all'ambiente desiderato:
- **`main` (Ambiente Primario Raccomandato)**: Configurazione standard pensata per l'esecuzione in locale tramite lo stack tradizionale **XAMPP (Apache + MySQL)**.
- **`Test_server` (Deploy Cloud & Container Opzionale)**: Configurazione per ambienti cloud e container **Docker**, utilizzata per il deploy su **Render** con database PostgreSQL ospitato su **Aiven Cloud**.

> 🌐 **Istanza Live Dimostrativa (Senza Installazione)**
> Se si desidera valutare o provare immediatamente l'applicazione senza eseguire alcuna installazione locale, la web app è già attiva e funzionante su cloud:
> 👉 **[https://gymfly.onrender.com/](https://gymfly.onrender.com/)**

---

## 1. Installazione Standard tramite XAMPP (Branch `main`)

Questa è la modalità di riferimento e raccomandata per l'esecuzione del progetto sul computer locale o di valutazione.

### Requisiti Preliminari
* **XAMPP** installato con i moduli **Apache** e **MySQL** avviati.
* **PHP >= 8.2** configurato nel `PATH` di sistema con le estensioni `pdo_mysql` e `zip` abilitate (standard in XAMPP).
* **Composer** installato a livello di sistema.
* **Git** installato.

### Procedura di Installazione

1. **Posizionamento e Clonazione del Repository:**
   Posizionarsi all'interno della directory `htdocs` di XAMPP e clonare il repository:
   * Su Linux:
     ```bash
     cd /opt/lampp/htdocs
     git clone https://github.com/Filenametxt/GymFly.git
     cd GymFly
     ```
   * Su Windows:
     ```bash
     cd C:\xampp\htdocs
     git clone https://github.com/Filenametxt/GymFly.git
     cd GymFly
     ```
   Assicurarsi di essere sul branch principale:
   ```bash
   git checkout main
   ```

2. **Creazione del Database MySQL:**
   L'applicazione si connette al database locale denominato `gymfly` (utente `root`, password vuota, porta standard `3306`), configurato in `src/Foundation/Persistence/Config/EntityManagerFactory.php`.
   * Tramite terminale:
     ```bash
     mysql -h 127.0.0.1 -u root -e "CREATE DATABASE IF NOT EXISTS gymfly CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
     ```
   * *In alternativa*, aprire **phpMyAdmin** (`http://localhost/phpmyadmin`) e creare un nuovo database vuoto chiamato `gymfly`.

3. **Installazione delle Dipendenze:**
   Eseguire nella root del progetto:
   ```bash
   composer install
   ```

4. **Configurazione Permessi di Scrittura (Solo per Linux/macOS):**
   Il template engine Smarty necessita dei permessi di scrittura nella cartella di cache per compilare le viste:
   ```bash
   mkdir -p src/View/Templates_c
   chmod -R 777 src/View/Templates_c
   ```

5. **Inizializzazione dello Schema del Database (Doctrine ORM):**
   Generare le tabelle tramite la CLI di Doctrine:
   ```bash
   php bin/console orm:schema-tool:create
   ```
   *(Opzionale)* Per verificare che tutte le 28 entità siano mappate correttamente:
   ```bash
   php bin/console orm:info
   ```

6. **Popolamento del Database con Dati Dimostrativi (Fixtures):**
   Per popolare rapidamente il database con utenti di prova, palestra, schede ed esercizi, importare lo script dal branch `Test_server` ed eseguirlo:
   ```bash
   git checkout origin/Test_server -- popola_db_interfacce.php
   php popola_db_interfacce.php
   ```
   Per maggiori dettagli sui dati generati, consultare la [Guida al Popolamento del Database](PopolamentoDB.md).

7. **Accesso all'Applicazione:**
   Aprire il browser e collegarsi all'indirizzo:
   ```text
   http://localhost/GymFly/public
   ```
   *(Nota: Il routing è gestito da `public/.htaccess`, verificare che il modulo `mod_rewrite` di Apache sia abilitato, come da default in XAMPP).*

---

## 2. Deploy Cloud tramite Render & Aiven Cloud (Branch `Test_server`)

Questa sezione illustra la configurazione per la messa online del progetto. Per evitare di dover installare e configurare manualmente PostgreSQL in locale, l'infrastruttura di test è stata realizzata con servizi cloud gestiti:

* **Render** ([render.com](https://render.com)): Hosting del Web Service containerizzato via Docker.
* **Aiven Cloud** ([aiven.io](https://aiven.io)): Database PostgreSQL gestito in cloud con connessione sicura SSL.

### Come è Strutturato il Deploy (Panoramica della Configurazione)

1. **Database PostgreSQL su Aiven:**
   - È stato creato un servizio PostgreSQL gestito gratuito su Aiven.
   - Aiven fornisce la stringa di connessione (*Service URI*) nel formato:
     `postgres://utente:password@host:porta/nomedatabase?sslmode=require`

2. **Web Service su Render:**
   - Su Render è stato creato un nuovo **Web Service** collegato al repository GitHub `GymFly`.
   - **Branch selezionato**: `Test_server` (che contiene il [`Dockerfile`](../Dockerfile) e lo script [`entrypoint.sh`](../entrypoint.sh)).
   - **Ambiente di Runtime**: *Docker*.
   - **Variabile d'Ambiente**: Nelle impostazioni del servizio è stata definita la variabile:
     - `DATABASE_URL` = `<Service URI di Aiven>`

3. **Build e Avvio Automatico del Container:**
   - Render esegue la build dell'immagine Docker partendo da `php:8.2-apache` e installando le librerie necessarie (`pdo_pgsql`, `zip`, ecc.).
   - All'avvio, lo script `entrypoint.sh` rileva la presenza di `DATABASE_URL`, verifica la connessione a PostgreSQL ed esegue automaticamente `php bin/console orm:schema-tool:create` prima di lanciare Apache.
   - `EntityManagerFactory.php` rileva automaticamente la variabile `DATABASE_URL`: se presente, adotta il driver `pdo_pgsql` per Render; se assente, ricade sul driver locale `pdo_mysql` per XAMPP.

4. **Popolamento delle Fixtures su Cloud:**
   - Dal terminale (*Shell*) di Render è sufficiente digitare:
     ```bash
     php popola_db_interfacce.php
     ```

### (Opzionale) Esecuzione Docker in Locale con Database Remoto
Se si desidera avviare il container Docker in locale collegandosi al database cloud (o a una propria istanza PostgreSQL):
```bash
git checkout Test_server
docker build -t gymfly .
docker run -d -p 8080:80 \
  -e DATABASE_URL="postgres://utente:password@host:porta/nomedatabase?sslmode=require" \
  --name gymfly_app gymfly
```
L'applicazione sarà accessibile all'indirizzo:
```text
http://localhost:8080
```

---

## 3. Credenziali Predefinite di Test

Dopo aver eseguito lo script di popolamento (o per consultare gli account di prova già presenti sull'istanza online), utilizzare le seguenti credenziali per l'accesso:

| Ruolo | Nome e Cognome | Email | Password | Note |
| :--- | :--- | :--- | :--- | :--- |
| **Amministratore** | Mario Rossi | `admin@gymfly.com` | `PasswordSicura123!` | Gestore della palestra, controllo abbonamenti e report |
| **Allenatore** | Luigi Verdi | `luigi.verdi@gymfly.com` | `AllenatorePass88!` | Gestione schede tecniche ed esercizi |
| **Cliente** | Chiara Bianchi | `chiara.bianchi@gymfly.com` | `ClientePass123!` | Consultazione scheda e monitoraggio progressi |

Per ulteriori dettagli sulla struttura dei dati e sulle entità simulate, fare riferimento alla [Guida al Popolamento del Database](PopolamentoDB.md).
