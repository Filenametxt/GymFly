<?php
namespace App\Control;

use App\Entity\Repository\AttivitaPianificataRepositoryInterface;
use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\SessionePrivataRepositoryInterface;
use App\Entity\Repository\SalaRepositoryInterface;
use App\Entity\Repository\AttivitaRepositoryInterface;
use App\Entity\Repository\AllenatoreRepositoryInterface;
use App\Entity\Repository\CodaAttesaRepositoryInterface;
use App\Entity\Repository\PalestraRepositoryInterface;
use App\Entity\Repository\MessaggioRepositoryInterface;
use App\Entity\Repository\AmministratoreRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrineAttivitaPianificataRepository;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineSessionePrivataRepository;
use App\Foundation\Persistence\Repository\DoctrineSalaRepository;
use App\Foundation\Persistence\Repository\DoctrineAttivitaRepository;
use App\Foundation\Persistence\Repository\DoctrineAllenatoreRepository;
use App\Foundation\Persistence\Repository\DoctrineCodaAttesaRepository;
use App\Foundation\Persistence\Repository\DoctrinePalestraRepository;
use App\Foundation\Persistence\Repository\DoctrineMessaggioRepository;
use App\Foundation\Persistence\Repository\DoctrineAmministratoreRepository;
use App\Foundation\Persistence\Type\DateTimeImmutableStringable;
use App\Entity\AttivitaPianificata;
use App\Entity\SessionePrivata;
use App\Entity\Cliente;
use App\Entity\Allenatore;
use App\Entity\Amministratore;
use App\Entity\Palestra;
use App\Entity\Attivita;
use App\Entity\Sala;
use App\Entity\CodaAttesa;
use App\Entity\Messaggio;
use App\View\Interface\AttivitaPianificataView;
use App\View\AttivitaPianificataViewSmarty;
use App\Foundation\Session;
use Doctrine\ORM\EntityManagerInterface;

