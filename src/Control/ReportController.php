<?php
namespace App\Control;

use App\Entity\Repository\PalestraRepositoryInterface;
use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\AttivitaPianificataRepositoryInterface;
use App\Entity\Repository\UtenteRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrinePalestraRepository;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineAttivitaPianificataRepository;
use App\Foundation\Persistence\Repository\DoctrineUtenteRepository;
use App\View\Interface\ReportView;
use App\View\ReportViewSmarty;
use App\Foundation\Session;
use Doctrine\ORM\EntityManagerInterface;

class ReportController
{
    private EntityManagerInterface $entityManager;
    private PalestraRepositoryInterface $palestraRepo;
    private ClienteRepositoryInterface $clienteRepo;
    private AttivitaPianificataRepositoryInterface $attivitaPianificataRepo;
    private UtenteRepositoryInterface $utenteRepo;
    private ReportView $view;

    public function __construct(
        EntityManagerInterface $entityManager,
        private Session $session
    ) {
        $this->entityManager = $entityManager;
        $this->palestraRepo = new DoctrinePalestraRepository($this->entityManager);
        $this->clienteRepo = new DoctrineClienteRepository($this->entityManager);
        $this->attivitaPianificataRepo = new DoctrineAttivitaPianificataRepository($this->entityManager);
        $this->utenteRepo = new DoctrineUtenteRepository($this->entityManager);
        $this->view = new ReportViewSmarty();
    }

    /**
     * Visualizza i report grafici dell'amministratore (dati 100% reali dal DB, filtrati per mese ed anno)
     */
    public function visualizzaReport(): void
    {
        $idUtente = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();

        if (!$idUtente || $ruolo !== 'amministratore') {
            $this->view->mostraErrore("Accesso negato. Solo l'amministratore può visualizzare i report.");
            return;
        }

        // Recupera la palestra dell'amministratore loggato
        $admin = $this->utenteRepo->findById($idUtente);
        $palestra = $this->palestraRepo->findByAmministratore($admin);

        if (!$palestra) {
            $this->view->mostraErrore("Nessuna palestra associata a questo amministratore.");
            return;
        }

        // Filtri temporali (default: mese e anno correnti)
        $meseSelezionato = isset($_GET['mese']) ? (int)$_GET['mese'] : (int)date('m');
        $annoSelezionato = isset($_GET['anno']) ? (int)$_GET['anno'] : (int)date('Y');

        // Calcola intervalli temporali del mese scelto
        $primoDelMese = new \DateTimeImmutable("$annoSelezionato-$meseSelezionato-01 00:00:00");
        $fineDelMese = $primoDelMese->modify('last day of this month 23:59:59');
        $numeroGiorniMese = (int)$fineDelMese->format('d');

        // Carica tutti i clienti della palestra
        $clienti = $this->clienteRepo->findByPalestra($palestra);

        // 1. Tipologia Abbonamento (Iniziati nel mese/anno selezionato)
        $abbonamentiDati = [];
        
        foreach ($clienti as $cliente) {
            $abb = $cliente->getAbbonamento();
            if ($abb) {
                $start = $abb->getDataInizio();
                if ((int)$start->format('n') === $meseSelezionato && (int)$start->format('Y') === $annoSelezionato) {
                    $tipoObj = $abb->getAbbonamento();
                    if ($tipoObj) {
                        $tipologiaName = $tipoObj->getTipologia();
                        $abbonamentiDati[$tipologiaName] = ($abbonamentiDati[$tipologiaName] ?? 0) + 1;
                    }
                }
            }
        }

        // 2. Numero prenotazioni alle attività pianificate (Nel mese/anno selezionato)
        $tutteAp = $this->attivitaPianificataRepo->findAll();
        $prenotazioniCorsi = [];

        foreach ($tutteAp as $ap) {
            if ($ap->getSala() && $ap->getSala()->getPalestra() && $ap->getSala()->getPalestra()->getId() === $palestra->getId()) {
                $giorno = $ap->getGiorno();
                if ((int)$giorno->format('Y') === $annoSelezionato && (int)$giorno->format('n') === $meseSelezionato) {
                    $nomeAttivita = $ap->getAttivita()->getNome();
                    $prenotazioniCorsi[$nomeAttivita] = ($prenotazioniCorsi[$nomeAttivita] ?? 0) + $ap->getPrenotati();
                }
            }
        }
        // Ordina per prenotazioni decrescenti
        arsort($prenotazioniCorsi);
        $prenotazioniCorsi = array_slice($prenotazioniCorsi, 0, 5);

        // 3. Numero iscritti giornalieri nel mese selezionato (1 a N giorni)
        $iscrittiGiornalieri = array_fill(1, $numeroGiorniMese, 0);
        foreach ($clienti as $cliente) {
            $iscrizione = $cliente->getIscrizione();
            if ($iscrizione) {
                $dataIni = $iscrizione->getDataInizio();
                if ((int)$dataIni->format('Y') === $annoSelezionato && (int)$dataIni->format('n') === $meseSelezionato) {
                    $g = (int)$dataIni->format('d');
                    $iscrittiGiornalieri[$g]++;
                }
            }
        }

        $mesiNomi = [
            1 => 'Gen', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mag', 6 => 'Giu',
            7 => 'Lug', 8 => 'Ago', 9 => 'Set', 10 => 'Ott', 11 => 'Nov', 12 => 'Dic'
        ];

        $datiView = [
            'utente' => $admin,
            'meseSelezionato' => $meseSelezionato,
            'annoSelezionato' => $annoSelezionato,
            'mesiNomi' => $mesiNomi,
            'abbonamentiDati' => $abbonamentiDati,
            'prenotazioniCorsi' => $prenotazioniCorsi,
            'iscrittiGiornalieri' => $iscrittiGiornalieri,
            'giorniMese' => range(1, $numeroGiorniMese),
            'anniDisponibili' => [2025, 2026, 2027]
        ];

        $this->view->mostraReport($datiView);
    }
}
