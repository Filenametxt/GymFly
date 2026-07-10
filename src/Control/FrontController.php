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
use App\Control\SchedaAllenamentoController;
use App\Foundation\Persistence\Repository\DoctrineSchedaRepository;
use App\View\SchedaAllenamentoViewSmarty;

class FrontController
{
    private array $routes = [];

    public function __construct()
    {
        $this->routes = [
            '/' => [VisualizzazioneController::class, 'mostraHome'],
            '/home' => [VisualizzazioneController::class, 'mostraHome'],
            
            '/login' => [AutenticazioneController::class, 'login'],
            '/logout' => [AutenticazioneController::class, 'logout'],
            '/registrazione' => [AutenticazioneController::class, 'registraAmministratore'],
            
            '/profilo' => [ProfiloController::class, 'visualizzaProfilo'],
            '/visualizza-profilo' => [ProfiloController::class, 'visualizzaProfilo'],
            '/modifica-anagrafica' => [ProfiloController::class, 'modificaAnagrafica'],
            '/aggiorna-misure' => [ProfiloController::class, 'aggiornaMisureCorporee'],
            '/inserisci-misure' => [ProfiloController::class, 'inserisciMisureCorporee'],
            '/carica-certificato' => [ProfiloController::class, 'caricaCertificato'],
            '/cambia-password' => [ProfiloController::class, 'cambiaPassword'],
            '/visualizza-grafico' => [ProfiloController::class, 'visualizzaGrafico'],
            '/carica-foto' => [ProfiloController::class, 'caricaFotoProfilo'],
            
            '/dashboard-admin' => [VisualizzazioneController::class, 'mostraDashboardAdmin'],
            '/dashboard-allenatore' => [VisualizzazioneController::class, 'mostraDashboardAllenatore'],
            '/dashboard-cliente' => [VisualizzazioneController::class, 'mostraDashboardCliente'],
            
            '/messaggi' => [MessaggiController::class, 'mostraMessaggi'],
            '/invia-messaggio' => [MessaggiController::class, 'inviaMessaggio'],
            
            '/clienti' => [VisualizzazioneUtentiController::class, 'visualizzaClienti'],
            '/allenatori' => [VisualizzazioneUtentiController::class, 'visualizzaAllenatori'],
            '/abbonamento' => [AbbonamentiController::class, 'visualizzaAbbonamenti'],
            '/gestione-abbonamento' => [AbbonamentiController::class, 'gestisciAbbonamento'],
            
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
            '/richiedi-scheda' => [SchedaAllenamentoController::class, 'apriFormRichiestaScheda'],
            '/crea-scheda' => [SchedaAllenamentoController::class, 'apriFormCreazioneScheda'],
            '/modifica-scheda' => [SchedaAllenamentoController::class, 'apriFormModificaScheda'],
            '/salva-scheda' => [SchedaAllenamentoController::class, 'salvaScheda'],
            '/invia-scheda' => [SchedaAllenamentoController::class, 'inviaScheda'],
            '/elimina-scheda' => [SchedaAllenamentoController::class, 'eliminaScheda'],
            '/rimuovi-scheda' => [SchedaAllenamentoController::class, 'eliminaScheda'],
            '/visualizza-scheda' => [SchedaAllenamentoController::class, 'visualizzaScheda'],
            '/modifica-dettagli' => [SchedaAllenamentoController::class, 'apriFormModificaSchedaCliente'],
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

        list($controllerClass, $method) = $this->routes[$route];

        // Avvia la sessione
        $session = new Session();

        // Istanzia l'EntityManager
        $entityManager = EntityManagerFactory::create();

        // Inizializza la View di Default per messaggi di autenticazione/errore generici
        $authView = new AutenticazioneViewSmarty();

        // Risoluzione delle dipendenze dei Controller tramite Switch basato sulla classe
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
                $utentiView = new VisualizzazioneUtentiViewSmarty();
                $controller = new VisualizzazioneUtentiController(
                    $entityManager,
                    $clienteRepo,
                    $allenatoreRepo,
                    $utentiView,
                    $session
                );
                break;

            case AbbonamentiController::class:
                $abbonamentoView = new AbbonamentiViewSmarty();
                $controller = new AbbonamentiController(
                    $entityManager,
                    $abbonamentoView,
                    $session
                );
                break;

            case VisualizzazioneController::class:
                $visualizzazioneView = new VisualizzazioneViewSmarty();
                $controller = new VisualizzazioneController(
                    $entityManager,
                    $visualizzazioneView,
                    $session
                );
                break;

            case MessaggiController::class:
                $messaggioRepo = new DoctrineMessaggioRepository($entityManager);
                $messaggiView = new MessaggiViewSmarty();
                $controller = new MessaggiController(
                    $entityManager,
                    $messaggioRepo,
                    $messaggiView,
                    $session
                );
                break;

            case AmministratoreController::class:
                $clienteRepo = new DoctrineClienteRepository($entityManager);
                $allenatoreRepo = new DoctrineAllenatoreRepository($entityManager);
                $attivitaRepo = new DoctrineAttivitaRepository($entityManager);
                $adminView = new AmministratoreViewSmarty();
                $controller = new AmministratoreController(
                    $entityManager,
                    $clienteRepo,
                    $allenatoreRepo,
                    $attivitaRepo,
                    $adminView,
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

            case SchedaAllenamentoController::class:
                $schedaRepo = new DoctrineSchedaRepository($entityManager);
                $schedaView = new SchedaAllenamentoViewSmarty();
                $controller = new SchedaAllenamentoController(
                    $entityManager,
                    $schedaRepo,
                    $schedaView,
                    $session
                );
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
        if (!empty($_SERVER['PATH_INFO'])) {
            return $_SERVER['PATH_INFO'];
        }
        
        $requestUri = $_SERVER['REQUEST_URI'];
        $scriptName = $_SERVER['SCRIPT_NAME'];
        
        if (strpos($requestUri, $scriptName) === 0) {
            $route = substr($requestUri, strlen($scriptName));
        } else {
            $baseDir = dirname($scriptName);
            $baseDir = str_replace('\\', '/', $baseDir);
            if ($baseDir === '/') {
                $route = $requestUri;
            } else {
                $route = substr($requestUri, strlen($baseDir));
            }
        }
        
        $pos = strpos($route, '?');
        if ($pos !== false) {
            $route = substr($route, 0, $pos);
        }
        
        $route = '/' . ltrim($route, '/');
        return $route;
    }
}
