<?php
namespace App\Control;

use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\AllenatoreRepositoryInterface;
use App\View\Interface\VisualizzazioneUtentiView;
use App\Foundation\Session;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Amministratore;
use App\Entity\Allenatore;
use App\Entity\Palestra;

class VisualizzazioneUtentiController 
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ClienteRepositoryInterface $clienteRepo,
        private AllenatoreRepositoryInterface $allenatoreRepo,
        private VisualizzazioneUtentiView $view,
        private Session $session
    ) {}

    public function visualizzaClienti(): void 
    {
        $idUtente = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUtente || !$ruolo) {
            $this->view->mostraErrore("Sessione non valida. Effettua il login.");
            return;
        }

        // Verifica permessi ed estrazione della palestra
        $palestra = null;
        if ($ruolo === 'amministratore') {
            $admin = $this->entityManager->find(Amministratore::class, $idUtente);
            if ($admin) {
                $palestra = $this->entityManager->getRepository(Palestra::class)->findOneBy(['amministratore' => $admin]);
            }
        } elseif ($ruolo === 'allenatore') {
            $trainer = $this->entityManager->find(Allenatore::class, $idUtente);
            if ($trainer) {
                $palestra = $trainer->getPalestra();
            }
        } else {
            $this->view->mostraErrore("Accesso negato. Questa area è riservata ad Amministratori ed Allenatori.");
            return;
        }

        if (!$palestra) {
            $this->view->mostraErrore("Palestra associata non trovata.");
            return;
        }

        // Recupero dei clienti filtrati per palestra, ricerca ed eventuale stato del certificato medico
        $query = $_POST['search_query'] ?? $_GET['search_query'] ?? null;
        $filtroCertificato = $_POST['filtro_certificato'] ?? $_GET['filtro_certificato'] ?? null;

        $clienti = $this->clienteRepo->findByPalestraAndFiltri($palestra, $query, $filtroCertificato);

        $clientiData = [];
        foreach ($clienti as $c) {
            $clientiData[] = [
                'id' => $c->getId(),
                'nome' => $c->getNome(),
                'cognome' => $c->getCognome(),
                'email' => $c->getEmail(),
                'cf' => $c->getCF()
            ];
        }

        $this->view->mostraListaClienti($clientiData);
    }

    public function visualizzaAllenatori(): void 
    {
        $idUtente = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUtente || !$ruolo) {
            $this->view->mostraErrore("Sessione non valida. Effettua il login.");
            return;
        }

        if ($ruolo !== 'amministratore') {
            $this->view->mostraErrore("Accesso riservato all'Amministratore.");
            return;
        }

        $admin = $this->entityManager->find(Amministratore::class, $idUtente);
        if (!$admin) {
            $this->view->mostraErrore("Amministratore non trovato.");
            return;
        }

        $palestra = $this->entityManager->getRepository(Palestra::class)->findOneBy(['amministratore' => $admin]);
        if (!$palestra) {
            $this->view->mostraErrore("Palestra associata non trovata.");
            return;
        }

        $allenatori = $this->allenatoreRepo->findByPalestra($palestra);
        $allenatoriData = [];
        foreach ($allenatori as $a) {
            $allenatoriData[] = [
                'id' => $a->getId(),
                'nome' => $a->getNome(),
                'cognome' => $a->getCognome(),
                'email' => $a->getEmail(),
                'cf' => $a->getCF()
            ];
        }

        $this->view->mostraListaAllenatori($allenatoriData);
    }
}