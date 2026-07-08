<?php
namespace App\Control;

use App\Infrastructure\Doctrine\EntityManagerFactory;
use App\Foundation\Session;
use App\View\AutenticazioneViewSmarty;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineParametriRepository;
use App\Foundation\Persistence\Repository\DoctrineCertificatoMedicoRepository;
use App\View\ProfiloViewSmarty;
use App\Foundation\Persistence\Repository\DoctrineAllenatoreRepository;
use App\View\VisualizzazioneUtentiViewSmarty;
use App\View\AbbonamentiViewSmarty;
use App\View\VisualizzazioneViewSmarty;
use App\View\MessaggiViewSmarty;
use App\Foundation\Persistence\Repository\DoctrineMessaggioRepository;
use App\View\AmministratoreViewSmarty;
use App\Foundation\Persistence\Repository\DoctrineEsercizioRepository;
use App\View\EserciziViewSmarty;
use App\Foundation\Persistence\Repository\DoctrineAttivitaPianificataRepository;
use App\Foundation\Persistence\Repository\DoctrineSessionePrivataRepository;
use App\View\AttivitaPianificataViewSmarty;
use App\Foundation\Persistence\Repository\DoctrineSalaRepository;
use App\Foundation\Persistence\Repository\DoctrineAttivitaRepository;
use App\View\ReportViewSmarty;



class FrontController
{
    private array $routes = [];

    public function __construct()
    {
        // Mappa delle rotte: rotta => [ControllerClass, MethodName]
        $this->routes = [
            '/' => [VisualizzazioneController::class, 'mostraHome'],
            '/login' => [AutenticazioneController::class, 'login'],
            '/logout' => [AutenticazioneController::class, 'logout'],
            '/registrazione' => [AutenticazioneController::class, 'registraAmministratore'],
            '/dashboard-admin' => [VisualizzazioneController::class, 'mostraDashboardAdmin'],
            '/dashboard-allenatore' => [VisualizzazioneController::class, 'mostraDashboardAllenatore'],
            '/dashboard-cliente' => [VisualizzazioneController::class, 'mostraDashboardCliente'],
            '/profilo' => [ProfiloController::class, 'visualizzaProfilo'],
            '/visualizza-profilo' => [ProfiloController::class, 'visualizzaProfilo'],
            '/modifica-anagrafica' => [ProfiloController::class, 'modificaAnagrafica'],
            '/carica-foto' => [ProfiloController::class, 'caricaFotoProfilo'],
            '/aggiorna-misure' => [ProfiloController::class, 'aggiornaMisureCorporee'],
            '/inserisci-misure' => [ProfiloController::class, 'inserisciMisureCorporee'],
            '/visualizza-grafico' => [ProfiloController::class, 'visualizzaGrafico'],
            '/carica-certificato' => [ProfiloController::class, 'caricaCertificato'],
            '/errore' => [VisualizzazioneController::class, 'mostraErrore'],
            '/clienti' => [VisualizzazioneUtentiController::class, 'visualizzaClienti'],
            '/allenatori' => [VisualizzazioneUtentiController::class, 'visualizzaAllenatori'],
            '/gestione-abbonamento' => [AbbonamentiController::class, 'gestisciAbbonamento'],
            '/cambia-password' => [ProfiloController::class, 'cambiaPassword'],
            '/messaggi' => [MessaggiController::class, 'mostraMessaggi'],
            '/invia-messaggio' => [MessaggiController::class, 'inviaMessaggio'],
            '/crea-cliente' => [AmministratoreController::class, 'creaCliente'],
            '/crea-allenatore' => [AmministratoreController::class, 'creaAllenatore'],
            '/crea-attivita' => [AmministratoreController::class, 'creaAttivita'],
            '/abilita-attivita-allenatore' => [AmministratoreController::class, 'abilitaAttivitaAllenatore'],
            '/rimuovi-cliente' => [AmministratoreController::class, 'rimuoviCliente'],
            '/rimuovi-allenatore' => [AmministratoreController::class, 'rimuoviAllenatore'],
            '/rimuovi-attivita' => [AmministratoreController::class, 'rimuoviAttivita'],
            '/crea-esercizio' => [EserciziController::class, 'apriFormCreazioneEsercizio'],
            '/valida-esercizio' => [EserciziController::class, 'compilaDatiEsercizio'],
            '/salva-esercizio' => [EserciziController::class, 'salvaEsercizio'],
            '/copia-esercizio' => [EserciziController::class, 'copiaEsercizio'],
            '/elimina-bozza' => [EserciziController::class, 'eliminaBozzaEsercizio'],
            '/calendario' => [AttivitaPianificataController::class, 'visualizzaCalendario'],
            '/prenota-attivita' => [AttivitaPianificataController::class, 'prenotaAttivita'],
            '/disdici-prenotazione' => [AttivitaPianificataController::class, 'disdiciPrenotazione'],
            '/prenota-sessione-privata' => [AttivitaPianificataController::class, 'prenotaSessionePrivata'],
            '/crea-attivita-pianificata' => [AttivitaPianificataController::class, 'creaAttivitaPianificata'],
            '/rimuovi-attivita-pianificata' => [AttivitaPianificataController::class, 'rimuoviAttivitaPianificata'],
            '/disdici-sessione-privata' => [AttivitaPianificataController::class, 'disdiciSessionePrivata'],
            '/report' => [ReportController::class, 'visualizzaReport'],
        ];
    }

