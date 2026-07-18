<?php
namespace App\Control;

use App\View\Interface\VisualizzazioneView;
use App\View\VisualizzazioneViewSmarty;
use App\Foundation\Persistence\Repository\DoctrineParametriRepository;
use App\Foundation\Session;
use App\Entity\Repository\PalestraRepositoryInterface;
use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\AllenatoreRepositoryInterface;
use App\Entity\Repository\UtenteRepositoryInterface;
use App\Entity\Repository\AttivitaRepositoryInterface;
use App\Entity\Repository\EsercizioRepositoryInterface;
use App\Entity\Repository\MessaggioRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrinePalestraRepository;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineAllenatoreRepository;
use App\Foundation\Persistence\Repository\DoctrineUtenteRepository;
use App\Foundation\Persistence\Repository\DoctrineAttivitaRepository;
use App\Foundation\Persistence\Repository\DoctrineEsercizioRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Cliente;
use App\Entity\Allenatore;
use App\Foundation\Persistence\Repository\DoctrineMessaggioRepository;
use App\Foundation\Persistence\Repository\DoctrineAttivitaPianificataRepository;
use DateTimeImmutable;

class VisualizzazioneController
{
    private VisualizzazioneView $view;
    private PalestraRepositoryInterface $palestraRepo;
    private ClienteRepositoryInterface $clienteRepo;
    private AllenatoreRepositoryInterface $allenatoreRepo;
    private UtenteRepositoryInterface $utenteRepo;
    private AttivitaRepositoryInterface $attivitaRepo;
    private EsercizioRepositoryInterface $esercizioRepo;
    private MessaggioRepositoryInterface $messaggioRepo;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Session $session
    ) {
        $this->palestraRepo = new DoctrinePalestraRepository($this->entityManager);
        $this->clienteRepo = new DoctrineClienteRepository($this->entityManager);
        $this->allenatoreRepo = new DoctrineAllenatoreRepository($this->entityManager);
        $this->utenteRepo = new DoctrineUtenteRepository($this->entityManager);
        $this->attivitaRepo = new DoctrineAttivitaRepository($this->entityManager);
        $this->esercizioRepo = new DoctrineEsercizioRepository($this->entityManager);
        $this->messaggioRepo = new DoctrineMessaggioRepository($this->entityManager);
        $this->view = new VisualizzazioneViewSmarty();
    }

    // =========================================================================
    // 1. MOSTRA HOME (/)
    // =========================================================================

    public function mostraHome(): void    //gestisce la richiesta di visualizzazione della home page
    {
        $this->view->mostraHome();
    }

    // =========================================================================
    // 2. MOSTRA DASHBOARD ADMIN (/dashboard-admin)
    // =========================================================================

    public function mostraDashboardAdmin(): void     //gestisce la richiesta di visualizzazione della dashboard dell'amministratore, verificando i permessi dell'utente loggato e calcolando i dati da mostrare nella dashboard
    {
        $id = $this->session->getLoggedUserId();
        $admin = ($id && $this->session->getLoggedUserRole() === 'amministratore') ? $this->utenteRepo->findById($id) : null;
        if (!$admin) {
            $this->session->logout();
            header("Location: login");
            exit;
        }
        $pal = $this->palestraRepo->findByAmministratore($admin);
        $clienti = $pal ? $this->clienteRepo->findByPalestra($pal) : [];
        [$scaduti, $inScadenza, $validi] = $this->calcolaSemaforoCertificati($clienti, 30);

        $this->view->mostraDashboardAdmin([
            'utente' => $admin, 'clienti' => $clienti, 'allenatori' => $pal ? $this->allenatoreRepo->findByPalestra($pal) : [],
            'certificati_scaduti' => $scaduti, 'certificati_in_scadenza' => $inScadenza, 'certificati_validi' => $validi,
            'registrazioni' => array_values($this->caricaRegistrazioniMese($clienti)),
            'ultimi_messaggi' => array_slice($this->messaggioRepo->findByMittente($admin), 0, 4),
            'eventi_oggi' => $this->caricaEventiOggi(), 'attivita' => $this->attivitaRepo->findAll()
        ]);
    }

    private function calcolaSemaforoCertificati(array $clienti, int $giorniScadenza): array
    {
        $scaduti = 0; $inScadenza = 0; $validi = 0;
        foreach ($clienti as $c) {
            $cert = $c->getCertificatoMedico();
            if (!$cert || $cert->giorniAllaScadenza() < 0) {
                $scaduti++;
            } elseif ($cert->giorniAllaScadenza() <= $giorniScadenza) {
                $inScadenza++;
            } else {
                $validi++;
            }
        }
        return [$scaduti, $inScadenza, $validi];
    }

    private function caricaRegistrazioniMese(array $clienti): array
    {
        $dati = [];
        $oggi = new DateTimeImmutable();
        $nomi = ['Jan' => 'Gen', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr', 'May' => 'Mag', 'Jun' => 'Giu', 'Jul' => 'Lug', 'Aug' => 'Ago', 'Sep' => 'Set', 'Oct' => 'Ott', 'Nov' => 'Nov', 'Dec' => 'Dic'];
        for ($i = 7; $i >= 0; $i--) {
            $d = $oggi->modify("-$i month");
            $dati[$d->format('Y-m')] = ['data' => $nomi[$d->format('M')] ?? $d->format('M'), 'valore' => 0];
        }
        foreach ($clienti as $c) {
            $isc = $c->getIscrizione();
            if ($isc && isset($dati[$key = $isc->getDataInizio()->format('Y-m')])) {
                $dati[$key]['valore']++;
            }
        }
        return $dati;
    }



    private function caricaEventiOggi(): array
    {
        $repo = new DoctrineAttivitaPianificataRepository($this->entityManager);
        $eventi = [];
        foreach ($repo->findByGiorno(new DateTimeImmutable()) as $ap) {
            $in = str_pad($ap->getOrario(), 2, '0', STR_PAD_LEFT) . ':00';
            $fi = str_pad($ap->getOrario() + 1, 2, '0', STR_PAD_LEFT) . ':00';
            $eventi[] = [
                'nome' => $ap->getAttivita()->getNome(), 'orario' => "$in - $fi", 'colore' => '#3273dc',
                'allenatore' => $ap->getAllenatore()->getNome() . ' ' . $ap->getAllenatore()->getCognome()
            ];
        }
        return $eventi;
    }

    // =========================================================================
    // 3. MOSTRA DASHBOARD ALLENATORI (/dashboard-allenatore)
    // =========================================================================

    public function mostraDashboardAllenatore(): void
    {
        $id = $this->session->getLoggedUserId();
        $all = ($id && $this->session->getLoggedUserRole() === 'allenatore') ? $this->entityManager->find(Allenatore::class, $id) : null;
        if (!$all) {
            $this->session->logout();
            header("Location: login");
            exit;
        }
        $clienti = $all->getPalestra() ? $this->clienteRepo->findByPalestra($all->getPalestra()) : [];
        [$scadute, $richieste, $inRegola] = $this->calcolaStatisticheSchede($clienti);

        $this->view->mostraDashboardAllenatore([
            'utente' => $all, 'clienti' => $clienti, 'esercizi' => $this->esercizioRepo->findAll(),
            'schede_scadute' => $scadute, 'richieste_scheda' => $richieste, 'schede_in_regola' => $inRegola,
            'ultimi_messaggi' => array_slice($this->messaggioRepo->findByMittente($all), 0, 5), 'eventi_oggi' => $this->caricaEventiOggi()
        ]);
    }

    private function calcolaStatisticheSchede(array $clienti): array
    {
        $scadute = 0; $richieste = 0; $inRegola = 0;
        $oggi = new DateTimeImmutable();
        foreach ($clienti as $c) {
            $scheda = $c->getScheda();
            if (!$scheda) {
                $richieste++;
            } elseif ($scheda->getData_fine() < $oggi) {
                $scadute++;
            } else {
                $inRegola++;
            }
        }
        return [$scadute, $richieste, $inRegola];
    }

    // =========================================================================
    // 4. MOSTRA DASHBOARD CLIENTI (/dashboard-cliente)
    // =========================================================================

    public function mostraDashboardCliente(): void
    {
        $id = $this->session->getLoggedUserId();
        $cli = ($id && $this->session->getLoggedUserRole() === 'cliente') ? $this->entityManager->find(Cliente::class, $id) : null;
        if (!$cli) {
            $this->session->logout();
            header("Location: login");
            exit;
        }
        $parametriRepo = new DoctrineParametriRepository($this->entityManager);
        $attivitaOggi = [];
        $oggiStr = (new DateTimeImmutable('today'))->format('Y-m-d');
        foreach ($cli->getAttivitaPianificate() as $ap) {
            if ($ap->getGiorno()->format('Y-m-d') === $oggiStr) {
                $attivitaOggi[] = $ap;
            }
        }
        $this->view->mostraDashboardCliente([
            'utente' => $cli, 'ultimaMisure' => $parametriRepo->findUltimaByCliente($cli), 'attivitaOggi' => $attivitaOggi
        ]);
    }

    // =========================================================================
    // 5. MOSTRA ERRORE D'APPLICAZIONE
    // =========================================================================

    public function mostraErrore(): void
    {
        $msg = $_GET['msg'] ?? 'Si è verificato un errore imprevisto.';
        $success = isset($_GET['success']) && $_GET['success'] == 1;
        $ritorno = 'login';
        if ($this->session->isLogged()) {
            $ruolo = $this->session->getLoggedUserRole();
            $mappa = ['amministratore' => 'dashboard-admin', 'allenatore' => 'dashboard-allenatore', 'cliente' => 'dashboard-cliente'];
            $ritorno = $mappa[$ruolo] ?? 'login';
        }
        $this->view->mostraStatoOperazione($success, $msg, $ritorno);
    }
}
