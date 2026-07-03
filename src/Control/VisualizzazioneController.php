<?php
namespace App\Control;

use App\View\Interface\AutenticazioneView;
use App\Foundation\Session;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Amministratore;
use App\Entity\Cliente;
use App\Entity\Allenatore;
use App\Entity\Esercizio;
use App\Entity\Palestra;

class VisualizzazioneController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AutenticazioneView $view,
        private Session $session
    ) {}

    /**
     * Mostra la dashboora vorrei fare in modo che dopo il login a seconda che l'utente loggato sia un Amministratore, Cliente o
  Allenatore devono essere registrati alle rispettive pagine, inoltre deve esserci una verifica: se Cliente prova ad accedere alla pagina di amministrazione il server deve rigettarloard dell'amministratore se autorizzato.
     */
    public function mostraDashboardAdmin(): void
    {
        $id = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$id || $ruolo !== 'amministratore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Questa area è riservata all'amministratore.");
            return;
        }

        $admin = $this->entityManager->find(Amministratore::class, $id);
        
        // Recupera la palestra gestita da questo amministratore
        $palestra = $this->entityManager->getRepository(Palestra::class)->findOneBy(['amministratore' => $admin]);
        
        if ($palestra) {
            $clienti = $this->entityManager->getRepository(Cliente::class)->findBy(['palestra' => $palestra]);
            $allenatori = $this->entityManager->getRepository(Allenatore::class)->findBy(['palestra' => $palestra]);
        } else {
            $clienti = [];
            $allenatori = [];
        }

        $this->view->mostraDashboardAdmin([
            'utente' => $admin,
            'clienti' => $clienti,
            'allenatori' => $allenatori
        ]);
    }

    /**
     * Mostra la dashboard dell'allenatore se autorizzato.
     */
    public function mostraDashboardAllenatore(): void
    {
        $id = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$id || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Questa area è riservata agli allenatori.");
            return;
        }

        $allenatore = $this->entityManager->find(Allenatore::class, $id);
        $palestra = $allenatore->getPalestra();
        
        // Filtra i clienti associati alla palestra dell'allenatore
        $clienti = $palestra 
            ? $this->entityManager->getRepository(Cliente::class)->findBy(['palestra' => $palestra])
            : [];
            
        $esercizi = $this->entityManager->getRepository(Esercizio::class)->findAll();

        $this->view->mostraDashboardAllenatore([
            'utente' => $allenatore,
            'clienti' => $clienti,
            'esercizi' => $esercizi
        ]);
    }

    /**
     * Mostra la dashboard del cliente se autorizzato.
     */
    public function mostraDashboardCliente(): void
    {
        $id = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$id || $ruolo !== 'cliente') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Questa area è riservata ai clienti.");
            return;
        }

        $cliente = $this->entityManager->find(Cliente::class, $id);

        $this->view->mostraDashboardCliente([
            'utente' => $cliente
        ]);
    }

    /**
     * Mostra la home dell'applicazione, reindirizzando alla dashboard se già loggati.
     */
    public function mostraHome(): void
    {
        if ($this->session->isLogged()) {
            $this->view->reindirizzaDopoLogin($this->session->getLoggedUserRole());
            return;
        }
        header('Content-Type: text/html; charset=utf-8');
        echo "<h1>Benvenuto in GymFly!</h1><p>Seleziona un'azione.</p>";
        echo '<p><a href="login">Accedi al servizio</a> o <a href="registrazione">Registra la tua Palestra</a></p>';
    }
}