class AttivitaPianificataController
{
    private AttivitaPianificataRepositoryInterface $attivitaPianificataRepo;
    private ClienteRepositoryInterface $clienteRepo;
    private SessionePrivataRepositoryInterface $sessionePrivataRepo;
    private SalaRepositoryInterface $salaRepo;
    private AttivitaRepositoryInterface $attivitaRepo;
    private AllenatoreRepositoryInterface $allenatoreRepo;
    private CodaAttesaRepositoryInterface $codaAttesaRepo;
    private PalestraRepositoryInterface $palestraRepo;
    private MessaggioRepositoryInterface $messaggioRepo;
    private AmministratoreRepositoryInterface $amministratoreRepo;
    private AttivitaPianificataView $view;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Session $session
    ) {
        $this->attivitaPianificataRepo = new DoctrineAttivitaPianificataRepository($this->entityManager);
        $this->clienteRepo = new DoctrineClienteRepository($this->entityManager);
        $this->sessionePrivataRepo = new DoctrineSessionePrivataRepository($this->entityManager);
        $this->salaRepo = new DoctrineSalaRepository($this->entityManager);
        $this->attivitaRepo = new DoctrineAttivitaRepository($this->entityManager);
        $this->allenatoreRepo = new DoctrineAllenatoreRepository($this->entityManager);
        $this->codaAttesaRepo = new DoctrineCodaAttesaRepository($this->entityManager);
        $this->palestraRepo = new DoctrinePalestraRepository($this->entityManager);
        $this->messaggioRepo = new DoctrineMessaggioRepository($this->entityManager);
        $this->amministratoreRepo = new DoctrineAmministratoreRepository($this->entityManager);
        $this->view = new AttivitaPianificataViewSmarty();
    }

    // =========================================================================
    // 1. VISUALIZZA CALENDARIO (/calendario)
    // =========================================================================

    public function visualizzaCalendario(): void
    {
        $palestra = $this->recuperaPalestraUtente();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Nessuna palestra associata all'utente.");
            return;
        }
        $ruolo = $this->session->getLoggedUserRole();
        $idUt = $this->session->getLoggedUserId();
        [$lun, $giorni, $dateStr] = $this->calcolaSettimana($_GET['data'] ?? 'today');
        $apList = $this->caricaAttivitaPianificate($palestra, $ruolo, $idUt);
        $spList = $this->caricaSessioniPrivate($ruolo, $idUt, $dateStr);
        $selAp = $this->recuperaApSelezionata($palestra, $ruolo, $idUt);
        $datiView = [
            'grid' => $this->costruisciGriglia($apList, $spList, $dateStr),
            'fasceOrarie' => range(8, 20), 'giorniSettimana' => $giorni,
            'dataPrecedente' => $lun->modify('-7 days')->format('Y-m-d'),
            'dataSuccessiva' => $lun->modify('+7 days')->format('Y-m-d'),
            'dataCorrente' => $lun->format('Y-m-d'),
            'meseAnno' => $this->costruisciMeseAnno($lun), 'ruolo_utente' => $ruolo,
            'sale' => $this->salaRepo->findByPalestra($palestra), 'allenatori' => $this->allenatoreRepo->findByPalestra($palestra),
            'attivita' => $this->attivitaRepo->findAll(), 'clienti' => $this->clienteRepo->findByPalestra($palestra),
            'selectedAp' => $selAp, 'codaAttesa' => $selAp ? $this->codaAttesaRepo->findByAttivitaPianificata($selAp) : [],
            'selectedSp' => $this->recuperaSpSelezionata($ruolo, $idUt), 'nuovo' => isset($_GET['nuovo']) ? 1 : 0, 'nuova_sessione' => isset($_GET['nuova_sessione']) ? 1 : 0
        ];
        if ($ruolo === 'cliente') {
            $this->arricchisciDatiCliente($idUt, $apList, $datiView);
        }
        $this->view->mostraCalendario($datiView);
    }

    private function calcolaSettimana(string $dataStr): array
    {
        try {
            $oggi = new \DateTimeImmutable($dataStr);
        } catch (\Throwable $e) {
            $oggi = new \DateTimeImmutable('today');
        }
        $giornoSettimanaCorrente = (int)$oggi->format('N');
        $lunedi = $oggi->modify('-' . ($giornoSettimanaCorrente - 1) . ' days');
        $giorniSettimana = [];
        $dateSettimanaStr = [];
        for ($i = 0; $i < 7; $i++) {
            $d = $lunedi->modify('+' . $i . ' days');
            $giorniSettimana[] = $d;
            $dateSettimanaStr[] = $d->format('Y-m-d');
        }
        return [$lunedi, $giorniSettimana, $dateSettimanaStr];
    }

    private function costruisciMeseAnno(\DateTimeImmutable $lunedi): string
    {
        $mesi = [
            1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
            5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
            9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
        ];
        return $mesi[(int)$lunedi->format('n')] . ' ' . $lunedi->format('Y');
    }

    private function caricaAttivitaPianificate(Palestra $palestra, string $ruolo, int $idUtente): array
    {
        if ($ruolo === 'allenatore') {
            $allenatore = $this->allenatoreRepo->findById($idUtente);
            return $allenatore ? $this->attivitaPianificataRepo->findByAllenatore($allenatore) : [];
        }
        return array_filter($this->attivitaPianificataRepo->findAll(), function(AttivitaPianificata $ap) use ($palestra) {
            return $ap->getSala()->getPalestra()->getId() === $palestra->getId();
        });
    }

    private function caricaSessioniPrivate(string $ruolo, int $idUtente, array $dateSettimanaStr): array
    {
        $sessioniPrivateSettimana = [];
        $privateRaw = [];
        if ($ruolo === 'cliente') {
            $cliente = $this->clienteRepo->findById($idUtente);
            $privateRaw = $cliente ? $this->sessionePrivataRepo->findByCliente($cliente) : [];
        } elseif ($ruolo === 'allenatore') {
            $allenatore = $this->allenatoreRepo->findById($idUtente);
            $privateRaw = $allenatore ? $this->sessionePrivataRepo->findByAllenatore($allenatore) : [];
        }
        foreach ($privateRaw as $sp) {
            if (in_array($sp->getData()->format('Y-m-d'), $dateSettimanaStr)) {
                $sessioniPrivateSettimana[] = $sp;
            }
        }
        return $sessioniPrivateSettimana;
    }

    private function costruisciGriglia(array $apList, array $spList, array $dateSettimanaStr): array
    {
        $grid = [];
        foreach (range(8, 20) as $ora) {
            $grid[$ora] = array_fill(1, 7, []);
        }
        foreach ($apList as $ap) {
            $dayIndex = array_search($ap->getGiorno()->format('Y-m-d'), $dateSettimanaStr);
            if ($dayIndex !== false && isset($grid[$ap->getOrario()])) {
                $grid[$ap->getOrario()][$dayIndex + 1][] = $ap;
            }
        }
        foreach ($spList as $sp) {
            $dayIndex = array_search($sp->getData()->format('Y-m-d'), $dateSettimanaStr);
            $ora = (int)$sp->getOraInizio()->format('H');
            if ($dayIndex !== false && isset($grid[$ora])) {
                $grid[$ora][$dayIndex + 1][] = $sp;
            }
        }
        return $grid;
    }

    private function recuperaApSelezionata(Palestra $palestra, string $ruolo, int $idUtente): ?AttivitaPianificata
    {
        $idAp = isset($_GET['id_ap']) ? (int)$_GET['id_ap'] : 0;
        if ($idAp <= 0) return null;
        $selectedAp = $this->attivitaPianificataRepo->findById($idAp);
        if ($selectedAp) {
            $belongsToPalestra = $selectedAp->getSala()->getPalestra()->getId() === $palestra->getId();
            $isTrainerCourse = ($ruolo === 'allenatore') ? ($selectedAp->getAllenatore()->getId() === $idUtente) : true;
            if ($belongsToPalestra && $isTrainerCourse) {
                return $selectedAp;
            }
        }
        return null;
    }

    private function recuperaSpSelezionata(string $ruolo, int $idUtente): ?SessionePrivata
    {
        $selAllenatoreId = isset($_GET['sel_allenatore']) ? (int)$_GET['sel_allenatore'] : 0;
        $selOraInizio = isset($_GET['sel_ora_inizio']) ? trim($_GET['sel_ora_inizio']) : '';
        $selOraFine = isset($_GET['sel_ora_fine']) ? trim($_GET['sel_ora_fine']) : '';
        if ($selAllenatoreId <= 0 || $selOraInizio === '' || $selOraFine === '') {
            return null;
        }
        $selAllenatore = $this->allenatoreRepo->findById($selAllenatoreId);
        if (!$selAllenatore) return null;
        try {
            $sp = $this->sessionePrivataRepo->findByChiave($selAllenatore, new DateTimeImmutableStringable($selOraInizio), new DateTimeImmutableStringable($selOraFine));
            if ($sp) {
                $isAuth = ($ruolo === 'allenatore' && $idUtente === $sp->getAllenatore()->getId()) || ($ruolo === 'cliente' && $idUtente === $sp->getAtleta()->getId());
                return $isAuth ? $sp : null;
            }
        } catch (\Throwable $e) {}
        return null;
    }

    private function arricchisciDatiCliente(int $idUtente, array $apList, array &$datiView): void
    {
        $cliente = $this->clienteRepo->findById($idUtente);
        if (!$cliente) return;
        $datiView['cliente'] = $cliente;
        $iscrittoMap = [];
        $inQueueMap = [];
        foreach ($apList as $ap) {
            $iscrittoMap[$ap->getId()] = $this->clienteRepo->isIscrittoAAttivita($cliente, $ap);
            $inQueueMap[$ap->getId()] = $this->codaAttesaRepo->existsInCoda($cliente, $ap);
        }
        $datiView['iscrittoMap'] = $iscrittoMap;
        $datiView['inQueueMap'] = $inQueueMap;
        $datiView['puoPrenotare'] = $cliente->puoPrenotareAttivita();
    }

    // =========================================================================
    // 2. PRENOTA ATTIVITÀ (/prenota-attivita)
    // =========================================================================

    public function prenotaAttivita(): void
    {
        $palestra = $this->recuperaPalestraUtente();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }
        $idAp = (int)($_REQUEST['id_attivita_pianificata'] ?? 0);
        $ap = $this->attivitaPianificataRepo->findById($idAp);
        if (!$ap || $ap->getSala()->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Attività non trovata.", "calendario", "Torna al Calendario");
            return;
        }
        $rit = "calendario?data=" . $ap->getGiorno()->format('Y-m-d');
        $cliente = $this->recuperaClientePrenotazione($palestra, $rit);
        if ($cliente) {
            $this->eseguiPrenotazione($cliente, $ap, $rit);
        }
    }

    private function recuperaClientePrenotazione(Palestra $palestra, string $ritorno): ?Cliente
    {
        $ruolo = $this->session->getLoggedUserRole();
        $id = ($ruolo === 'cliente') ? $this->session->getLoggedUserId() : (int)($_POST['id_cliente'] ?? 0);
        $cliente = $this->clienteRepo->findById($id);
        if (!$cliente || $cliente->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Cliente non valido.", $ritorno, "Torna al Calendario");
            return null;
        }
        return $cliente;
    }

    private function eseguiPrenotazione(Cliente $cliente, AttivitaPianificata $ap, string $ritorno): void
    {
        if ($this->clienteRepo->isIscrittoAAttivita($cliente, $ap)) {
            $this->view->mostraStatoOperazione(false, "Il cliente risulta già iscritto.", $ritorno, "Torna al Calendario");
            return;
        }
        if (!$cliente->puoPrenotareAttivita()) {
            $this->view->mostraStatoOperazione(false, "Il cliente deve avere abbonamento attivo e certificato valido.", $ritorno, "Torna al Calendario");
            return;
        }
        if ($ap->getPrenotati() >= $ap->getMaxPartecipanti()) {
            $this->gestisciCodaAttesa($cliente, $ap, $ritorno);
            return;
        }
        try {
            $cliente->iscriviAAttivita($ap);
            $ap->setPrenotati($ap->getPrenotati() + 1);
            $this->clienteRepo->save($cliente);
            $this->view->mostraStatoOperazione(true, "Iscrizione registrata con successo.", $ritorno, "Torna al Calendario");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore: " . $e->getMessage(), $ritorno, "Torna al Calendario");
        }
    }

    private function gestisciCodaAttesa(Cliente $cliente, AttivitaPianificata $ap, string $ritorno): void
    {
        if ($this->codaAttesaRepo->findOneByClienteAndAttivita($cliente, $ap)) {
            $this->view->mostraStatoOperazione(false, "Sei già inserito nella coda di attesa.", $ritorno, "Torna al Calendario");
            return;
        }
        try {
            $this->codaAttesaRepo->save(new CodaAttesa($cliente, $ap));
            $this->view->mostraStatoOperazione(true, "Capienza massima raggiunta. Inserito nella coda di attesa.", $ritorno, "Torna al Calendario");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Impossibile inserire nella coda: " . $e->getMessage(), $ritorno, "Torna al Calendario");
        }
    }

    // =========================================================================
    // 3. DISDICI PRENOTAZIONE (/disdici-prenotazione)
    // =========================================================================

    public function disdiciPrenotazione(): void
    {
        $palestra = $this->recuperaPalestraUtente();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }
        $idAp = (int)($_REQUEST['id_attivita_pianificata'] ?? 0);
        $ap = $this->attivitaPianificataRepo->findById($idAp);
        if (!$ap || $ap->getSala()->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Attività non trovata.", "calendario", "Torna al Calendario");
            return;
        }
        $rit = "calendario?data=" . $ap->getGiorno()->format('Y-m-d');
        $cliente = $this->recuperaClientePrenotazione($palestra, $rit);
        if ($cliente) {
            $this->eseguiDisdetta($cliente, $ap, $rit);
        }
    }

    private function eseguiDisdetta(Cliente $cliente, AttivitaPianificata $ap, string $ritorno): void
    {
        $inQueue = $this->codaAttesaRepo->findOneByClienteAndAttivita($cliente, $ap);
        if (!$this->clienteRepo->isIscrittoAAttivita($cliente, $ap)) {
            if ($inQueue) {
                $this->rimuoviDaCoda($inQueue, $ritorno);
            } else {
                $this->view->mostraStatoOperazione(false, "Il cliente non risulta iscritto o in coda.", $ritorno, "Torna al Calendario");
            }
            return;
        }
        try {
            $cliente->cancellaIscrizioneAttivita($ap);
            $ap->setPrenotati(max(0, $ap->getPrenotati() - 1));
            $this->clienteRepo->save($cliente);
            $this->scorriCodaEnotifica($ap);
            $this->view->mostraStatoOperazione(true, "Iscrizione cancellata con successo.", $ritorno, "Torna al Calendario");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore disdetta: " . $e->getMessage(), $ritorno, "Torna al Calendario");
        }
    }

    private function rimuoviDaCoda(CodaAttesa $inQueue, string $ritorno): void
    {
        try {
            $this->codaAttesaRepo->delete($inQueue);
            $this->view->mostraStatoOperazione(true, "Sei stato rimosso dalla coda di attesa.", $ritorno, "Torna al Calendario");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Impossibile rimuovere dalla coda: " . $e->getMessage(), $ritorno, "Torna al Calendario");
        }
    }

    private function scorriCodaEnotifica(AttivitaPianificata $ap): void
    {
        $codaPrimo = $this->codaAttesaRepo->findPrimoInCoda($ap);
        if ($codaPrimo) {
            $clienteScelto = $codaPrimo->getCliente();
            $clienteScelto->iscriviAAttivita($ap);
            $ap->setPrenotati($ap->getPrenotati() + 1);
            $this->codaAttesaRepo->delete($codaPrimo);
            $this->clienteRepo->save($clienteScelto);

            $oggettoMsg = "Iscrizione automatica all'attività";
            $contenutoMsg = "Ciao " . $clienteScelto->getNome() . ",\n\nti informiamo che si è liberato un posto e sei stato iscritto automaticamente all'attività: " . $ap->getAttivita()->getNome() . " in data " . $ap->getGiorno()->format('d/m/Y') . " alle ore " . $ap->getOrario() . ":00.\n\nSaluti,\nLo staff di GymFly";
            $messaggio = new Messaggio($ap->getAllenatore(), $oggettoMsg, $contenutoMsg);
            $messaggio->aggiungiDestinatario($clienteScelto);
            $this->messaggioRepo->save($messaggio);

            $headers = "From: no-reply@gymfly.com\r\nReply-To: support@gymfly.com\r\nContent-Type: text/plain; charset=utf-8";
            @mail($clienteScelto->getEmail(), $oggettoMsg, $contenutoMsg, $headers);
        }
    }

    // =========================================================================
    // 4. PRENOTA SESSIONE PRIVATA (/prenota-sessione-privata)
    // =========================================================================

    public function prenotaSessionePrivata(): void
    {
        $palestra = $this->recuperaPalestraUtente();
        if (!$palestra || $this->session->getLoggedUserRole() !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }
        $idAllenatore = $this->session->getLoggedUserId();
        $allenatore = $this->allenatoreRepo->findById($idAllenatore);
        if (!$allenatore) {
            $this->view->mostraStatoOperazione(false, "Allenatore non trovato.", "calendario", "Torna al Calendario");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormPrenotaSessionePrivata(['clienti' => $this->clienteRepo->findByPalestra($palestra)]);
            return;
        }
        $this->eseguiPrenotazioneSp($allenatore, $palestra);
    }

    private function eseguiPrenotazioneSp(Allenatore $all, Palestra $pal): void
    {
        $idCliente = (int)($_POST['id_cliente'] ?? 0);
        $dataStr = trim($_POST['data'] ?? '');
        $rit = ($dataStr !== '') ? "calendario?data=" . $dataStr : "calendario";
        $oraIn = trim($_POST['ora_inizio'] ?? '');
        $oraFi = trim($_POST['ora_fine'] ?? '');

        if ($idCliente <= 0 || $dataStr === '' || $oraIn === '' || $oraFi === '') {
            $this->view->mostraStatoOperazione(false, "Campi obbligatori mancanti.", $rit, "Torna al Calendario");
            return;
        }
        $cliente = $this->clienteRepo->findById($idCliente);
        if (!$cliente || $cliente->getPalestra()->getId() !== $pal->getId()) {
            $this->view->mostraStatoOperazione(false, "Cliente non valido.", $rit, "Torna al Calendario");
            return;
        }
        $this->salvaSpEInviaMessaggio($cliente, $all, $dataStr, $oraIn, $oraFi, $rit);
    }

    private function salvaSpEInviaMessaggio(Cliente $cli, Allenatore $all, string $dataStr, string $oraIn, string $oraFi, string $rit): void
    {
        try {
            $dataObj = new \DateTimeImmutable($dataStr);
            $oraInObj = new DateTimeImmutableStringable($dataStr . ' ' . $oraIn);
            $oraFiObj = new DateTimeImmutableStringable($dataStr . ' ' . $oraFi);
            if ($oraInObj >= $oraFiObj) {
                $this->view->mostraStatoOperazione(false, "L'ora di inizio deve precedere la fine.", $rit, "Torna al Calendario");
                return;
            }
            if ($this->sessionePrivataRepo->existsSovrapposizioneAllenatore($all, $dataObj, $oraInObj, $oraFiObj) || $this->sessionePrivataRepo->existsSovrapposizioneCliente($cli, $dataObj, $oraInObj, $oraFiObj)) {
                $this->view->mostraStatoOperazione(false, "Sovrapposizione di impegni rilevata.", $rit, "Torna al Calendario");
                return;
            }
            $this->sessionePrivataRepo->save(new SessionePrivata($dataObj, $oraInObj, $oraFiObj, $cli, $all));
            $msg = new Messaggio($all, "Nuova Sessione Privata", "Sessione privata pianificata per il giorno " . $dataObj->format('d/m/Y') . " dalle " . $oraInObj->format('H:i') . " alle " . $oraFiObj->format('H:i') . ".");
            $msg->aggiungiDestinatario($cli);
            $this->messaggioRepo->save($msg);
            $this->view->mostraStatoOperazione(true, "Sessione privata pianificata con successo.", $rit, "Torna al Calendario");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore: " . $e->getMessage(), $rit, "Torna al Calendario");
        }
    }

    // =========================================================================
    // 5. CREA ATTIVITÀ PIANIFICATA (/crea-attivita-pianificata)
    // =========================================================================

    public function creaAttivitaPianificata(): void
    {
        $palestra = $this->recuperaPalestraUtente();
        if (!$palestra || $this->session->getLoggedUserRole() !== 'amministratore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Solo l'amministratore può pianificare corsi.");
            return;
        }
        $dataStr = trim($_POST['data'] ?? '');
        $orario = (int)($_POST['orario'] ?? 0);
        $idAllenatore = (int)($_POST['id_allenatore'] ?? 0);
        $ritorno = ($dataStr !== '') ? "calendario?data=" . $dataStr : "calendario";
        if ($dataStr === '' || $orario < 8 || $orario > 20 || $idAllenatore <= 0) {
            $this->view->mostraStatoOperazione(false, "Dati incompleti o orario non valido (8-20).", $ritorno, "Torna al Calendario");
            return;
        }
        $attivita = $this->ottieniOCreaAttivita((int)($_POST['id_attivita'] ?? 0), $ritorno);
        $sala = $attivita ? $this->ottieniOCreaSala((int)($_POST['id_sala'] ?? 0), $palestra, $ritorno) : null;
        $allenatore = $sala ? $this->allenatoreRepo->findById($idAllenatore) : null;
        if (!$allenatore || $sala->getPalestra()->getId() !== $palestra->getId() || $allenatore->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Allenatore non trovato o risorse esterne alla palestra.", $ritorno, "Torna al Calendario");
            return;
        }
        $this->salvaPianificazioni(new \DateTime($dataStr), $_POST['ripetizione'] ?? [], $orario, $sala, $allenatore, $attivita, $ritorno);
    }

    private function ottieniOCreaAttivita(int $idAttivita, string $ritorno): ?Attivita
    {
        if ($idAttivita <= 0) {
            $nome = trim($_POST['nuova_attivita_nome'] ?? '');
            $desc = trim($_POST['nuova_attivita_desc'] ?? '');
            $max = (int)($_POST['nuova_attivita_max'] ?? 0);
            if ($nome === '' || $max <= 0) {
                $this->view->mostraStatoOperazione(false, "Nome e limite partecipanti validi obbligatori.", $ritorno, "Torna al Calendario");
                return null;
            }
            if ($this->attivitaRepo->existsByNome($nome)) {
                $this->view->mostraStatoOperazione(false, "Attività già esistente nel catalogo.", $ritorno, "Torna al Calendario");
                return null;
            }
            $attivita = new Attivita($nome, $desc, $max);
            $this->attivitaRepo->save($attivita);
            return $attivita;
        }
        return $this->attivitaRepo->findById($idAttivita);
    }

    private function ottieniOCreaSala(int $idSala, Palestra $palestra, string $ritorno): ?Sala
    {
        if ($idSala <= 0) {
            $nome = trim($_POST['nuova_sala_nome'] ?? '');
            $max = (int)($_POST['nuova_sala_max'] ?? 0);
            if ($nome === '' || $max <= 0) {
                $this->view->mostraStatoOperazione(false, "Nome e capienza massima validi obbligatori.", $ritorno, "Torna al Calendario");
                return null;
            }
            if ($this->salaRepo->existsByNomeAndPalestra($nome, $palestra)) {
                $this->view->mostraStatoOperazione(false, "La sala esiste già nella tua palestra.", $ritorno, "Torna al Calendario");
                return null;
            }
            $sala = new Sala($nome, $max, $palestra);
            $this->salaRepo->save($sala);
            return $sala;
        }
        return $this->salaRepo->findById($idSala);
    }

    private function salvaPianificazioni(\DateTime $startDate, array $rip, int $orario, Sala $sala, Allenatore $all, Attivita $att, string $rit): void
    {
        try {
            if (!empty($rip)) {
                for ($i = 0; $i < 28; $i++) {
                    $current = (clone $startDate)->modify("+$i days");
                    if (in_array((string)$current->format('N'), $rip)) {
                        $giornoImm = \DateTimeImmutable::createFromMutable($current);
                        if (!$this->attivitaPianificataRepo->findOneByGiornoOrarioAndSala($giornoImm, $orario, $sala)) {
                            $this->attivitaPianificataRepo->save(new AttivitaPianificata($giornoImm, $orario, $sala, $all, $att));
                        }
                    }
                }
            } else {
                $giornoImm = \DateTimeImmutable::createFromMutable($startDate);
                if ($this->attivitaPianificataRepo->findOneByGiornoOrarioAndSala($giornoImm, $orario, $sala)) {
                    $this->view->mostraStatoOperazione(false, "Sala occupata.", $rit, "Torna al Calendario");
                    return;
                }
                $this->attivitaPianificataRepo->save(new AttivitaPianificata($giornoImm, $orario, $sala, $all, $att));
            }
            $this->view->mostraStatoOperazione(true, "Corso pianificato con successo.", $rit, "Torna al Calendario");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore: " . $e->getMessage(), $rit, "Torna al Calendario");
        }
    }

    // =========================================================================
    // 6. RIMUOVI ATTIVITÀ PIANIFICATA (/rimuovi-attivita-pianificata)
    // =========================================================================

    public function rimuoviAttivitaPianificata(): void
    {
        $palestra = $this->recuperaPalestraUtente();
        if (!$palestra || $this->session->getLoggedUserRole() !== 'amministratore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }
        $id = (int)($_REQUEST['id_attivita_pianificata'] ?? 0);
        $ap = $this->attivitaPianificataRepo->findById($id);
        if (!$ap || $ap->getSala()->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Attività non trovata o accesso negato.", "calendario", "Torna al Calendario");
            return;
        }
        $this->eseguiRimozioneAp($ap);
    }

    private function eseguiRimozioneAp(AttivitaPianificata $ap): void
    {
        try {
            $rit = "calendario?data=" . $ap->getGiorno()->format('Y-m-d');
            foreach ($ap->getUtenti() as $cliente) {
                $cliente->cancellaIscrizioneAttivita($ap);
                $this->clienteRepo->save($cliente);
            }
            $this->attivitaPianificataRepo->delete($ap);
            $this->view->mostraStatoOperazione(true, "Attività pianificata rimossa.", $rit, "Torna al Calendario");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore: " . $e->getMessage(), "calendario", "Torna al Calendario");
        }
    }

    // =========================================================================
    // 7. DISDICI SESSIONE PRIVATA (/disdici-sessione-privata)
    // =========================================================================

    public function disdiciSessionePrivata(): void
    {
        $palestra = $this->recuperaPalestraUtente();
        if (!$palestra || !in_array($this->session->getLoggedUserRole(), ['cliente', 'allenatore'])) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }
        $idAllenatore = (int)($_REQUEST['id_allenatore'] ?? 0);
        $oraInStr = trim($_REQUEST['ora_inizio'] ?? '');
        $oraFiStr = trim($_REQUEST['ora_fine'] ?? '');
        if ($idAllenatore <= 0 || $oraInStr === '' || $oraFiStr === '') {
            $this->view->mostraStatoOperazione(false, "Dati identificativi della sessione non validi.", "calendario", "Torna al Calendario");
            return;
        }
        $allenatore = $this->allenatoreRepo->findById($idAllenatore);
        if (!$allenatore) {
            $this->view->mostraStatoOperazione(false, "Allenatore non trovato.", "calendario", "Torna al Calendario");
            return;
        }
        $this->eseguiDisdettaSp($allenatore, $oraInStr, $oraFiStr);
    }

    private function eseguiDisdettaSp(Allenatore $all, string $oraIn, string $oraFi): void
    {
        try {
            $sessione = $this->sessionePrivataRepo->findByChiave($all, new DateTimeImmutableStringable($oraIn), new DateTimeImmutableStringable($oraFi));
            if (!$sessione) {
                $this->view->mostraStatoOperazione(false, "Sessione non trovata.", "calendario", "Torna al Calendario");
                return;
            }
            $rit = "calendario?data=" . $sessione->getData()->format('Y-m-d');
            $idUt = $this->session->getLoggedUserId();
            $ruolo = $this->session->getLoggedUserRole();
            if (($ruolo === 'allenatore' && $idUt !== $sessione->getAllenatore()->getId()) || ($ruolo === 'cliente' && $idUt !== $sessione->getAtleta()->getId())) {
                $this->view->mostraStatoOperazione(false, "Non sei autorizzato a disdire la sessione.", $rit, "Torna al Calendario");
                return;
            }
            $this->inviaNotificaAnnullamentoSp($sessione, $ruolo);
            $this->sessionePrivataRepo->delete($sessione);
            $this->view->mostraStatoOperazione(true, "Sessione privata annullata con successo.", $rit, "Torna al Calendario");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore: " . $e->getMessage(), "calendario", "Torna al Calendario");
        }
    }

    private function inviaNotificaAnnullamentoSp(SessionePrivata $sessione, string $ruolo): void
    {
        if ($ruolo === 'allenatore') {
            $cli = $sessione->getAtleta();
            $msg = new Messaggio(
                $sessione->getAllenatore(),
                "Sessione Privata Annullata",
                "Ciao " . $cli->getNome() . ", ho annullato la sessione privata del giorno " . $sessione->getData()->format('d/m/Y') . " dalle ore " . $sessione->getOraInizio()->format('H:i') . " alle " . $sessione->getOraFine()->format('H:i') . "."
            );
            $msg->aggiungiDestinatario($cli);
            $this->messaggioRepo->save($msg);
        }
    }

    // =========================================================================
    // HELPER GENERALI
    // =========================================================================

    private function recuperaPalestraUtente(): ?Palestra
    {
        $idUtente = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUtente || !$ruolo) {
            return null;
        }
        if ($ruolo === 'amministratore') {
            $admin = $this->amministratoreRepo->findById($idUtente);
            return $admin ? $this->palestraRepo->findByAmministratore($admin) : null;
        } elseif ($ruolo === 'allenatore') {
            $allenatore = $this->allenatoreRepo->findById($idUtente);
            return $allenatore ? $allenatore->getPalestra() : null;
        } elseif ($ruolo === 'cliente') {
            $cliente = $this->clienteRepo->findById($idUtente);
            return $cliente ? $cliente->getPalestra() : null;
        }
        return null;
    }
}
