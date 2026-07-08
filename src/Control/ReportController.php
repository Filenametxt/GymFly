<?php
namespace App\Control;

use App\Entity\Amministratore;
use App\Entity\Palestra;
use App\Entity\Cliente;
use App\Entity\AttivitaPianificata;
use App\View\Interface\ReportView;
use App\Infrastructure\Doctrine\EntityManagerFactory;
use App\Foundation\Session;
use Doctrine\ORM\EntityManager;

class ReportController
{
    private EntityManager $entityManager;

    public function __construct(
        private ReportView $view,
        private Session $session
    ) {
        $this->entityManager = EntityManagerFactory::create();
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
        $admin = $this->entityManager->find(Amministratore::class, $idUtente);
        $palestra = $this->entityManager->getRepository(Palestra::class)->findOneBy(['amministratore' => $admin]);

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
        $clienti = $this->entityManager->getRepository(Cliente::class)->findBy(['palestra' => $palestra]);

        // 1. Tipologia Abbonamento (Attivi nel mese/anno selezionato)
        $abbonamentiDati = [];
        
        foreach ($clienti as $cliente) {
            $abb = $cliente->getAbbonamento();
            if ($abb) {
                $start = $abb->getDataInizio();
                $end = $abb->getDataFine();
                // Verifica sovrapposizione temporale
                if ($start <= $fineDelMese && ($end === null || $end >= $primoDelMese)) {
                    $tipoObj = $abb->getAbbonamento();
                    if ($tipoObj) {
                        $tipologiaName = $tipoObj->getTipologia();
                        $abbonamentiDati[$tipologiaName] = ($abbonamentiDati[$tipologiaName] ?? 0) + 1;
                    }
                }
            }
        }

        // 2. Numero prenotazioni alle attività pianificate (Nel mese/anno selezionato)
        $tutteAp = $this->entityManager->getRepository(AttivitaPianificata::class)->findAll();
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