    /**
     * Avvia il Front Controller, risolve la rotta richiesta ed esegue l'azione associata.
     */
    public function run(): void
    {
        $route = $this->resolveRoute();

        if (!isset($this->routes[$route])) {
            header("HTTP/1.0 404 Not Found");
            echo "<h1>404 Not Found</h1><p>La pagina richiesta non esiste.</p>";
            return;
        }

        [$controllerClass, $method] = $this->routes[$route];

        // Bootstrap dei componenti dell'applicazione
        $entityManager = EntityManagerFactory::create();
        $session = new Session();
        $authView = new AutenticazioneViewSmarty();

        // Inizializza il controller richiesto in base alle dipendenze
        $controller = null;
        switch ($controllerClass) {
            case ProfiloController::class:
                $clienteRepo = new DoctrineClienteRepository($entityManager);
                $parametriRepo = new DoctrineParametriRepository($entityManager);
                $certificatoRepo = new DoctrineCertificatoMedicoRepository($entityManager);
                $profiloView = new ProfiloViewSmarty();

                $controller = new ProfiloController(
                    $clienteRepo,
                    $parametriRepo,
                    $certificatoRepo,
                    $profiloView,
                    $session
                );
                break;

            case VisualizzazioneUtentiController::class:
                $clienteRepo = new DoctrineClienteRepository($entityManager);
                $allenatoreRepo = new DoctrineAllenatoreRepository($entityManager);
                $visualizzazioneUtentiView = new VisualizzazioneUtentiViewSmarty();

                $controller = new VisualizzazioneUtentiController(
                    $entityManager,
                    $clienteRepo,
                    $allenatoreRepo,
                    $visualizzazioneUtentiView,
                    $session
                );
                break;

            case VisualizzazioneController::class:
                $visualizzazioneView = new VisualizzazioneViewSmarty();
                $controller = new VisualizzazioneController($entityManager, $visualizzazioneView, $session);
                break;

            case AbbonamentiController::class:
                $abbonamentiView = new AbbonamentiViewSmarty();
                $controller = new AbbonamentiController($entityManager, $abbonamentiView, $session);
                break;

            case MessaggiController::class:
                $messaggioRepo = new DoctrineMessaggioRepository($entityManager);
                $messaggiView = new MessaggiViewSmarty();
                $controller = new MessaggiController($entityManager, $messaggioRepo, $messaggiView, $session);
                break;

            case AmministratoreController::class:
                $clienteRepo = new DoctrineClienteRepository($entityManager);
                $allenatoreRepo = new DoctrineAllenatoreRepository($entityManager);
                $attivitaRepo = new DoctrineAttivitaRepository($entityManager);
                $amministratoreView = new AmministratoreViewSmarty();
                $controller = new AmministratoreController(
                    $entityManager,
                    $clienteRepo,
                    $allenatoreRepo,
                    $attivitaRepo,
                    $amministratoreView,
                    $session
                );
                break;

            case EserciziController::class:
                $esercizioRepo = new DoctrineEsercizioRepository($entityManager);
                $eserciziView = new EserciziViewSmarty();
                $controller = new EserciziController(
                    $entityManager,
                    $esercizioRepo,
                    $eserciziView,
                    $session
                );
                break;

            case AttivitaPianificataController::class:
                $attivitaPianificataRepo = new DoctrineAttivitaPianificataRepository($entityManager);
                $clienteRepo = new DoctrineClienteRepository($entityManager);
                $sessionePrivataRepo = new DoctrineSessionePrivataRepository($entityManager);
                $salaRepo = new DoctrineSalaRepository($entityManager);
                $attivitaRepo = new DoctrineAttivitaRepository($entityManager);
                $allenatoreRepo = new DoctrineAllenatoreRepository($entityManager);
                $attivitaPianificataView = new AttivitaPianificataViewSmarty();
                $controller = new AttivitaPianificataController(
                    $entityManager,
                    $attivitaPianificataRepo,
                    $clienteRepo,
                    $sessionePrivataRepo,
                    $salaRepo,
                    $attivitaRepo,
                    $allenatoreRepo,
                    $attivitaPianificataView,
                    $session
                );
                break;

            case ReportController::class:
                $reportView = new ReportViewSmarty();
                $controller = new ReportController($reportView, $session);
                break;

            default:
                $controller = new $controllerClass($entityManager, $authView, $session);
                break;
        }

        // Esegue l'azione specifica
        $controller->$method();
    }

    /**
     * Risolve il percorso URL per estrarre la rotta pulita.
     */
    private function resolveRoute(): string
    {
        // 1. Prova con PATH_INFO (es: index.php/login -> /login)
        if (!empty($_SERVER['PATH_INFO'])) {
            return '/' . trim($_SERVER['PATH_INFO'], '/');
        }

        // 2. Prova con REQUEST_URI (es: /public/login -> /login)
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestPath = parse_url($requestUri, PHP_URL_PATH);

        $scriptName = $_SERVER['SCRIPT_NAME']; // es: /GymFly/public/index.php
        $basePath = dirname($scriptName); // es: /GymFly/public

        if (strpos($requestPath, $basePath) === 0) {
            $requestPath = substr($requestPath, strlen($basePath));
        }

        // Rimuove l'eventuale index.php residuo
        if (strpos($requestPath, '/index.php') === 0) {
            $requestPath = substr($requestPath, 10);
        } elseif ($requestPath === 'index.php') {
            $requestPath = '';
        }

        return '/' . trim($requestPath, '/');
    }
}
