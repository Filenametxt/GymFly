<?php
namespace App\Control;

use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\AllenatoreRepositoryInterface;
use App\View\Interface\VisualizzazioneUtentiView;
use App\Foundation\Session;

class VisualizzazioneUtentiController 
{
    public function __construct(
        private ClienteRepositoryInterface $clienteRepo,
        private AllenatoreRepositoryInterface $allenatoreRepo,
        private VisualizzazioneUtentiView $view,
        private Session $session
    ) {}

    public function visualizzaClienti(): void 
    {
        $idUtente = $this->session->getLoggedUserId();
        if (!$idUtente) {
            $this->view->mostraErrore("Sessione non valida. Effettua il login.");
            return;
        }
        $utenteLoggato = $this->clienteRepo->findById($idUtente); // Assumendo che anche admin/allenatore siano trovabili qui
        $ruolo = $utenteLoggato ? $utenteLoggato->getRuolo() : null;

        if ($ruolo !== 'ADMIN' && $ruolo !== 'ALLENATORE') {
            $this->view->mostraErrore("Permessi insufficienti.");
            return;
        }

        $query = $_POST['search_query'] ?? null;
        $clienti = $query ? $this->clienteRepo->findByStringa($query) : $this->clienteRepo->findAll();

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
        if (!$idUtente) {
            $this->view->mostraErrore("Sessione non valida. Effettua il login.");
            return;
        }
        $utenteLoggato = $this->clienteRepo->findById($idUtente);
        $ruolo = $utenteLoggato ? $utenteLoggato->getRuolo() : null;

        if ($ruolo !== 'ADMIN') {
            $this->view->mostraErrore("Accesso riservato all'Amministratore.");
            return;
        }

        $allenatori = $this->allenatoreRepo->findAll();
        $allenatoriData = [];
        foreach ($allenatori as $a) {
            $allenatoriData[] = [
                'id' => $a->getId(),
                'nome' => $a->getNome(),
                'cognome' => $a->getCognome(),
                'email' => $a->getEmail()
            ];
        }

        $this->view->mostraListaAllenatori($allenatoriData);
    }
}