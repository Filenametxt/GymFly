<?php

/**
 * Script di popolamento database completo e coerente (Fixtures)
 * 
 * Da eseguire CLI: php popola_db_completo.php
 */

// Abilita la visualizzazione degli errori a livello di runtime
error_reporting(E_ALL);
ini_set('display_errors', '1');

use App\Entity\Amministratore;
use App\Entity\Allenatore;
use App\Entity\Cliente;
use App\Entity\Attivita;
use App\Entity\Palestra;
use App\Entity\Sala;
use App\Entity\AttivitaPianificata;
use App\Entity\CodaAttesa;
use App\Entity\Abbonamento;
use App\Entity\AbbonamentoDurata;
use App\Entity\AbbonamentoAttivo;
use App\Entity\Iscrizione;
use App\Entity\CertificatoMedico;
use App\Entity\Messaggio;
use App\Entity\Tipologia;
use App\Entity\Esercizio;
use App\Entity\Scheda;
use App\Entity\Allenamento;
use App\Entity\DettaglioAllenamento;
use App\Entity\Parametri;
use App\Enum\Sesso;
use App\Infrastructure\Doctrine\EntityManagerFactory;

echo "[DEBUG] 1. Caricamento autoload...\n";
require_once __DIR__ . '/vendor/autoload.php';

echo "[DEBUG] 2. Creazione EntityManager...\n";
$entityManager = EntityManagerFactory::create();

if (!$entityManager instanceof \Doctrine\ORM\EntityManagerInterface) {
    die("Errore: Il file di bootstrap non ha restituito un'istanza valida di EntityManagerInterface.\n");
}

echo "Inizio popolamento completo del database con scenari reali...\n\n";

