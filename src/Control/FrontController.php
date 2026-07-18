<?php
namespace App\Control;

use App\Infrastructure\Doctrine\EntityManagerFactory;
use App\Foundation\Session;

class FrontController
{
    private array $routes = [];         //gli URL, ognuno dei quali è mappato da classe e metodo

    public function __construct()       //definisce il path delle rotte
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
            '/aggiorna-abilitazioni-profilo' => [ProfiloController::class, 'aggiornaAbilitazioniAllenatore'],
            
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

            '/rimuovi-cliente' => [AmministratoreController::class, 'rimuoviCliente'],
            '/rimuovi-allenatore' => [AmministratoreController::class, 'rimuoviAllenatore'],
            
            '/crea-esercizio' => [EserciziController::class, 'apriFormCreazioneEsercizio'],
            '/valida-esercizio' => [EserciziController::class, 'compilaDatiEsercizio'],
            '/salva-esercizio' => [EserciziController::class, 'salvaEsercizio'],
            '/copia-esercizio' => [EserciziController::class, 'copiaEsercizio'],
            '/esercizi' => [EserciziController::class, 'listaEsercizi'],
            '/visualizza-esercizio' => [EserciziController::class, 'visualizzaEsercizio'],
            
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
            '/invia-scheda' => [SchedaAllenamentoController::class, 'inviaScheda'],
            '/elimina-scheda' => [SchedaAllenamentoController::class, 'eliminaScheda'],
            '/visualizza-scheda' => [SchedaAllenamentoController::class, 'visualizzaScheda'],
            '/modifica-dettagli' => [SchedaAllenamentoController::class, 'apriFormModificaSchedaCliente'],
            '/progressi-cliente' => [SchedaAllenamentoController::class, 'visualizzaProgressiCliente'],
        ];
    }

    /**
     * Avvia il Front Controller, risolve la rotta richiesta ed esegue l'azione associata.
     */
    public function run(): void
    {
        $route = $this->resolveRoute();

        if (!isset($this->routes[$route])) {                //se la rotta non esiste, allora 404 Not Found
            header("HTTP/1.0 404 Not Found");
            echo "<h1>404 Not Found</h1><p>La pagina richiesta non esiste.</p>";
            return;
        }

        list($controllerClass, $method) = $this->routes[$route];

        // Avvia la sessione ed istanzia l'EntityManager
        $session = new Session();
        $entityManager = EntityManagerFactory::create();

        // Istanziazione ed esecuzione dinamica del Controller
        $controller = new $controllerClass($entityManager, $session);           //viene richiamato il costruttore della classe $controllerClass
        $controller->$method();
    }

    /**
     * Risolve il percorso URL per estrarre la rotta pulita tramite query parameter passato da .htaccess.
     */
    private function resolveRoute(): string    //risolve il percorso URL per estrarre la rotta pulita
    {
        $route = $_GET['route'] ?? '/';
        return '/' . ltrim($route, '/');
    }
}
