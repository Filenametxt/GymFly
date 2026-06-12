<?php

/**
 * Script di popolamento database (Fixtures)
 * 
 * Da eseguire CLI (es: php popola_db_interfacce.php) o tramite browser per i test preliminari.
 * Utilizza il pattern dell'Inversione delle Dipendenze: sfrutta le interfacce per salvare le Entity.
 */

use App\Entity\Amministratore;
use App\Entity\Allenatore;
use App\Entity\Cliente;
use App\Entity\Attivita;
use App\Entity\Palestra;
use App\Entity\Abbonamento;
use App\Enum\Sesso;


require_once __DIR__ . '/vendor/autoload.php';

// 1. CARICAMENTO ENTITY MANAGER
// Integra lo script con la tua architettura recuperando l'EntityManager dal tuo file bootstrap.
// Sostituisci il percorso di require_once con quello esatto dove configuri Doctrine.
/** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
$entityManager = require_once __DIR__ . '/src/Foundation/bootstrap.php';

if (!$entityManager instanceof \Doctrine\ORM\EntityManagerInterface) {
    die("Errore: Il file di bootstrap non ha restituito un'istanza valida di EntityManagerInterface.\n");
}

echo "Inizio popolamento del database con dati Dummy...\n\n";

try {
    // 1. Creazione Amministratore (Entità indipendente)
    $admin = new Amministratore(
        'Mario',
        'Rossi',
        'admin@gymfly.com',
        'RSSMRA80A01H501U',
        'Via Roma 1, Milano',
        Sesso::MALE, // NB: Assicurati che "MASCHIO" corrisponda al caso reale del tuo Enum Sesso
        'PasswordSicura123!'
    );
    $entityManager->persist($admin);
    $entityManager->flush();
    echo "[OK] Amministratore salvato con successo.\n";

    // 2. Creazione Palestra (Dipende da Amministratore, passato nel costruttore)
    $palestra = new Palestra(
        'GymFly Central',
        'Via delle Palestre 10, Milano',
        '0212345678',
        'info@gymflycentral.com',
        $admin // 5° parametro: l'oggetto Amministratore
    );
    // Questo repository non ha un metodo save() custom, usiamo l'EntityManager
    $entityManager->persist($palestra);
    $entityManager->flush();
    echo "[OK] Palestra salvata con successo.\n";

    // 3. Creazione Allenatore (Richiede obbligatoriamente una Palestra nel costruttore)
    $allenatore = new Allenatore(
        'Luigi',
        'Verdi',
        'luigi.verdi@gymfly.com',
        'VRDLGU85B02H501Z',
        'Via Milano 2, Torino',
        Sesso::MALE,
        'AllenatorePass88!',
        null,      // 8° parametro (profilePicture, da Utente)
        null,      // 9° parametro (telefono, da Utente)
        $palestra  // 10° parametro: l'oggetto Palestra
    );
    $entityManager->persist($allenatore);
    $entityManager->flush();
    echo "[OK] Allenatore salvato e associato alla Palestra con successo.\n";
    
    // 4. Creazione Cliente (Usa il costruttore completo di Cliente)
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
    $attivita = new Attivita('Zumba Fitness', 'Corso intensivo di Zumba', 20); // Aggiunto maxPartecipanti
    $entityManager->persist($attivita);
    $entityManager->flush();
    
    // Relazione N-N (Allenatore - Attivita, l'owner è Allenatore secondo il tuo XML)
    if (method_exists($allenatore, 'aggiungiAttivitaAbilitata')) {
        $allenatore->aggiungiAttivitaAbilitata($attivita);
        $entityManager->flush(); // Flush dell'aggiornamento della join table
    }
    echo "[OK] Attività salvata e collegata all'allenatore.\n";

    // NB: Abbonamento è una classe astratta. Se vuoi testarne la persistenza, 
    // istanzia una sua classe figlia (es: $abbonamento = new AbbonamentoDurata(...)).

    echo "\nPopolamento del database completato senza errori!\n";

} catch (\InvalidArgumentException $e) {
    echo "\n[ERRORE DI DOMINIO] I dati forniti non rispettano le regole dell'Entity:\n" . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo "\n[ERRORE DI PERSISTENZA] Problema con il database:\n" . $e->getMessage() . "\n";
}