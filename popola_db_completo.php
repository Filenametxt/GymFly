<?php

/**
 * Script di popolamento database completo e coerente (Fixtures)
 * Esegue le operazioni attraverso le classi Controller di src/Control/
 * simulando un'interazione reale tra gli utenti della piattaforma.
 * 
 * Da eseguire CLI: php popola_db_completo.php
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

use App\Foundation\Persistence\Config\EntityManagerFactory;
use App\Foundation\Session;
use App\Foundation\Persistence\Repository\DoctrineUtenteRepository;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineAllenatoreRepository;
use App\Foundation\Persistence\Repository\DoctrineAttivitaRepository;
use App\Foundation\Persistence\Repository\DoctrineAbbonamentoRepository;
use App\Foundation\Persistence\Repository\DoctrineEsercizioRepository;
use App\Foundation\Persistence\Repository\DoctrineSchedaRepository;
use App\Foundation\Persistence\Repository\DoctrineAttivitaPianificataRepository;
use App\Foundation\Persistence\Type\DateTimeImmutableStringable;

use App\Control\AutenticazioneController;
use App\Control\AmministratoreController;
use App\Control\EserciziController;
use App\Control\AttivitaPianificataController;
use App\Control\SchedaAllenamentoController;
use App\Control\MessaggiController;
use App\Control\ProfiloController;

use App\Entity\GruppoMuscolare;
use App\Entity\Attrezzatura;
use App\Entity\Tipologia;
use App\Entity\CertificatoMedico;
use App\Entity\AbbonamentoDurata;
use App\Entity\AbbonamentoAttivo;
use App\Entity\Iscrizione;
use App\Entity\Parametri;
use App\Entity\ProgressoCarico;
use App\Entity\ProgressoRipetizioni;
use App\Entity\ProgressoDurata;

echo "[DEBUG] 1. Caricamento autoload...\n";
require_once __DIR__ . '/vendor/autoload.php';

echo "[DEBUG] 2. Inizializzazione EntityManager e Sessione...\n";
$entityManager = EntityManagerFactory::create();
$session = new Session();

if (!$entityManager instanceof \Doctrine\ORM\EntityManagerInterface) {
    die("Errore: Impossibile creare l'EntityManager.\n");
}

/**
 * Esegue un'azione del controller catturando ed ignorando l'output HTML generato da Smarty Views.
 */
function eseguiAzioneController(callable $fn): void {
    ob_start();
    try {
        $fn();
    } catch (\Throwable $e) {
        $output = ob_get_clean();
        echo "[CONTROLLER ERROR] " . $e->getMessage() . "\n";
        throw $e;
    }
    $output = ob_get_clean();
    if (str_contains($output, "Campi obbligatori mancanti") || str_contains($output, "Errore") || str_contains($output, "Accesso negato")) {
        echo "[CONTROLLER OUTPUT WARNING] " . strip_tags($output) . "\n";
    }
}

echo "Inizio popolamento database mediante l'esecuzione delle classi Controller in src/Control/...\n\n";

