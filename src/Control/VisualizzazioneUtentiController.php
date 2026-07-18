<?php
namespace App\Control;

use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\AllenatoreRepositoryInterface;
use App\Entity\Repository\PalestraRepositoryInterface;
use App\Entity\Repository\AmministratoreRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineAllenatoreRepository;
use App\Foundation\Persistence\Repository\DoctrinePalestraRepository;
use App\Foundation\Persistence\Repository\DoctrineAmministratoreRepository;
use App\View\Interface\VisualizzazioneUtentiView;
use App\View\VisualizzazioneUtentiViewSmarty;
use App\Foundation\Session;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Amministratore;
use App\Entity\Palestra;
use App\Entity\Repository\UtenteRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrineUtenteRepository;
use App\Control\AttivitaPianificataController;

class VisualizzazioneUtentiController 
{
    private ClienteRepositoryInterface $clienteRepo;
    private AllenatoreRepositoryInterface $allenatoreRepo;
    private PalestraRepositoryInterface $palestraRepo;
    private UtenteRepositoryInterface $utenteRepo;
    private AmministratoreRepositoryInterface $amministratoreRepo;
    private VisualizzazioneUtentiView $view;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Session $session
    ) {
        $this->clienteRepo = new DoctrineClienteRepository($this->entityManager);
        $this->allenatoreRepo = new DoctrineAllenatoreRepository($this->entityManager);
        $this->palestraRepo = new DoctrinePalestraRepository($this->entityManager);
        $this->utenteRepo = new DoctrineUtenteRepository($this->entityManager);
        $this->amministratoreRepo = new DoctrineAmministratoreRepository($this->entityManager);
        $this->view = new VisualizzazioneUtentiViewSmarty();
    }

    // =========================================================================
    // 1. VISUALIZZA CLIENTI (/clienti)
    // =========================================================================

    public function visualizzaClienti(): void     //gestisce la richiesta di visualizzazione dei clienti, verificando i permessi dell'utente loggato e calcolando i dati da mostrare nella lista dei clienti
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUt || !$ruolo) {
            $this->view->mostraErrore("Sessione non valida. Effettua il login.");
            return;
        }
        $pal = $this->recuperaPalestraUtenti();
        if (!$pal) {
            $this->view->mostraErrore("Accesso negato o palestra non trovata.");
            return;
        }
        $clienti = $this->clienteRepo->findByPalestra($pal);
        $clienti = $this->applicaFiltriClienti($clienti, $_POST['search_query'] ?? $_GET['search_query'] ?? null, $_POST['filtro_certificato'] ?? $_GET['filtro_certificato'] ?? null, $_POST['filtro_abbonamento'] ?? $_GET['filtro_abbonamento'] ?? null, $_POST['filtro_scheda'] ?? $_GET['filtro_scheda'] ?? null);
        $clienti = $this->ordinaClienti($clienti, $_POST['ordine'] ?? $_GET['ordine'] ?? null);
        $this->view->mostraListaClienti($this->mappaClientiPerView($clienti));
    }

    // =========================================================================
    // 2. VISUALIZZA ALLENATORI (/allenatori)
    // =========================================================================

    public function visualizzaAllenatori(): void 
    {
        $idUt = $this->session->getLoggedUserId();
        $admin = ($idUt && $this->session->getLoggedUserRole() === 'amministratore') ? $this->amministratoreRepo->findById($idUt) : null;
        $pal = $admin ? $this->palestraRepo->findByAmministratore($admin) : null;
        if (!$admin || !$pal) {
            $this->view->mostraErrore("Accesso riservato all'Amministratore o palestra non trovata.");
            return;
        }
        $allenatori = $this->allenatoreRepo->findByPalestra($pal);
        $this->view->mostraListaAllenatori($this->mappaAllenatoriPerView($allenatori));
    }

    // =========================================================================
    // FILTRI ED HELPER DI ORDINAMENTO E MAPPATURA
    // =========================================================================

    private function applicaFiltriClienti(array $clienti, ?string $q, ?string $fCert, ?string $fAbb, ?string $fSch): array   // Applica i filtri di ricerca e di stato ai clienti
    {
        $clienti = $this->applicaFiltroRicerca($clienti, $q);     
        $clienti = $this->applicaFiltroCertificato($clienti, $fCert);
        $clienti = $this->applicaFiltroAbbonamento($clienti, $fAbb);
        $clienti = $this->applicaFiltroScheda($clienti, $fSch);
        return $clienti;
    }

    private function applicaFiltroRicerca(array $clienti, ?string $q): array
    {
        if ($q === null || trim($q) === '') return $clienti;
        $search = strtolower(trim($q));
        return array_filter($clienti, fn($c) => str_contains(strtolower($c->getNome()), $search) || str_contains(strtolower($c->getCognome()), $search));
    }

    private function applicaFiltroCertificato(array $clienti, ?string $f): array
    {
        if ($f === 'scaduti') {
            return array_filter($clienti, fn($c) => $c->getCertificatoMedico() === null || $c->getCertificatoMedico()->giorniAllaScadenza() < 0);
        } elseif ($f === 'in_scadenza') {
            return array_filter($clienti, fn($c) => $c->getCertificatoMedico() !== null && $c->getCertificatoMedico()->giorniAllaScadenza() >= 0 && $c->getCertificatoMedico()->giorniAllaScadenza() <= 30);
        } elseif ($f === 'in_regola') {
            return array_filter($clienti, fn($c) => $c->getCertificatoMedico() !== null && $c->getCertificatoMedico()->giorniAllaScadenza() > 30);
        }
        return $clienti;
    }

    private function applicaFiltroAbbonamento(array $clienti, ?string $f): array
    {
        if ($f === 'attivo') {
            return array_filter($clienti, fn($c) => $c->isAbbonamentoAttivo());
        } elseif ($f === 'scaduto') {
            return array_filter($clienti, fn($c) => !$c->isAbbonamentoAttivo());
        }
        return $clienti;
    }

    private function applicaFiltroScheda(array $clienti, ?string $f): array
    {
        if ($f === null || trim($f) === '') return $clienti;
        $oggi = new \DateTimeImmutable('today');
        return array_filter($clienti, function($c) use ($f, $oggi) {
            $s = $c->getScheda();
            if ($f === 'scadute') return $s !== null && $s->getData_fine() < $oggi;
            if ($f === 'assenti' || $f === 'richieste') return $s === null;
            if ($f === 'in_regola') return $s !== null && $s->getData_fine() >= $oggi;
            return true;
        });
    }

    private function ordinaClienti(array $clienti, ?string $ordine): array
    {
        usort($clienti, function($a, $b) use ($ordine) {
            switch ($ordine) {       // Applica l'ordinamento in base al parametro $ordine
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

    private function recuperaPalestraUtenti(): ?Palestra
    {
        return AttivitaPianificataController::recuperaPalestraUtenteStatic(
            $this->session,
            $this->utenteRepo,
            $this->palestraRepo,
            $this->clienteRepo
        );
    }

    private function mappaClientiPerView(array $clienti): array    //MAPPING CLIENTI PER LA VIEW
    {
        $data = [];
        foreach ($clienti as $c) {
            $data[] = [
                'id' => $c->getId(), 'nome' => $c->getNome(), 'cognome' => $c->getCognome(), 'email' => $c->getEmail(),
                'cf' => $c->getCF(), 'fotoProfilo' => $c->getProfilePicture() ? base64_encode($c->getProfilePicture()) : null
            ];
        }
        return $data;
    }

    private function mappaAllenatoriPerView(array $allenatori): array
    {
        $data = [];
        foreach ($allenatori as $a) {
            $nomi = array_map(fn($att) => $att->getNome(), $a->getAttivitaAbilitate()->toArray());
            $data[] = [
                'id' => $a->getId(), 'nome' => $a->getNome(), 'cognome' => $a->getCognome(), 'email' => $a->getEmail(),
                'cf' => $a->getCF(), 'sesso' => $a->getSesso()->value, 'attivita' => implode(',', $nomi),
                'fotoProfilo' => $a->getProfilePicture() ? base64_encode($a->getProfilePicture()) : null
            ];
        }
        return $data;
    }
}