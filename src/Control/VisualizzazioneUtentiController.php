<?php
namespace App\Control;

use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\AllenatoreRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineAllenatoreRepository;
use App\View\Interface\VisualizzazioneUtentiView;
use App\View\VisualizzazioneUtentiViewSmarty;
use App\Foundation\Session;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Amministratore;
use App\Entity\Allenatore;
use App\Entity\Palestra;

class VisualizzazioneUtentiController 
{
    private ClienteRepositoryInterface $clienteRepo;
    private AllenatoreRepositoryInterface $allenatoreRepo;
    private VisualizzazioneUtentiView $view;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Session $session
    ) {
        $this->clienteRepo = new DoctrineClienteRepository($this->entityManager);
        $this->allenatoreRepo = new DoctrineAllenatoreRepository($this->entityManager);
        $this->view = new VisualizzazioneUtentiViewSmarty();
    }

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

        // Recupero dei clienti filtrati per palestra, ricerca, certificato medico, abbonamento e ordinamento
        $query = $_POST['search_query'] ?? $_GET['search_query'] ?? null;
        $filtroCertificato = $_POST['filtro_certificato'] ?? $_GET['filtro_certificato'] ?? null;
        $filtroAbbonamento = $_POST['filtro_abbonamento'] ?? $_GET['filtro_abbonamento'] ?? null;
        $ordine = $_POST['ordine'] ?? $_GET['ordine'] ?? null;

        $clienti = $this->clienteRepo->findByPalestraAndFiltri($palestra, $query, $filtroCertificato, $filtroAbbonamento, $ordine);

        // Filtro per stato scheda a livello applicativo (in-memory) per non modificare il repository o il DB
        $filtroScheda = $_POST['filtro_scheda'] ?? $_GET['filtro_scheda'] ?? null;
        if ($filtroScheda !== null && trim($filtroScheda) !== '') {
            $oggi = new \DateTimeImmutable('today');
            $clienti = array_filter($clienti, function($c) use ($filtroScheda, $oggi) {
                $scheda = $c->getScheda();
                if ($filtroScheda === 'scadute') {
                    return $scheda !== null && $scheda->getData_fine() < $oggi;
                } elseif ($filtroScheda === 'richieste') {
                    return $scheda === null;
                } elseif ($filtroScheda === 'in_regola') {
                    return $scheda !== null && $scheda->getData_fine() >= $oggi;
                }
                return true;
            });
        }

        $clientiData = [];
        foreach ($clienti as $c) {
            $clientiData[] = [
                'id' => $c->getId(),
                'nome' => $c->getNome(),
                'cognome' => $c->getCognome(),
                'email' => $c->getEmail(),
                'cf' => $c->getCF(),
                'fotoProfilo' => $c->getProfilePicture() ? base64_encode($c->getProfilePicture()) : null
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
            $attivitaNomi = [];
            foreach ($a->getAttivitaAbilitate() as $att) {
                $attivitaNomi[] = $att->getNome();
            }
            $allenatoriData[] = [
                'id' => $a->getId(),
                'nome' => $a->getNome(),
                'cognome' => $a->getCognome(),
                'email' => $a->getEmail(),
                'cf' => $a->getCF(),
                'sesso' => $a->getSesso()->value,
                'attivita' => implode(',', $attivitaNomi),
                'fotoProfilo' => $a->getProfilePicture() ? base64_encode($a->getProfilePicture()) : null
            ];
        }

        $this->view->mostraListaAllenatori($allenatoriData);
    }
}