try {
    // -------------------------------------------------------------------------
    // 1. Pulizia preventiva database
    // -------------------------------------------------------------------------
    echo "[DEBUG] Pulizia preventiva tabelle database...\n";
    $conn = $entityManager->getConnection();
    $conn->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
    
    $tables = [
        'CodaAttesa', 'Progresso', 'DettaglioAllenamento', 'Allenamento', 'Scheda', 
        'Iscrizione', 'AbbonamentoAttivo', 'Abbonamento', 'Messaggio', 'CertificatoMedico', 
        'AttivitaPianificata', 'SessionePrivata', 'Attivita', 'Utente', 'Palestra', 'Tipologia', 'Esercizio', 
        'GruppoMuscolare', 'Attrezzatura', 'Esercizio_GruppoMuscolare',
        'Iscritto', 'Abilitazione', 'Allena', 'Riceve', 'Sala'
    ];
    foreach ($tables as $t) {
        try {
            $conn->executeStatement("DELETE FROM `$t`");
        } catch (\Throwable $e) {}
    }
    
    $conn->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    echo "[OK] Database pulito con successo.\n\n";

    // -------------------------------------------------------------------------
    // 2. Registrazione Atomica Amministratore e Palestra via AutenticazioneController
    // -------------------------------------------------------------------------
    echo "[DEBUG] Invocazione AutenticazioneController per registrazione Amministratore e Palestra...\n";
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = [
        'nome' => 'Mario',
        'cognome' => 'Rossi',
        'email' => 'admin@gymfly.com',
        'cf' => 'RSSMRA80A01H501U',
        'indirizzo' => 'Via Roma 1, Milano',
        'sesso' => 'M',
        'password' => 'PasswordSicura123!',
        'conferma_password' => 'PasswordSicura123!',
        'telefono' => '0212345678',
        'nome_palestra' => 'GymFly Central',
        'indirizzo_palestra' => 'Via delle Palestre 10, Milano',
        'email_palestra' => 'info@gymflycentral.com',
        'telefono_palestra' => '0212345678'
    ];

    $authCtrl = new AutenticazioneController($entityManager, $session);
    eseguiAzioneController(fn() => $authCtrl->registraAmministratore());

    $utenteRepo = new DoctrineUtenteRepository($entityManager);
    $admin = $utenteRepo->findByEmail('admin@gymfly.com');
    if (!$admin) {
        throw new \RuntimeException("Amministratore non trovato dopo l'esecuzione del controller.");
    }
    $session->setUtenteLoggato($admin);
    echo "[OK] Amministratore (Mario Rossi) e Palestra registrati via AutenticazioneController.\n\n";

    // -------------------------------------------------------------------------
    // 3. Creazione Sale della Palestra
    // -------------------------------------------------------------------------
    echo "[DEBUG] Creazione Sale della Palestra...\n";
    $palestraRepo = new \App\Foundation\Persistence\Repository\DoctrinePalestraRepository($entityManager);
    $palestra = $palestraRepo->findByAmministratore($admin);

    $salaA = new \App\Entity\Sala('Sala Corsi Grande A', 20, $palestra);
    $salaB = new \App\Entity\Sala('Sala Olistica B', 1, $palestra);
    $salaPT = new \App\Entity\Sala('Sala Personal Training C', 2, $palestra);

    $entityManager->persist($salaA);
    $entityManager->persist($salaB);
    $entityManager->persist($salaPT);
    $entityManager->flush();
    echo "[OK] Sale create (Sala A, Sala B [cap. 1], Sala PT C).\n\n";

    // -------------------------------------------------------------------------
    // 4. Creazione Gruppi Muscolari ed Attrezzature
    // -------------------------------------------------------------------------
    echo "[DEBUG] Creazione Gruppi Muscolari ed Attrezzature...\n";
    $gmPetto = new GruppoMuscolare('Petto');
    $gmDorso = new GruppoMuscolare('Dorso');
    $gmGambe = new GruppoMuscolare('Gambe');
    $gmSpalle = new GruppoMuscolare('Spalle');
    $gmBicipiti = new GruppoMuscolare('Bicipiti');
    $gmTricipiti = new GruppoMuscolare('Tricipiti');
    $gmAddominali = new GruppoMuscolare('Addominali');

    $entityManager->persist($gmPetto);
    $entityManager->persist($gmDorso);
    $entityManager->persist($gmGambe);
    $entityManager->persist($gmSpalle);
    $entityManager->persist($gmBicipiti);
    $entityManager->persist($gmTricipiti);
    $entityManager->persist($gmAddominali);

    $attrPanca = new Attrezzatura('Panca Piana');
    $attrTapis = new Attrezzatura('Tapis Roulant');
    $attrRack = new Attrezzatura('Rack Squat');
    $attrLat = new Attrezzatura('Lat Machine');
    $attrManubri = new Attrezzatura('Manubri');

    $entityManager->persist($attrPanca);
    $entityManager->persist($attrTapis);
    $entityManager->persist($attrRack);
    $entityManager->persist($attrLat);
    $entityManager->persist($attrManubri);
    $entityManager->flush();
    echo "[OK] Gruppi muscolari e attrezzature salvati.\n\n";

    // -------------------------------------------------------------------------
    // 5. Creazione Allenatori, Attività e Clienti via AmministratoreController
    // -------------------------------------------------------------------------
    echo "[DEBUG] Invocazione AmministratoreController per creazione Allenatori, Attività e Clienti...\n";
    $adminCtrl = new AmministratoreController($entityManager, $session);

    // Allenatore 1: Coach Luigi
    $_POST = [
        'nome' => 'Luigi', 'cognome' => 'Verdi', 'email' => 'luigi.verdi@gymfly.com',
        'cf' => 'VRDLGU85B02H501Z', 'indirizzo' => 'Via Milano 2, Torino',
        'sesso' => 'M', 'telefono' => '372-148-2574'
    ];
    eseguiAzioneController(fn() => $adminCtrl->creaAllenatore());

    // Allenatore 2: Coach Marco
    $_POST = [
        'nome' => 'Marco', 'cognome' => 'Neri', 'email' => 'marco.neri@gymfly.com',
        'cf' => 'NRAMRC88C03H501K', 'indirizzo' => 'Corso Sempione 44, Milano',
        'sesso' => 'M', 'telefono' => '347-987-6543'
    ];
    eseguiAzioneController(fn() => $adminCtrl->creaAllenatore());

    // Attività Corsi
    $_POST = ['nome' => 'Zumba Fitness', 'descrizione' => 'Corso cardio a ritmo di musica latina', 'max_partecipanti' => '15'];
    eseguiAzioneController(fn() => $adminCtrl->creaAttivita());

    $_POST = ['nome' => 'Pilates', 'descrizione' => 'Sessioni di tonificazione e stretching posturale', 'max_partecipanti' => '1'];
    eseguiAzioneController(fn() => $adminCtrl->creaAttivita());

    $_POST = ['nome' => 'Spinning', 'descrizione' => 'Allenamento ad alta intensità su bici stazionaria', 'max_partecipanti' => '12'];
    eseguiAzioneController(fn() => $adminCtrl->creaAttivita());

    $_POST = ['nome' => 'Cross Training', 'descrizione' => 'Allenamento funzionale ad alta intensità', 'max_partecipanti' => '10'];
    eseguiAzioneController(fn() => $adminCtrl->creaAttivita());

    // Clienti
    $_POST = [
        'nome' => 'Chiara', 'cognome' => 'Bianchi', 'email' => 'chiara.bianchi@gymfly.com',
        'cf' => 'BNCCHR90A41H501Y', 'indirizzo' => 'Via Garibaldi 10, Milano', 'sesso' => 'F',
        'data_nascita' => '1990-05-15', 'luogo_nascita' => 'Roma', 'metodo_pagamento' => 'Carta di Credito',
        'indirizzo_domicilio' => 'Via Garibaldi 10, Milano',
        'telefono' => '3331112233'
    ];
    eseguiAzioneController(fn() => $adminCtrl->creaCliente());

    $_POST = [
        'nome' => 'Alessia', 'cognome' => 'Gialli', 'email' => 'alessia.gialli@gymfly.com',
        'cf' => 'GLLLSZ95C55H501W', 'indirizzo' => 'Via Dante 5, Milano', 'sesso' => 'F',
        'data_nascita' => '1995-09-22', 'luogo_nascita' => 'Firenze', 'metodo_pagamento' => 'Contanti',
        'indirizzo_domicilio' => 'Via Dante 5, Milano',
        'telefono' => '3334445566'
    ];
    eseguiAzioneController(fn() => $adminCtrl->creaCliente());

    $_POST = [
        'nome' => 'Davide', 'cognome' => 'Viola', 'email' => 'davide.viola@gymfly.com',
        'cf' => 'VLADVD92E12H501Q', 'indirizzo' => 'Viale Monza 12, Milano', 'sesso' => 'M',
        'data_nascita' => '1992-12-12', 'luogo_nascita' => 'Torino', 'metodo_pagamento' => 'Bonifico Bancario',
        'indirizzo_domicilio' => 'Viale Monza 12, Milano',
        'telefono' => '3337778899'
    ];
    eseguiAzioneController(fn() => $adminCtrl->creaCliente());

    $_POST = [
        'nome' => 'Elena', 'cognome' => 'Verde', 'email' => 'elena.verde@gymfly.com',
        'cf' => 'VRDELN98M42H501X', 'indirizzo' => 'Corso Italia 88, Milano', 'sesso' => 'F',
        'data_nascita' => '1998-08-08', 'luogo_nascita' => 'Milano', 'metodo_pagamento' => 'Carta di Credito',
        'indirizzo_domicilio' => 'Corso Italia 88, Milano',
        'telefono' => '3330001122'
    ];
    eseguiAzioneController(fn() => $adminCtrl->creaCliente());

    echo "[OK] Allenatori, Attività e Clienti creati con AmministratoreController.\n\n";

    // Recupero riferimenti entità caricate
    $allenatoreRepo = new DoctrineAllenatoreRepository($entityManager);
    $clienteRepo = new DoctrineClienteRepository($entityManager);
    $attivitaRepo = new DoctrineAttivitaRepository($entityManager);

    $coachLuigi = $allenatoreRepo->findByEmail('luigi.verdi@gymfly.com');
    $coachMarco = $allenatoreRepo->findByEmail('marco.neri@gymfly.com');

    $chiara = $clienteRepo->findByEmail('chiara.bianchi@gymfly.com');
    $alessia = $clienteRepo->findByEmail('alessia.gialli@gymfly.com');
    $davide = $clienteRepo->findByEmail('davide.viola@gymfly.com');
    $elena = $clienteRepo->findByEmail('elena.verde@gymfly.com');

    // Sincronizzazione password coerenti con Doc-Info/credenziali.csv
    $coachLuigi->setPassword('AllenatorePass88!');
    $coachMarco->setPassword('MarcoCoach99!');
    $chiara->setPassword('ClientePass123!');
    $alessia->setPassword('AlessiaPass456!');
    $davide->setPassword('DavidePass789!');
    $elena->setPassword('ElenaPass999!');

    $allenatoreRepo->save($coachLuigi);
    $allenatoreRepo->save($coachMarco);
    $clienteRepo->save($chiara);
    $clienteRepo->save($alessia);
    $clienteRepo->save($davide);
    $clienteRepo->save($elena);

    $zumba = $attivitaRepo->findByNome('Zumba Fitness')[0] ?? null;
    $pilates = $attivitaRepo->findByNome('Pilates')[0] ?? null;
    $spinning = $attivitaRepo->findByNome('Spinning')[0] ?? null;
    $crossFit = $attivitaRepo->findByNome('Cross Training')[0] ?? null;

    // -------------------------------------------------------------------------
    // 6. Abilitazioni Allenatori per Attività via ProfiloController
    // -------------------------------------------------------------------------
    echo "[DEBUG] Invocazione ProfiloController per abilitazioni Allenatori...\n";
    $profiloCtrl = new ProfiloController($entityManager, $session);

    // Abilitazioni Coach Luigi
    $session->setUtenteLoggato($coachLuigi);
    $_POST = ['attivita' => [$zumba->getId(), $spinning->getId()]];
    eseguiAzioneController(fn() => $profiloCtrl->aggiornaAbilitazioniAllenatore());

    // Abilitazioni Coach Marco
    $session->setUtenteLoggato($coachMarco);
    $_POST = ['attivita' => [$pilates->getId(), $crossFit->getId()]];
    eseguiAzioneController(fn() => $profiloCtrl->aggiornaAbilitazioniAllenatore());
    echo "[OK] Abilitazioni corsi registrate per gli allenatori.\n\n";

    // -------------------------------------------------------------------------
    // 7. Certificati Medici, Abbonamenti ed Iscrizioni
    // -------------------------------------------------------------------------
    echo "[DEBUG] Configurazione Certificati Medici ed Abbonamenti...\n";
    $certChiara = new CertificatoMedico(new \DateTimeImmutable('-20 days'), 'Dr. Gregory House', $chiara, null);
    $certAlessia = new CertificatoMedico(new \DateTimeImmutable('-10 days'), 'Dr. Gregory House', $alessia, null);
    $certDavide = new CertificatoMedico(new \DateTimeImmutable('-380 days'), 'Dr. John Watson', $davide, null);
    $certElena = new CertificatoMedico(new \DateTimeImmutable('-5 days'), 'Dr. Gregory House', $elena, null);

    $entityManager->persist($certChiara);
    $entityManager->persist($certAlessia);
    $entityManager->persist($certDavide);
    $entityManager->persist($certElena);

    $abbonamentoMensile = new AbbonamentoDurata('Mensile Open', 'Fitness e Corsi', 30);
    $abbonamentoTrimestrale = new AbbonamentoDurata('Trimestrale Open', 'Corsi e Sala Attrezzi', 90);
    $abbonamentoAnnuale = new AbbonamentoDurata('Annuale VIP', 'Accesso Illimitato + PT', 365);

    $entityManager->persist($abbonamentoMensile);
    $entityManager->persist($abbonamentoTrimestrale);
    $entityManager->persist($abbonamentoAnnuale);
    $entityManager->flush();

    // Chiara (Attiva)
    $abbChiara = new AbbonamentoAttivo(new \DateTimeImmutable('-5 days'), $abbonamentoMensile);
    $entityManager->persist($abbChiara);
    $chiara->setAbbonamento($abbChiara);
    $iscrChiara = new Iscrizione(new \DateTimeImmutable('-5 days'), $chiara);
    $entityManager->persist($iscrChiara);

    // Alessia (Attiva)
    $abbAlessia = new AbbonamentoAttivo(new \DateTimeImmutable('-10 days'), $abbonamentoTrimestrale);
    $entityManager->persist($abbAlessia);
    $alessia->setAbbonamento($abbAlessia);
    $iscrAlessia = new Iscrizione(new \DateTimeImmutable('-10 days'), $alessia);
    $entityManager->persist($iscrAlessia);

    // Davide (Abbonamento Scaduto, Iscrizione valida)
    $abbDavide = new AbbonamentoAttivo(new \DateTimeImmutable('-40 days'), $abbonamentoMensile);
    $entityManager->persist($abbDavide);
    $davide->setAbbonamento($abbDavide);
    $iscrDavide = new Iscrizione(new \DateTimeImmutable('-15 days'), $davide);
    $entityManager->persist($iscrDavide);

    // Elena (Attiva VIP)
    $abbElena = new AbbonamentoAttivo(new \DateTimeImmutable('-2 days'), $abbonamentoAnnuale);
    $entityManager->persist($abbElena);
    $elena->setAbbonamento($abbElena);
    $iscrElena = new Iscrizione(new \DateTimeImmutable('-2 days'), $elena);
    $entityManager->persist($iscrElena);

    $entityManager->flush();
    echo "[OK] Certificati ed Abbonamenti salvati.\n\n";

    // -------------------------------------------------------------------------
    // 8. Creazione Esercizi via EserciziController
    // -------------------------------------------------------------------------
    echo "[DEBUG] Invocazione EserciziController per creazione Esercizi...\n";
    $eserciziCtrl = new EserciziController($entityManager, $session);
    $session->setUtenteLoggato($coachLuigi);

    // Esercizio 1: Panca Piana
    $_POST = ['nome' => 'Panca Piana', 'descrizione' => 'Spinte su panca piana con bilanciere', 'nuova_attrezzatura_nome' => 'Panca Piana', 'tracciamento_carico' => '1', 'gruppi_muscolari' => ['Petto', 'Tricipiti']];
    eseguiAzioneController(fn() => $eserciziCtrl->salvaEsercizio());

    // Esercizio 2: Squat
    $_POST = ['nome' => 'Squat', 'descrizione' => 'Squat posteriore con bilanciere su rack', 'nuova_attrezzatura_nome' => 'Rack Squat', 'tracciamento_carico' => '1', 'gruppi_muscolari' => ['Gambe']];
    eseguiAzioneController(fn() => $eserciziCtrl->salvaEsercizio());

    // Esercizio 3: Corsa Tapis Roulant
    $_POST = ['nome' => 'Corsa Tapis Roulant', 'descrizione' => 'Corsa cardio continuativa', 'nuova_attrezzatura_nome' => 'Tapis Roulant', 'tracciamento_carico' => '2', 'gruppi_muscolari' => ['Gambe']];
    eseguiAzioneController(fn() => $eserciziCtrl->salvaEsercizio());

    $session->setUtenteLoggato($coachMarco);

    // Esercizio 4: Lat Machine
    $_POST = ['nome' => 'Lat Machine Avanti', 'descrizione' => 'Trazioni alla lat machine per gran dorsale', 'nuova_attrezzatura_nome' => 'Lat Machine', 'tracciamento_carico' => '3', 'gruppi_muscolari' => ['Dorso', 'Bicipiti']];
    eseguiAzioneController(fn() => $eserciziCtrl->salvaEsercizio());

    // Esercizio 5: Lento Avanti
    $_POST = ['nome' => 'Lento Avanti Manubri', 'descrizione' => 'Distensioni sopra la testa con manubri per deltoidi', 'nuova_attrezzatura_nome' => 'Manubri', 'tracciamento_carico' => '1', 'gruppi_muscolari' => ['Spalle', 'Tricipiti']];
    eseguiAzioneController(fn() => $eserciziCtrl->salvaEsercizio());

    // Esercizio 6: Crunch
    $_POST = ['nome' => 'Crunch Addominali', 'descrizione' => 'Flessioni del busto per retto dell addome', 'nuova_attrezzatura_nome' => '', 'tracciamento_carico' => '3', 'gruppi_muscolari' => ['Addominali']];
    eseguiAzioneController(fn() => $eserciziCtrl->salvaEsercizio());

    echo "[OK] Esercizi creati via EserciziController.\n\n";

    $esercizioRepo = new DoctrineEsercizioRepository($entityManager);
    $tuttiEsercizi = $esercizioRepo->findAll();
    $trovaEs = function(string $nome) use ($tuttiEsercizi) {
        foreach ($tuttiEsercizi as $ex) {
            if ($ex->getNomeEsercizio() === $nome) return $ex;
        }
        return null;
    };

    $pancaPiana = $trovaEs('Panca Piana');
    $squat = $trovaEs('Squat');
    $tapisRoulant = $trovaEs('Corsa Tapis Roulant');
    $latMachine = $trovaEs('Lat Machine Avanti');
    $lentoAvanti = $trovaEs('Lento Avanti Manubri');
    $crunch = $trovaEs('Crunch Addominali');

    // -------------------------------------------------------------------------
    // 9. Attività Pianificate (Corsi) e Prenotazioni via AttivitaPianificataController
    // -------------------------------------------------------------------------
    echo "[DEBUG] Invocazione AttivitaPianificataController per corsi e prenotazioni...\n";
    $attPianifCtrl = new AttivitaPianificataController($entityManager, $session);
    $session->setUtenteLoggato($admin);

    $oggiStr = (new \DateTimeImmutable('today'))->format('Y-m-d');
    $domaniStr = (new \DateTimeImmutable('tomorrow'))->format('Y-m-d');

    // Pianificazione Corso Zumba (Oggi 10:00, Sala A, Coach Luigi)
    $_POST = ['data' => $oggiStr, 'ora' => '10', 'sala' => (string)$salaA->getId(), 'allenatore' => (string)$coachLuigi->getId(), 'attivita' => (string)$zumba->getId()];
    eseguiAzioneController(fn() => $attPianifCtrl->creaAttivitaPianificata());

    // Pianificazione Corso Pilates (Oggi 18:00, Sala B capienza 1, Coach Marco)
    $_POST = ['data' => $oggiStr, 'ora' => '18', 'sala' => (string)$salaB->getId(), 'allenatore' => (string)$coachMarco->getId(), 'attivita' => (string)$pilates->getId()];
    eseguiAzioneController(fn() => $attPianifCtrl->creaAttivitaPianificata());

    // Pianificazione Corso Spinning (Domani 11:00, Sala A, Coach Luigi)
    $_POST = ['data' => $domaniStr, 'ora' => '11', 'sala' => (string)$salaA->getId(), 'allenatore' => (string)$coachLuigi->getId(), 'attivita' => (string)$spinning->getId()];
    eseguiAzioneController(fn() => $attPianifCtrl->creaAttivitaPianificata());

    $attPianifRepo = new DoctrineAttivitaPianificataRepository($entityManager);
    $tuttiCorsi = $attPianifRepo->findAll();
    $trovaCorso = function(string $nomeAttivita) use ($tuttiCorsi) {
        foreach ($tuttiCorsi as $c) {
            if ($c->getAttivita() && $c->getAttivita()->getNome() === $nomeAttivita) return $c;
        }
        return null;
    };

    $corsoZumbaObj = $trovaCorso('Zumba Fitness');
    $corsoPilatesObj = $trovaCorso('Pilates');
    $corsoSpinningObj = $trovaCorso('Spinning');

    // Prenotazioni Chiara Bianchi
    $session->setUtenteLoggato($chiara);
    if ($corsoZumbaObj) {
        $_GET = ['id' => $corsoZumbaObj->getId()];
        eseguiAzioneController(fn() => $attPianifCtrl->prenotaAttivita());
    }
    if ($corsoPilatesObj) {
        $_GET = ['id' => $corsoPilatesObj->getId()];
        eseguiAzioneController(fn() => $attPianifCtrl->prenotaAttivita());
    }

    // Prenotazioni ed inserimento in Coda di Attesa per Alessia Gialli
    $session->setUtenteLoggato($alessia);
    if ($corsoZumbaObj) {
        $_GET = ['id' => $corsoZumbaObj->getId()];
        eseguiAzioneController(fn() => $attPianifCtrl->prenotaAttivita());
    }
    if ($corsoPilatesObj) {
        $_GET = ['id' => $corsoPilatesObj->getId()];
        eseguiAzioneController(fn() => $attPianifCtrl->prenotaAttivita()); // Entra in Coda di Attesa!
    }
    if ($corsoSpinningObj) {
        $_GET = ['id' => $corsoSpinningObj->getId()];
        eseguiAzioneController(fn() => $attPianifCtrl->prenotaAttivita());
    }

    // Sessioni Private PT
    $session->setUtenteLoggato($coachLuigi);
    $_POST = ['id_cliente' => (string)$chiara->getId(), 'data' => $oggiStr, 'ora_inizio' => '15:00', 'ora_fine' => '16:00'];
    eseguiAzioneController(fn() => $attPianifCtrl->prenotaSessionePrivata());

    $session->setUtenteLoggato($coachMarco);
    $_POST = ['id_cliente' => (string)$alessia->getId(), 'data' => $domaniStr, 'ora_inizio' => '16:00', 'ora_fine' => '17:00'];
    eseguiAzioneController(fn() => $attPianifCtrl->prenotaSessionePrivata());

    echo "[OK] Corsi, prenotazioni, code di attesa e sessioni private gestiti tramite AttivitaPianificataController.\n\n";

    // -------------------------------------------------------------------------
    // 10. Richiesta, Creazione ed Invio Schede di Allenamento via SchedaAllenamentoController
    // -------------------------------------------------------------------------
    echo "[DEBUG] Invocazione SchedaAllenamentoController per richieste e creazione schede...\n";
    $schedaCtrl = new SchedaAllenamentoController($entityManager, $session);

    // Scenario 1: Scheda Attiva per Chiara prodotta direttamente da Coach Luigi
    $schedaChiara = new \App\Entity\Scheda(
        "Protocollo Forza & Tonificazione Chiara",
        new \DateTimeImmutable('-15 days'),
        new \DateTimeImmutable('+15 days'),
        "Aumento forza massimale e tonificazione muscolare",
        $chiara,
        $coachLuigi
    );
    $entityManager->persist($schedaChiara);
    $entityManager->flush();
    $chiara->setScheda($schedaChiara);

    $workoutA = new \App\Entity\Allenamento("Allenamento A - Parte Superiore", "Focus Petto, Spalle, Dorso");
    $schedaChiara->addAllenamento($workoutA);
    $entityManager->persist($workoutA);

    $detA1 = new \App\Entity\DettaglioAllenamento($pancaPiana, $workoutA, 4, 8, 50.0, null);
    $detA2 = new \App\Entity\DettaglioAllenamento($latMachine, $workoutA, 3, 10, 45.0, null);
    $detA3 = new \App\Entity\DettaglioAllenamento($lentoAvanti, $workoutA, 3, 12, 12.0, null);
    $workoutA->addDettaglio($detA1);
    $workoutA->addDettaglio($detA2);
    $workoutA->addDettaglio($detA3);
    $entityManager->persist($detA1);
    $entityManager->persist($detA2);
    $entityManager->persist($detA3);

    $workoutB = new \App\Entity\Allenamento("Allenamento B - Parte Inferiore & Cardio", "Focus Gambe, Core, Resistenza");
    $schedaChiara->addAllenamento($workoutB);
    $entityManager->persist($workoutB);

    $detB1 = new \App\Entity\DettaglioAllenamento($squat, $workoutB, 4, 10, 60.0, null);
    $detB2 = new \App\Entity\DettaglioAllenamento($crunch, $workoutB, 3, 20, 0.0, null);
    $detB3 = new \App\Entity\DettaglioAllenamento($tapisRoulant, $workoutB, 1, null, 0.0, '20m');
    $workoutB->addDettaglio($detB1);
    $workoutB->addDettaglio($detB2);
    $workoutB->addDettaglio($detB3);
    $entityManager->persist($detB1);
    $entityManager->persist($detB2);
    $entityManager->persist($detB3);
    $entityManager->flush();

    // Scenario 2: Richiesta Scheda da Alessia Gialli via SchedaAllenamentoController
    $session->setUtenteLoggato($alessia);
    $_POST = ['obiettivo' => 'Miglioramento postura e definizione muscolare', 'n_allenamenti' => '3', 'cf_allenatore' => $coachMarco->getCF()];
    eseguiAzioneController(fn() => $schedaCtrl->apriFormRichiestaScheda());

    // Scenario 3: Bozza per Elena Verde
    $schedaElenaBozza = new \App\Entity\Scheda(
        "Bozza Funzionale Elena",
        new \DateTimeImmutable('today'),
        new \DateTimeImmutable('+1 month'),
        "Riaffiatamento motorio e ricomposizione corporea",
        $elena,
        $coachMarco
    );
    $entityManager->persist($schedaElenaBozza);
    $entityManager->flush();

    echo "[OK] Schede di allenamento generate via SchedaAllenamentoController.\n\n";

    // -------------------------------------------------------------------------
    // 11. Progressi Storici e Misure Corporee
    // -------------------------------------------------------------------------
    echo "[DEBUG] Registrazione Progressi storici e Misure Corporee...\n";
    $progressiDati = [
        ['-14 days 10:00:00', $pancaPiana, 'carico', 45.0],
        ['-10 days 10:00:00', $pancaPiana, 'carico', 47.5],
        ['-5 days 10:00:00', $pancaPiana, 'carico', 50.0],
        ['-1 days 10:00:00', $pancaPiana, 'carico', 52.5],

        ['-14 days 10:00:00', $pancaPiana, 'ripetizioni', 6.0],
        ['-10 days 10:00:00', $pancaPiana, 'ripetizioni', 7.0],
        ['-5 days 10:00:00', $pancaPiana, 'ripetizioni', 8.0],
        ['-1 days 10:00:00', $pancaPiana, 'ripetizioni', 8.0],

        ['-14 days 10:00:00', $squat, 'carico', 55.0],
        ['-10 days 10:00:00', $squat, 'carico', 58.0],
        ['-5 days 10:00:00', $squat, 'carico', 60.0],
        ['-1 days 10:00:00', $squat, 'carico', 65.0],

        ['-14 days 10:00:00', $tapisRoulant, 'durata', 15.0],
        ['-10 days 10:00:00', $tapisRoulant, 'durata', 18.0],
        ['-5 days 10:00:00', $tapisRoulant, 'durata', 20.0],
        ['-1 days 10:00:00', $tapisRoulant, 'durata', 25.0],
    ];

    foreach ($progressiDati as $pDato) {
        $dataRef = new \DateTimeImmutable($pDato[0]);
        $es = $pDato[1];
        $tipoP = $pDato[2];
        $val = $pDato[3];

        if ($tipoP === 'carico') {
            $progObj = new ProgressoCarico($dataRef, $chiara, $es, $val);
        } elseif ($tipoP === 'ripetizioni') {
            $progObj = new ProgressoRipetizioni($dataRef, $chiara, $es, $val);
        } else {
            $progObj = new ProgressoDurata($dataRef, $chiara, $es, $val);
        }
        $entityManager->persist($progObj);
    }

    $misureChiara = new Parametri(
        60.0, 168.0, new \DateTimeImmutable('-1 days'), $chiara,
        28.0, 28.0, 27.0, 27.0, 50.0, 50.0, 34.0, 34.0, 88.0, 66.0, 96.0, 94.0
    );
    $entityManager->persist($misureChiara);

    $misureAlessia = new Parametri(
        55.0, 162.0, new \DateTimeImmutable('-3 days'), $alessia,
        25.0, 25.0, 24.0, 24.0, 48.0, 48.0, 32.0, 32.0, 84.0, 62.0, 90.0, 88.0
    );
    $entityManager->persist($misureAlessia);

    $entityManager->flush();
    echo "[OK] Progressi e misure corporee registrati.\n\n";

    // -------------------------------------------------------------------------
    // 12. Invio Messaggi tra utenti via MessaggiController
    // -------------------------------------------------------------------------
    echo "[DEBUG] Invocazione MessaggiController per invio messaggi...\n";
    $msgCtrl = new MessaggiController($entityManager, $session);

    // Messaggio 1: Admin -> Coach Luigi
    $session->setUtenteLoggato($admin);
    $_POST = ['destinatario_id' => (string)$coachLuigi->getId(), 'oggetto' => 'Riunione di Staff Venerdì', 'contenuto' => 'Ciao Luigi, vi ricordo la riunione di aggiornamento venerdì alle 18:00 in Sala Riunioni.'];
    eseguiAzioneController(fn() => $msgCtrl->inviaMessaggio());

    // Messaggio 2: Coach Luigi -> Chiara
    $session->setUtenteLoggato($coachLuigi);
    $_POST = ['destinatario_id' => (string)$chiara->getId(), 'oggetto' => 'Nuova Scheda di Allenamento', 'contenuto' => 'Ciao Chiara! La tua nuova scheda "Protocollo Forza & Tonificazione Chiara" è stata caricata. Buon allenamento!'];
    eseguiAzioneController(fn() => $msgCtrl->inviaMessaggio());

    // Messaggio 3: Coach Marco -> Alessia
    $session->setUtenteLoggato($coachMarco);
    $_POST = ['destinatario_id' => (string)$alessia->getId(), 'oggetto' => 'Stato Coda di Attesa Pilates', 'contenuto' => 'Ciao Alessia, ti confermo che sei la prima in coda di attesa per la lezione di Pilates delle 18:00!'];
    eseguiAzioneController(fn() => $msgCtrl->inviaMessaggio());

    // Messaggio 4: Admin -> Chiara
    $session->setUtenteLoggato($admin);
    $_POST = ['destinatario_id' => (string)$chiara->getId(), 'oggetto' => 'Offerta Rinnovo Abbonamento', 'contenuto' => 'Gentile Chiara, grazie per essere parte della famiglia GymFly. Ti ricordiamo la promozione sul rinnovo annuale.'];
    eseguiAzioneController(fn() => $msgCtrl->inviaMessaggio());

    echo "[OK] Messaggi inviati con successo tramite MessaggiController.\n\n";

    echo "=========================================================================\n";
    echo "Popolamento del database completato con SUCCESSO tramite i Controller in src/Control/!\n";
    echo "=========================================================================\n";

} catch (\InvalidArgumentException $e) {
    echo "\n[ERRORE DI DOMINIO] I dati forniti non rispettano le regole dell'Entity:\n" . $e->getMessage() . "\n";
} catch (\Throwable $e) {
    echo "\n[ERRORE FATALE O DI PERSISTENZA] Rilevato un problema:\n" . $e->getMessage() . "\nIn file: " . $e->getFile() . " alla riga " . $e->getLine() . "\n";
}
