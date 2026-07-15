<?php
namespace App\Control;

use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\AllenatoreRepositoryInterface;
use App\Entity\Repository\PalestraRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineAllenatoreRepository;
use App\Foundation\Persistence\Repository\DoctrinePalestraRepository;
use App\View\Interface\VisualizzazioneUtentiView;
use App\View\VisualizzazioneUtentiViewSmarty;
use App\Foundation\Session;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Amministratore;
use App\Entity\Allenatore;

class VisualizzazioneUtentiController 
{
    private ClienteRepositoryInterface $clienteRepo;
    private AllenatoreRepositoryInterface $allenatoreRepo;
    private PalestraRepositoryInterface $palestraRepo;
    private VisualizzazioneUtentiView $view;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Session $session
    ) {
        $this->clienteRepo = new DoctrineClienteRepository($this->entityManager);
        $this->allenatoreRepo = new DoctrineAllenatoreRepository($this->entityManager);
        $this->palestraRepo = new DoctrinePalestraRepository($this->entityManager);
        $this->view = new VisualizzazioneUtentiViewSmarty();
    }

    private function applicaFiltriClienti(array $clienti, ?string $query, ?string $filtroCertificato, ?string $filtroAbbonamento, ?string $filtroScheda): array
    {
        if ($query !== null && trim($query) !== '') {
            $search = strtolower(trim($query));
            $clienti = array_filter($clienti, function($c) use ($search) {
                return str_contains(strtolower($c->getNome()), $search) || 
                       str_contains(strtolower($c->getCognome()), $search);
            });
        }

        switch ($filtroCertificato) {
            case 'scaduti':
                $clienti = array_filter($clienti, function($c) {
                    $cm = $c->getCertificatoMedico();
                    return $cm === null || $cm->giorniAllaScadenza() < 0;
                });
                break;
            case 'in_scadenza':
                $clienti = array_filter($clienti, function($c) {
                    $cm = $c->getCertificatoMedico();
                    return $cm !== null && $cm->giorniAllaScadenza() >= 0 && $cm->giorniAllaScadenza() <= 30;
                });
                break;
            case 'in_regola':
                $clienti = array_filter($clienti, function($c) {
                    $cm = $c->getCertificatoMedico();
                    return $cm !== null && $cm->giorniAllaScadenza() > 30;
                });
                break;
        }

        switch ($filtroAbbonamento) {
            case 'attivo':
                $clienti = array_filter($clienti, function($c) {
                    return $c->isAbbonamentoAttivo();
                });
                break;
            case 'scaduto':
                $clienti = array_filter($clienti, function($c) {
                    return !$c->isAbbonamentoAttivo();
                });
                break;
        }

        if ($filtroScheda !== null && trim($filtroScheda) !== '') {
            $oggi = new \DateTimeImmutable('today');
            $clienti = array_filter($clienti, function($c) use ($filtroScheda, $oggi) {
                $scheda = $c->getScheda();
                switch ($filtroScheda) {
                    case 'scadute':
                        return $scheda !== null && $scheda->getData_fine() < $oggi;
                    case 'richieste':
                        return $scheda === null;
                    case 'in_regola':
                        return $scheda !== null && $scheda->getData_fine() >= $oggi;
                    default:
                        return true;
                }
            });
        }

        return $clienti;
    }

    private function ordinaClienti(array $clienti, ?string $ordine): array
    {
        usort($clienti, function($a, $b) use ($ordine) {
            switch ($ordine) {
                case 'cognome_desc':
                    $cmp = strcmp($b->getCognome(), $a->getCognome());
                    return $cmp !== 0 ? $cmp : strcmp($a->getNome(), $b->getNome());
                case 'nome_asc':
                    $cmp = strcmp($a->getNome(), $b->getNome());
                    return $cmp !== 0 ? $cmp : strcmp($a->getCognome(), $b->getCognome());
                case 'nome_desc':
                    $cmp = strcmp($b->getNome(), $a->getNome());
                    return $cmp !== 0 ? $cmp : strcmp($a->getCognome(), $b->getCognome());
                default:
                    $cmp = strcmp($a->getCognome(), $b->getCognome());
                    return $cmp !== 0 ? $cmp : strcmp($a->getNome(), $b->getNome());
            }
        });
        return $clienti;
    }

    public function visualizzaClienti(): void 
    {
        $idUtente = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUtente || !$ruolo) {
            $this->view->mostraErrore("Sessione non valida. Effettua il login.");
            return;
        }

        $palestra = null;
        if ($ruolo === 'amministratore') {
            $admin = $this->entityManager->find(Amministratore::class, $idUtente);
            if ($admin) {
                $palestra = $this->palestraRepo->findByAmministratore($admin);
            }
        } elseif ($ruolo === 'allenatore') {
            $trainer = $this->entityManager->find(Allenatore::class, $idUtente);
            if ($trainer) {
                $palestra = $trainer->getPalestra();
            }
        }

        if (!$palestra) {
            $this->view->mostraErrore("Accesso negato o palestra non trovata.");
            return;
        }

        $query = $_POST['search_query'] ?? $_GET['search_query'] ?? null;
        $filtroCertificato = $_POST['filtro_certificato'] ?? $_GET['filtro_certificato'] ?? null;
        $filtroAbbonamento = $_POST['filtro_abbonamento'] ?? $_GET['filtro_abbonamento'] ?? null;
        $filtroScheda = $_POST['filtro_scheda'] ?? $_GET['filtro_scheda'] ?? null;
        $ordine = $_POST['ordine'] ?? $_GET['ordine'] ?? null;

        $clienti = $this->clienteRepo->findByPalestra($palestra);
        $clienti = $this->applicaFiltriClienti($clienti, $query, $filtroCertificato, $filtroAbbonamento, $filtroScheda);
        $clienti = $this->ordinaClienti($clienti, $ordine);

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

        $palestra = $this->palestraRepo->findByAmministratore($admin);
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