<?php
namespace App\Control;

use App\View\Interface\AutenticazioneView;
use App\Foundation\Session;
use Doctrine\ORM\EntityManagerInterface;

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

        $admin = $this->entityManager->find(\App\Entity\Amministratore::class, $id);
        $clienti = $this->entityManager->getRepository(\App\Entity\Cliente::class)->findAll();
        $allenatori = $this->entityManager->getRepository(\App\Entity\Allenatore::class)->findAll();

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

        $allenatore = $this->entityManager->find(\App\Entity\Allenatore::class, $id);
        $clienti = $this->entityManager->getRepository(\App\Entity\Cliente::class)->findAll();
        $esercizi = $this->entityManager->getRepository(\App\Entity\Esercizio::class)->findAll();

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

        $cliente = $this->entityManager->find(\App\Entity\Cliente::class, $id);

        $this->view->mostraDashboardCliente([
            'utente' => $cliente
        ]);
    }
}
