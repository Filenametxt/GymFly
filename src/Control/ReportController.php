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

    // =========================================================================
    // 1. VISUALIZZA REPORT (/report)
    // =========================================================================

    public function visualizzaReport(): void
    {
        $idUt = $this->session->getLoggedUserId();
        $admin = ($idUt && $this->session->getLoggedUserRole() === 'amministratore') ? $this->utenteRepo->findById($idUt) : null;
        $palestra = $admin ? $this->palestraRepo->findByAmministratore($admin) : null;
        if (!$admin || !$palestra) {
            $this->view->mostraErrore("Accesso negato o palestra non associata.");
            return;
        }
        $mese = isset($_GET['mese']) ? (int)$_GET['mese'] : (int)date('m');
        $anno = isset($_GET['anno']) ? (int)$_GET['anno'] : (int)date('Y');
        $giorni = (int)(new \DateTimeImmutable("$anno-$mese-01"))->modify('last day of this month')->format('d');
        $clienti = $this->clienteRepo->findByPalestra($palestra);

        $this->view->mostraReport([
            'utente' => $admin, 'meseSelezionato' => $mese, 'annoSelezionato' => $anno,
            'mesiNomi' => [1 => 'Gen', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mag', 6 => 'Giu', 7 => 'Lug', 8 => 'Ago', 9 => 'Set', 10 => 'Ott', 11 => 'Nov', 12 => 'Dic'],
            'abbonamentiDati' => $this->calcolaAbbonamentiDati($clienti, $mese, $anno),
            'prenotazioniCorsi' => $this->calcolaPrenotazioniCorsi($palestra->getId(), $mese, $anno),
            'iscrittiGiornalieri' => $this->calcolaIscrittiGiornalieri($clienti, $mese, $anno, $giorni),
            'giorniMese' => range(1, $giorni), 'anniDisponibili' => [2025, 2026, 2027]
        ]);
    }

    private function calcolaAbbonamentiDati(array $clienti, int $mese, int $anno): array
    {
        $dati = [];
        foreach ($clienti as $cliente) {
            $abb = $cliente->getAbbonamento();
            if ($abb && (int)$abb->getDataInizio()->format('n') === $mese && (int)$abb->getDataInizio()->format('Y') === $anno) {
                $tipoObj = $abb->getAbbonamento();
                if ($tipoObj) {
                    $name = $tipoObj->getTipologia();
                    $dati[$name] = ($dati[$name] ?? 0) + 1;
                }
            }
        }
        return $dati;
    }

    private function calcolaPrenotazioniCorsi(int $palestraId, int $mese, int $anno): array
    {
        $prenotazioni = [];
        foreach ($this->attivitaPianificataRepo->findAll() as $ap) {
            $sala = $ap->getSala();
            if ($ap->getSala() && $sala->getPalestra()->getId() === $palestraId) {
                $g = $ap->getGiorno();
                if ((int)$g->format('Y') === $anno && (int)$g->format('n') === $mese) {
                    $nome = $ap->getAttivita()->getNome();
                    $prenotazioni[$nome] = ($prenotazioni[$nome] ?? 0) + $ap->getPrenotati();
                }
            }
        }
        arsort($prenotazioni);
        return array_slice($prenotazioni, 0, 5);
    }

    private function calcolaIscrittiGiornalieri(array $clienti, int $mese, int $anno, int $giorni): array
    {
        $iscritti = array_fill(1, $giorni, 0);
        foreach ($clienti as $cliente) {
            $iscrizione = $cliente->getIscrizione();
            if ($iscrizione) {
                $dataIni = $iscrizione->getDataInizio();
                if ((int)$dataIni->format('Y') === $anno && (int)$dataIni->format('n') === $mese) {
                    $iscritti[(int)$dataIni->format('d')]++;
                }
            }
        }
        return $iscritti;
    }
}
