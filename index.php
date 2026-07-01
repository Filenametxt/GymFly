<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/vendor/autoload.php';

// Import delle classi necessarie
use App\Infrastructure\Doctrine\EntityManagerFactory;
use App\Foundation\Session;
use App\Control\AutenticazioneController;
use App\View\AutenticazioneViewSmarty;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;

// Determina quale azione è stata richiesta (da GET o POST)
$action = $_REQUEST['action'] ?? 'home'; // Se nessuna azione, mostra la home

// --- Bootstrap dell'applicazione ---
// Questi oggetti verrebbero creati da un container di dipendenze in un'app reale
$entityManager = EntityManagerFactory::create();
$session = new Session();

// --- Inizializzazione dei Controller ---
// In un'applicazione complessa, questo verrebbe gestito in modo più dinamico
$clienteRepo = new DoctrineClienteRepository($entityManager);
$authView = new AutenticazioneViewSmarty();
$authController = new AutenticazioneController($clienteRepo, $authView, $session);


// --- ROUTING ---
// Il "vigile urbano" che smista le richieste

switch ($action) {
    case 'login':
        // L'utente ha inviato il form di login (method POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->login();
        } else {
            // L'utente ha solo richiesto la pagina di login (method GET)
            $authView->mostraFormLogin();
        }
        break;

    case 'logout':
        $authController->logout();
        break;

    // Aggiungi qui altri 'case' per 'registrazione', 'visualizzaProfilo', etc.

    default:
        // Azione di default: mostra una pagina di benvenuto o la dashboard
        echo "<h1>Benvenuto in GymFly!</h1><p>Seleziona un'azione.</p>";
        break;
}