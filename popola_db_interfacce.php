<?php

/**
 * Script di popolamento database (Fixtures)
 * 
 * Da eseguire CLI (es: php popola_db_interfacce.php) o tramite browser per i test preliminari.
 * Utilizza il pattern dell'Inversione delle Dipendenze: sfrutta le interfacce per salvare le Entity.
 */

// Abilita la visualizzazione degli errori a livello di runtime
error_reporting(E_ALL);
ini_set('display_errors', '1');

use App\Entity\Amministratore;
use App\Entity\Allenatore;
use App\Entity\Cliente;
use App\Entity\Attivita;
use App\Entity\Palestra;
use App\Entity\Abbonamento;
use App\Entity\AbbonamentoDurata;
use App\Entity\AbbonamentoAttivo;
use App\Entity\Iscrizione;
use App\Entity\CertificatoMedico;
use App\Entity\Messaggio;
use App\Enum\Sesso;
use App\Infrastructure\Doctrine\EntityManagerFactory;


echo "[DEBUG] 1. Caricamento autoload...\n";
require_once __DIR__ . '/vendor/autoload.php';

// 1. CARICAMENTO ENTITY MANAGER
/** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
echo "[DEBUG] 2. Creazione EntityManager tramite Factory...\n";
$entityManager = EntityManagerFactory::create();

echo "[DEBUG] 3. Verifica istanza EntityManager...\n";
if (!$entityManager instanceof \Doctrine\ORM\EntityManagerInterface) {
    die("Errore: Il file di bootstrap non ha restituito un'istanza valida di EntityManagerInterface.\n");
}

echo "Inizio popolamento del database con dati Dummy...\n\n";
var_dump(preg_match('/^[a-zA-ZàèéìòùÀÈÉÌÒÙ\s\']+$/u', 'Mario'));

try {
    // Pulizia preventiva per rendere lo script completamente re-runnable
    echo "[DEBUG] Pulizia preventiva database...\n";
    $conn = $entityManager->getConnection();
    $conn->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
    
    $tables = ['Progresso', 'DettaglioAllenamento', 'Allenamento', 'Scheda', 'Iscrizione', 'AbbonamentoAttivo', 'Abbonamento', 'Messaggio', 'CertificatoMedico', 'AttivitaPianificata', 'Attivita', 'Utente', 'Palestra', 'Tipologia', 'Esercizio', 'Iscritto', 'Abilitazione', 'Allena', 'Riceve'];
    foreach ($tables as $t) {
        $conn->executeStatement("DELETE FROM `$t`");
    }
    
    $conn->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    echo "[OK] Database pulito con successo.\n";

    // 1. Creazione Amministratore (Entità indipendente)
    echo "[DEBUG] 4. Creazione Amministratore...\n";
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
    echo "[OK] Amministratore salvato con successo.\n";

    // 2. Creazione Palestra (Dipende da Amministratore, passato nel costruttore)
    echo "[DEBUG] 5. Creazione Palestra...\n";
    $palestra = new Palestra(
        'GymFly Central',
        'Via delle Palestre 10, Milano',
        'info@gymflycentral.com',
        '0212345678',
        $admin // 5° parametro: l'oggetto Amministratore
    );
    // Questo repository non ha un metodo save() custom, usiamo l'EntityManager
    $entityManager->persist($palestra);
    $entityManager->flush();
    echo "[OK] Palestra salvata con successo.\n";

    // 3. Creazione Allenatore (Richiede obbligatoriamente una Palestra nel costruttore)
    echo "[DEBUG] 6. Creazione Allenatore...\n";
    $allenatore = new Allenatore(
        'Luigi',
        'Verdi',
        'luigi.verdi@gymfly.com',
        'VRDLGU85B02H501Z',
        'Via Milano 2, Torino',
        Sesso::MALE,
        'AllenatorePass88!',
        null,      // 8° parametro (profilePicture, da Utente)
        '372-148-2574',      // 9° parametro (telefono, da Utente)
        $palestra  // 10° parametro: l'oggetto Palestra
    );
    $entityManager->persist($allenatore);
    $entityManager->flush();
    echo "[OK] Allenatore salvato e associato alla Palestra con successo.\n";
    
    // 4. Creazione Cliente (Usa il costruttore completo di Cliente)
    echo "[DEBUG] 7. Creazione Cliente...\n";
    $cliente = new Cliente(
        'Chiara',
        'Bianchi',
        'chiara.bianchi@gymfly.com',
        'BNCCHR90A41H501Y', // CF fittizio
        'Via Garibaldi 10, Milano',
        Sesso::FEMALE, // NB: Enum ipotetico
        new \DateTimeImmutable('1990-05-15'), // dataDiNascita
        'Roma',                               // luogoDiNascita
        'Via Garibaldi 10, Milano',           // indirizzoDiDomicilio
        'Carta di Credito',                   // metodoDiPagamento
        'ClientePass123!'                     // password
    );
    $cliente->setPalestra($palestra); // Metodo esistente in Cliente.php
    // Questo repository non ha un metodo save() custom, usiamo l'EntityManager
    $entityManager->persist($cliente);
    $entityManager->flush();
    echo "[OK] Cliente salvato con successo.\n";

    // 5. Creazione Attivita (Esempio aggiuntivo)
    echo "[DEBUG] 8. Creazione Attività...\n";
    $attivita = new Attivita('Zumba Fitness', 'Corso intensivo di Zumba', 20); // Aggiunto maxPartecipanti
    $entityManager->persist($attivita);
    $entityManager->flush();
    
    // Relazione N-N (Allenatore - Attivita, l'owner è Allenatore secondo il tuo XML)
    if (method_exists($allenatore, 'aggiungiAttivitaAbilitata')) {
        $allenatore->aggiungiAttivitaAbilitata($attivita);
        $entityManager->flush(); // Flush dell'aggiornamento della join table
    }
    echo "[OK] Attività salvata e collegata all'allenatore.\n";

    // 6. Creazione Certificato Medico (Relazione 1-1 o N-1 con Cliente)
    echo "[DEBUG] 9. Creazione Certificato Medico...\n";
    $certificato = new CertificatoMedico(
        new \DateTimeImmutable('2024-01-10'),
        'Dr. Gregory House',
        $cliente,
        '/uploads/certificati/cert_chiara_2024.pdf'
    );
    $entityManager->persist($certificato);
    $entityManager->flush();
    echo "[OK] Certificato Medico salvato (Scadenza calcolata: " . $certificato->getDataScadenza()->format('d/m/Y') . ").\n";

    // 7. Creazione Messaggio (Relazione Mittente -> N Destinatari)
    echo "[DEBUG] 10. Invio Messaggio di benvenuto...\n";
    $messaggio = new Messaggio(
        $allenatore, // Mittente
        'Benvenuta nel team!', // Oggetto
        'Ciao Chiara, sono Luigi. Ho visto la tua iscrizione, quando vuoi iniziamo con il primo allenamento!' // Contenuto
    );
    // Aggiungiamo il cliente come destinatario
    $messaggio->aggiungiDestinatario($cliente);
    
    // Salvataggio tramite EntityManager
    $entityManager->persist($messaggio);
    $entityManager->flush();
    echo "[OK] Messaggio inviato correttamente dall'allenatore al cliente.\n";

    // 8. Creazione Abbonamento e associazione al Cliente
    echo "[DEBUG] 11. Creazione AbbonamentoDurata...\n";
    $abbonamentoDurata = new AbbonamentoDurata('Mensile Open', 'Fitness', 30);
    $entityManager->persist($abbonamentoDurata);
    $entityManager->flush();

    echo "[DEBUG] 12. Creazione AbbonamentoAttivo e associazione al Cliente...\n";
    $abbonamentoAttivo = new AbbonamentoAttivo(new \DateTimeImmutable('-5 days'), $abbonamentoDurata);
    $entityManager->persist($abbonamentoAttivo);
    
    $cliente->setAbbonamento($abbonamentoAttivo);
    $entityManager->flush();
    echo "[OK] AbbonamentoAttivo salvato ed associato.\n";

    // 9. Creazione Iscrizione
    echo "[DEBUG] 13. Creazione Iscrizione...\n";
    $iscrizione = new Iscrizione(new \DateTimeImmutable('-5 days'), $cliente);
    $entityManager->persist($iscrizione);
    $entityManager->flush();
    echo "[OK] Iscrizione salvata (Scadenza: " . $iscrizione->getDataFine()->format('d/m/Y') . ").\n";

    // 10. Creazione Tipologia ed Esercizi di test
    echo "[DEBUG] 14. Creazione Tipologie ed Esercizi...\n";
    $tipologiaRepo = $entityManager->getRepository(\App\Entity\Tipologia::class);
    $tipologiaForza = $tipologiaRepo->findOneBy(['nomeTipologia' => 'Forza']) 
        ?: new \App\Entity\Tipologia('Forza');
    $tipologiaCardio = $tipologiaRepo->findOneBy(['nomeTipologia' => 'Cardio']) 
        ?: new \App\Entity\Tipologia('Cardio');
    $entityManager->persist($tipologiaForza);
    $entityManager->persist($tipologiaCardio);
    $entityManager->flush();

    $esercizioRepo = $entityManager->getRepository(\App\Entity\Esercizio::class);
    $pancaPiana = $esercizioRepo->findOneBy(['nomeEsercizio' => 'Panca Piana']) 
        ?: new \App\Entity\Esercizio('Panca Piana', 'Spinte su panca piana con bilanciere', $tipologiaForza, null, $allenatore);
    $squat = $esercizioRepo->findOneBy(['nomeEsercizio' => 'Squat']) 
        ?: new \App\Entity\Esercizio('Squat', 'Squat posteriore con bilanciere', $tipologiaForza, null, $allenatore);
    $tapisRoulant = $esercizioRepo->findOneBy(['nomeEsercizio' => 'Corsa su Tapis Roulant']) 
        ?: new \App\Entity\Esercizio('Corsa su Tapis Roulant', 'Corsa cardio su tapis roulant', $tipologiaCardio, null, $allenatore);
    $entityManager->persist($pancaPiana);
    $entityManager->persist($squat);
    $entityManager->persist($tapisRoulant);
    $entityManager->flush();

    // 11. Creazione Scheda di allenamento per Chiara Bianchi
    echo "[DEBUG] 15. Creazione Scheda e Allenamenti...\n";
    $schedaRepo = $entityManager->getRepository(\App\Entity\Scheda::class);
    $vecchieSchede = $schedaRepo->findBy(['cliente' => $cliente]);
    foreach ($vecchieSchede as $vs) {
        $cliente->setScheda(null);
        $entityManager->flush();
        $entityManager->remove($vs);
    }
    $entityManager->flush();

    $scheda = new \App\Entity\Scheda(
        "Scheda Forza e Cardio",
        new \DateTimeImmutable('-15 days'),
        new \DateTimeImmutable('+15 days'),
        "Aumento forza e resistenza cardiovascolare",
        $cliente,
        $allenatore
    );
    $entityManager->persist($scheda);
    $entityManager->flush();
    $cliente->setScheda($scheda);
    $entityManager->flush();

    // Creazione allenamenti A e B
    $workoutA = new \App\Entity\Allenamento("Allenamento A", "Focus sulla parte superiore ed inferiore del corpo");
    $scheda->addAllenamento($workoutA);
    $entityManager->persist($workoutA);

    $detA1 = new \App\Entity\DettaglioAllenamento($pancaPiana, $workoutA, 4, 8, 50.0, null);
    $workoutA->addDettaglio($detA1);
    $entityManager->persist($detA1);

    $detA2 = new \App\Entity\DettaglioAllenamento($squat, $workoutA, 3, 10, 60.0, null);
    $workoutA->addDettaglio($detA2);
    $entityManager->persist($detA2);

    $workoutB = new \App\Entity\Allenamento("Allenamento B", "Focus cardiovascolare");
    $scheda->addAllenamento($workoutB);
    $entityManager->persist($workoutB);

    $detB1 = new \App\Entity\DettaglioAllenamento($tapisRoulant, $workoutB, 1, null, 0.0, '20m');
    $workoutB->addDettaglio($detB1);
    $entityManager->persist($detB1);

    $entityManager->flush();

    // 12. Creazione Progressi storici per Chiara
    echo "[DEBUG] 16. Creazione Progressi Esercizi...\n";
    // Pulizia vecchi progressi per non duplicarli in caso di riesecuzioni multiple dello script
    $progressoRepo = $entityManager->getRepository(\App\Entity\Progresso::class);
    $vecchiProgressi = $progressoRepo->findBy(['cliente' => $cliente]);
    foreach ($vecchiProgressi as $vp) {
        $entityManager->remove($vp);
    }
    $entityManager->flush();

    $progressiDati = [
        // Panca Piana
        ['-14 days 10:00:00', $pancaPiana, 'carico', 48.0],
        ['-10 days 10:00:00', $pancaPiana, 'carico', 49.0],
        ['-5 days 10:00:00', $pancaPiana, 'carico', 50.0],
        ['-1 days 10:00:00', $pancaPiana, 'carico', 52.0],

        ['-14 days 10:00:00', $pancaPiana, 'ripetizioni', 6.0],
        ['-10 days 10:00:00', $pancaPiana, 'ripetizioni', 7.0],
        ['-5 days 10:00:00', $pancaPiana, 'ripetizioni', 8.0],
        ['-1 days 10:00:00', $pancaPiana, 'ripetizioni', 8.0],

        // Squat
        ['-14 days 10:00:00', $squat, 'carico', 55.0],
        ['-10 days 10:00:00', $squat, 'carico', 58.0],
        ['-5 days 10:00:00', $squat, 'carico', 60.0],
        ['-1 days 10:00:00', $squat, 'carico', 65.0],

        ['-14 days 10:00:00', $squat, 'ripetizioni', 8.0],
        ['-10 days 10:00:00', $squat, 'ripetizioni', 9.0],
        ['-5 days 10:00:00', $squat, 'ripetizioni', 10.0],
        ['-1 days 10:00:00', $squat, 'ripetizioni', 10.0],

        // Tapis Roulant
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
            $progObj = new \App\Entity\ProgressoCarico($dataRef, $cliente, $es, $val);
        } elseif ($tipoP === 'ripetizioni') {
            $progObj = new \App\Entity\ProgressoRipetizioni($dataRef, $cliente, $es, $val);
        } else {
            $progObj = new \App\Entity\ProgressoDurata($dataRef, $cliente, $es, $val);
        }
        $entityManager->persist($progObj);
    }
    $entityManager->flush();
    echo "[OK] Progressi storici inseriti con successo.\n";

    echo "\nPopolamento del database completato senza errori!\n";

} catch (\InvalidArgumentException $e) {
    echo "\n[ERRORE DI DOMINIO] I dati forniti non rispettano le regole dell'Entity:\n" . $e->getMessage() . "\n";
} catch (\Throwable $e) {
    echo "\n[ERRORE FATALE O DI PERSISTENZA] Rilevato un problema:\n" . $e->getMessage() . "\nIn file: " . $e->getFile() . " alla riga " . $e->getLine() . "\n";
}