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
            '/carica-certificato' => [ProfiloController::class, 'caricaCertificato'],
            '/errore' => [VisualizzazioneController::class, 'mostraErrore'],
            '/clienti' => [VisualizzazioneUtentiController::class, 'visualizzaClienti'],
            '/allenatori' => [VisualizzazioneUtentiController::class, 'visualizzaAllenatori'],
            '/gestione-abbonamento' => [AbbonamentiController::class, 'gestisciAbbonamento'],
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
        if ($controllerClass === ProfiloController::class) {
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
        } elseif ($controllerClass === VisualizzazioneUtentiController::class) {
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
        } elseif ($controllerClass === VisualizzazioneController::class) {
            $visualizzazioneView = new VisualizzazioneViewSmarty();
            $controller = new VisualizzazioneController($entityManager, $visualizzazioneView, $session);
        } elseif ($controllerClass === AbbonamentiController::class) {
            $abbonamentiView = new AbbonamentiViewSmarty();
            $controller = new AbbonamentiController($entityManager, $abbonamentiView, $session);
        } else {
            $controller = new $controllerClass($entityManager, $authView, $session);
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