try {
    // 1. Pulizia preventiva database
    echo "[DEBUG] Pulizia preventiva database...\n";
    $conn = $entityManager->getConnection();
    $conn->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
    
    $tables = [
        'CodaAttesa', 'Progresso', 'DettaglioAllenamento', 'Allenamento', 'Scheda', 
        'Iscrizione', 'AbbonamentoAttivo', 'Abbonamento', 'Messaggio', 'CertificatoMedico', 
        'AttivitaPianificata', 'Attivita', 'Utente', 'Palestra', 'Tipologia', 'Esercizio', 
        'Iscritto', 'Abilitazione', 'Allena', 'Riceve', 'Sala'
    ];
    foreach ($tables as $t) {
        $conn->executeStatement("DELETE FROM `$t`");
    }
    
    $conn->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    echo "[OK] Database pulito con successo.\n";

    // 2. Creazione Amministratore
    echo "[DEBUG] Creazione Amministratore...\n";
    $admin = new Amministratore(
        'Mario',
        'Rossi',
        'admin@gymfly.com',
        'RSSMRA80A01H501U',
        'Via Roma 1, Milano',
        Sesso::MALE,
        'PasswordSicura123!'
    );
    $entityManager->persist($admin);
    $entityManager->flush();
    echo "[OK] Amministratore salvato.\n";

    // 3. Creazione Palestra
    echo "[DEBUG] Creazione Palestra...\n";
    $palestra = new Palestra(
        'GymFly Central',
        'Via delle Palestre 10, Milano',
        'info@gymflycentral.com',
        '0212345678',
        $admin
    );
    $entityManager->persist($palestra);
    $entityManager->flush();
    echo "[OK] Palestra salvata.\n";

    // 4. Creazione Sale
    echo "[DEBUG] Creazione Sale...\n";
    $salaA = new Sala('Sala Corsi Grande A', 20, $palestra);
    $salaB = new Sala('Sala Olistica B', 1, $palestra); // maxPartecipanti = 1 per testare la Coda di Attesa
    $entityManager->persist($salaA);
    $entityManager->persist($salaB);
    $entityManager->flush();
    echo "[OK] Sale create.\n";

    // 5. Creazione Allenatori
    echo "[DEBUG] Creazione Allenatori...\n";
    $coachLuigi = new Allenatore(
        'Luigi',
        'Verdi',
        'luigi.verdi@gymfly.com',
        'VRDLGU85B02H501Z',
        'Via Milano 2, Torino',
        Sesso::MALE,
        'AllenatorePass88!',
        null,
        '372-148-2574',
        $palestra
    );
    
    $coachMarco = new Allenatore(
        'Marco',
        'Neri',
        'marco.neri@gymfly.com',
        'NRAMRC88C03H501K',
        'Corso Sempione 44, Milano',
        Sesso::MALE,
        'MarcoCoach99!',
        null,
        '347-987-6543',
        $palestra
    );

    $entityManager->persist($coachLuigi);
    $entityManager->persist($coachMarco);
    $entityManager->flush();
    echo "[OK] Allenatori salvati.\n";

    // 6. Creazione Attivita
    echo "[DEBUG] Creazione Attività...\n";
    $zumba = new Attivita('Zumba Fitness', 'Corso cardio a ritmo di musica latina', 15);
    $pilates = new Attivita('Pilates', 'Sessioni di tonificazione e stretching posturale', 1); // maxPartecipanti 1
    $spinning = new Attivita('Spinning', 'Allenamento ad alta intensità su bici stazionaria', 12);

    $entityManager->persist($zumba);
    $entityManager->persist($pilates);
    $entityManager->persist($spinning);
    $entityManager->flush();

    // Abilitazioni Allenatori per Attività
    if (method_exists($coachLuigi, 'aggiungiAttivitaAbilitata')) {
        $coachLuigi->aggiungiAttivitaAbilitata($zumba);
        $coachLuigi->aggiungiAttivitaAbilitata($spinning);
    }
    if (method_exists($coachMarco, 'aggiungiAttivitaAbilitata')) {
        $coachMarco->aggiungiAttivitaAbilitata($pilates);
        $coachMarco->aggiungiAttivitaAbilitata($zumba);
    }
    $entityManager->flush();
    echo "[OK] Attività e Abilitazioni configurate.\n";

    // 7. Creazione Clienti
    echo "[DEBUG] Creazione Clienti...\n";
    
    // Cliente 1: Chiara Bianchi (Attiva)
    $chiara = new Cliente(
        'Chiara',
        'Bianchi',
        'chiara.bianchi@gymfly.com',
        'BNCCHR90A41H501Y',
        'Via Garibaldi 10, Milano',
        Sesso::FEMALE,
        new \DateTimeImmutable('1990-05-15'),
        'Roma',
        'Via Garibaldi 10, Milano',
        'Carta di Credito',
        'ClientePass123!'
    );
    $chiara->setPalestra($palestra);

    // Cliente 2: Alessia Gialli (Attiva)
    $alessia = new Cliente(
        'Alessia',
        'Gialli',
        'alessia.gialli@gymfly.com',
        'GLLLSZ95C55H501W',
        'Via Dante 5, Milano',
        Sesso::FEMALE,
        new \DateTimeImmutable('1995-09-22'),
        'Firenze',
        'Via Dante 5, Milano',
        'Contanti',
        'AlessiaPass456!'
    );
    $alessia->setPalestra($palestra);

    // Cliente 3: Davide Viola (Misure inserite ma iscrizione scaduta)
    $davide = new Cliente(
        'Davide',
        'Viola',
        'davide.viola@gymfly.com',
        'VLADVD92E12H501Q',
        'Viale Monza 12, Milano',
        Sesso::MALE,
        new \DateTimeImmutable('1992-12-12'),
        'Torino',
        'Viale Monza 12, Milano',
        'Bonifico Bancario',
        'DavidePass789!'
    );
    $davide->setPalestra($palestra);

    $entityManager->persist($chiara);
    $entityManager->persist($alessia);
    $entityManager->persist($davide);
    $entityManager->flush();
    echo "[OK] Clienti creati.\n";

    // 8. Certificati Medici
    echo "[DEBUG] Creazione Certificati Medici...\n";
    $certChiara = new CertificatoMedico(new \DateTimeImmutable('-20 days'), 'Dr. Gregory House', $chiara, '/uploads/certificati/cert_chiara.pdf');
    $certAlessia = new CertificatoMedico(new \DateTimeImmutable('-10 days'), 'Dr. Gregory House', $alessia, '/uploads/certificati/cert_alessia.pdf');
    // Davide ha un certificato scaduto da un anno
    $certDavide = new CertificatoMedico(new \DateTimeImmutable('-380 days'), 'Dr. John Watson', $davide, '/uploads/certificati/cert_davide.pdf');
    
    $entityManager->persist($certChiara);
    $entityManager->persist($certAlessia);
    $entityManager->persist($certDavide);
    $entityManager->flush();
    echo "[OK] Certificati Medici salvati.\n";

    // 9. Abbonamenti e Iscrizioni
    echo "[DEBUG] Configurazione Abbonamenti...\n";
    $abbonamentoMensile = new AbbonamentoDurata('Mensile Open', 'Fitness', 30);
    $abbonamentoTrimestrale = new AbbonamentoDurata('Trimestrale Open', 'Corsi e Sala', 90);
    $entityManager->persist($abbonamentoMensile);
    $entityManager->persist($abbonamentoTrimestrale);
    $entityManager->flush();

    // Abbonamento Attivo Chiara (Inizio 5 giorni fa)
    $abbChiara = new AbbonamentoAttivo(new \DateTimeImmutable('-5 days'), $abbonamentoMensile);
    $entityManager->persist($abbChiara);
    $chiara->setAbbonamento($abbChiara);
    $iscrChiara = new Iscrizione(new \DateTimeImmutable('-5 days'), $chiara);
    $entityManager->persist($iscrChiara);

    // Abbonamento Attivo Alessia (Inizio 10 giorni fa)
    $abbAlessia = new AbbonamentoAttivo(new \DateTimeImmutable('-10 days'), $abbonamentoTrimestrale);
    $entityManager->persist($abbAlessia);
    $alessia->setAbbonamento($abbAlessia);
    $iscrAlessia = new Iscrizione(new \DateTimeImmutable('-10 days'), $alessia);
    $entityManager->persist($iscrAlessia);

    // Abbonamento Scaduto Davide (Scaduto da 5 giorni)
    $abbDavide = new AbbonamentoAttivo(new \DateTimeImmutable('-35 days'), $abbonamentoMensile);
    $entityManager->persist($abbDavide);
    $davide->setAbbonamento($abbDavide);
    // Iscrizione annuale ancora attiva
    $iscrDavide = new Iscrizione(new \DateTimeImmutable('-15 days'), $davide);
    $entityManager->persist($iscrDavide);

    $entityManager->flush();
    echo "[OK] Abbonamenti e Iscrizioni salvati.\n";

    // 10. Creazione Attivita Pianificate (Calendario / Corsi)
    echo "[DEBUG] Pianificazione Corsi / Attività...\n";
    
    $oggi = new \DateTimeImmutable('today');
    $domani = new \DateTimeImmutable('tomorrow');

    // Corso 1: Zumba oggi alle 10:00 (Coach Luigi, Sala A)
    $corsoZumbaOggi = new AttivitaPianificata($oggi, 10, $salaA, $coachLuigi, $zumba);
    // Prenotazioni
    $corsoZumbaOggi->aggiungiCliente($chiara);
    $corsoZumbaOggi->aggiungiCliente($alessia);
    $corsoZumbaOggi->setPrenotati(2);
    $entityManager->persist($corsoZumbaOggi);

    // Corso 2: Pilates oggi alle 18:00 (Coach Marco, Sala B - Capienza max = 1)
    $corsoPilatesOggi = new AttivitaPianificata($oggi, 18, $salaB, $coachMarco, $pilates);
    // Chiara si iscrive per prima prendendo l'unico posto
    $corsoPilatesOggi->aggiungiCliente($chiara);
    $corsoPilatesOggi->setPrenotati(1);
    $entityManager->persist($corsoPilatesOggi);

    // Corso 3: Spinning domani alle 11:00 (Coach Luigi, Sala A)
    $corsoSpinningDomani = new AttivitaPianificata($domani, 11, $salaA, $coachLuigi, $spinning);
    $corsoSpinningDomani->aggiungiCliente($alessia);
    $corsoSpinningDomani->setPrenotati(1);
    $entityManager->persist($corsoSpinningDomani);

    $entityManager->flush();
    echo "[OK] Corsi pianificati salvati.\n";

    // 11. Coda di Attesa per Pilates (perché al completo con Chiara)
    echo "[DEBUG] Aggiunta Alessia in Coda di Attesa per Pilates...\n";
    $codaPilates = new CodaAttesa($alessia, $corsoPilatesOggi);
    $entityManager->persist($codaPilates);
    $entityManager->flush();
    echo "[OK] Coda di attesa salvata.\n";

    // 12. Creazione Tipologie ed Esercizi
    echo "[DEBUG] Creazione Tipologie ed Esercizi...\n";
    $tipologiaForza = new Tipologia('Forza');
    $tipologiaCardio = new Tipologia('Cardio');
    $entityManager->persist($tipologiaForza);
    $entityManager->persist($tipologiaCardio);
    $entityManager->flush();

    $pancaPiana = new Esercizio('Panca Piana', 'Spinte su panca piana con bilanciere', $tipologiaForza, null, $coachLuigi);
    $squat = new Esercizio('Squat', 'Squat posteriore con bilanciere', $tipologiaForza, null, $coachLuigi);
    $tapisRoulant = new Esercizio('Corsa su Tapis Roulant', 'Corsa cardio su tapis roulant', $tipologiaCardio, null, $coachLuigi);
    
    $entityManager->persist($pancaPiana);
    $entityManager->persist($squat);
    $entityManager->persist($tapisRoulant);
    $entityManager->flush();
    echo "[OK] Esercizi creati.\n";

    // 13. Creazione Scheda di allenamento per Chiara Bianchi
    echo "[DEBUG] Creazione Scheda e Allenamenti per Chiara...\n";
    $schedaChiara = new Scheda(
        "Forza & Cardio Chiara",
        new \DateTimeImmutable('-15 days'),
        new \DateTimeImmutable('+15 days'),
        "Aumento forza e resistenza cardiovascolare",
        $chiara,
        $coachLuigi
    );
    $entityManager->persist($schedaChiara);
    $entityManager->flush();
    $chiara->setScheda($schedaChiara);

    $workoutA = new Allenamento("Allenamento A (Forza)", "Focus sulla parte superiore ed inferiore del corpo");
    $schedaChiara->addAllenamento($workoutA);
    $entityManager->persist($workoutA);

    $detA1 = new DettaglioAllenamento($pancaPiana, $workoutA, 4, 8, 50.0, null);
    $workoutA->addDettaglio($detA1);
    $entityManager->persist($detA1);

    $detA2 = new DettaglioAllenamento($squat, $workoutA, 3, 10, 60.0, null);
    $workoutA->addDettaglio($detA2);
    $entityManager->persist($detA2);

    $workoutB = new Allenamento("Allenamento B (Cardio)", "Focus resistenza");
    $schedaChiara->addAllenamento($workoutB);
    $entityManager->persist($workoutB);

    $detB1 = new DettaglioAllenamento($tapisRoulant, $workoutB, 1, null, 0.0, '20m');
    $workoutB->addDettaglio($detB1);
    $entityManager->persist($detB1);

    $entityManager->flush();
    echo "[OK] Scheda e allenamenti per Chiara salvati.\n";

    // 14. Creazione Progressi storici per Chiara
    echo "[DEBUG] Creazione Progressi storici per Chiara...\n";
    $progressiDati = [
        ['-14 days 10:00:00', $pancaPiana, 'carico', 48.0],
        ['-10 days 10:00:00', $pancaPiana, 'carico', 49.0],
        ['-5 days 10:00:00', $pancaPiana, 'carico', 50.0],
        ['-1 days 10:00:00', $pancaPiana, 'carico', 52.0],

        ['-14 days 10:00:00', $pancaPiana, 'ripetizioni', 6.0],
        ['-10 days 10:00:00', $pancaPiana, 'ripetizioni', 7.0],
        ['-5 days 10:00:00', $pancaPiana, 'ripetizioni', 8.0],
        ['-1 days 10:00:00', $pancaPiana, 'ripetizioni', 8.0],

        ['-14 days 10:00:00', $squat, 'carico', 55.0],
        ['-10 days 10:00:00', $squat, 'carico', 58.0],
        ['-5 days 10:00:00', $squat, 'carico', 60.0],
        ['-1 days 10:00:00', $squat, 'carico', 65.0],

        ['-14 days 10:00:00', $squat, 'ripetizioni', 8.0],
        ['-10 days 10:00:00', $squat, 'ripetizioni', 9.0],
        ['-5 days 10:00:00', $squat, 'ripetizioni', 10.0],
        ['-1 days 10:00:00', $squat, 'ripetizioni', 10.0],

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
            $progObj = new \App\Entity\ProgressoCarico($dataRef, $chiara, $es, $val);
        } elseif ($tipoP === 'ripetizioni') {
            $progObj = new \App\Entity\ProgressoRipetizioni($dataRef, $chiara, $es, $val);
        } else {
            $progObj = new \App\Entity\ProgressoDurata($dataRef, $chiara, $es, $val);
        }
        $entityManager->persist($progObj);
    }
    
    // Misure Corporee Chiara
    $parametriRepo = new \App\Foundation\Persistence\Repository\DoctrineParametriRepository($entityManager);
    $misureChiara = new Parametri(
        60.0, // peso
        168.0, // altezza
        new \DateTimeImmutable('-1 days'), // data
        $chiara, // cliente
        28.0, // bicipiteDestro
        28.0, // bicipiteSinistro
        27.0, // tricipiteDestro
        27.0, // tricipiteSinistro
        50.0, // cosciaDestra
        50.0, // cosciaSinistra
        34.0, // polpaccioDestro
        34.0, // polpaccioSinistro
        88.0, // misuraPetto
        66.0, // misuraVita
        96.0, // misuraSpalle
        94.0  // misuraFianchi
    );
    $entityManager->persist($misureChiara);

    $entityManager->flush();
    echo "[OK] Progressi e misure per Chiara salvati.\n";

    // 15. Creazione Messaggi
    echo "[DEBUG] Creazione Messaggi...\n";
    $msg1 = new Messaggio($coachLuigi, 'Benvenuta Chiara!', 'Ciao Chiara, ho preparato la tua nuova scheda di allenamento. Facci sapere se hai domande!');
    $msg1->aggiungiDestinatario($chiara);
    $entityManager->persist($msg1);

    $msg2 = new Messaggio($coachMarco, 'Avviso Coda Pilates', 'Ciao Alessia, sei al primo posto in coda di attesa per la lezione di Pilates di stasera!');
    $msg2->aggiungiDestinatario($alessia);
    $entityManager->persist($msg2);

    $entityManager->flush();
    echo "[OK] Messaggi inviati.\n";

    echo "\nPopolamento del database completato con SUCCESSO senza errori!\n";

} catch (\InvalidArgumentException $e) {
    echo "\n[ERRORE DI DOMINIO] I dati forniti non rispettano le regole dell'Entity:\n" . $e->getMessage() . "\n";
} catch (\Throwable $e) {
    echo "\n[ERRORE FATALE O DI PERSISTENZA] Rilevato un problema:\n" . $e->getMessage() . "\nIn file: " . $e->getFile() . " alla riga " . $e->getLine() . "\n";
}
