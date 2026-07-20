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
use App\Entity\Repository\UtenteRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrineUtenteRepository;
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
use App\Foundation\Utility\HTTPMethods;
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
    private UtenteRepositoryInterface $utenteRepo;
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
        $this->utenteRepo = new DoctrineUtenteRepository($this->entityManager);
        $this->view = new AttivitaPianificataViewSmarty();
    }

    // =========================================================================
    // 1. VISUALIZZA CALENDARIO (/calendario)
    // =========================================================================

    public function visualizzaCalendario(): void  //recupera la palestra dell'utente loggato, calcola la settimana da visualizzare, carica le attività pianificate e le sessioni private, costruisce la griglia del calendario e mostra la vista
    {
        $palestra = $this->recuperaPalestraUtente();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Nessuna palestra associata all'utente.", "login", "Torna al Login");
            return;
        }
        $ruolo = $this->session->getLoggedUserRole();
        $idUt = $this->session->getLoggedUserId();
        [$lun, $giorni, $dateStr] = $this->calcolaSettimana(HTTPMethods::get('data', 'today'));
        $apList = $this->caricaAttivitaPianificate($palestra, $ruolo, $idUt);
        $spList = $this->caricaSessioniPrivate($ruolo, $idUt, $dateStr);
        $selAp = $this->recuperaApSelezionata($palestra, $ruolo, $idUt);            //QUELLA SELEZIONATA

        $codaCounts = [];
        foreach ($apList as $ap) {
            $codaCounts[$ap->getId()] = $this->codaAttesaRepo->countByAttivitaPianificata($ap);
        }

        $isPassata = false;
        if ($selAp) {
            $dataAttivita = $selAp->getGiorno()->setTime($selAp->getOrario(), 0, 0);
            $isPassata = $dataAttivita < new \DateTimeImmutable();
        }

        $datiView = [
            'grid' => $this->costruisciGriglia($apList, $spList, $dateStr),
            'fasceOrarie' => range(8, 20), 
            'giorniSettimana' => $giorni,           //giorni definiti in calcolaSettimana
            'dataPrecedente' => $lun->modify('-7 days')->format('Y-m-d'),           
            'dataSuccessiva' => $lun->modify('+7 days')->format('Y-m-d'),
            'dataCorrente' => $lun->format('Y-m-d'),
            'meseAnno' => $this->costruisciMeseAnno($lun), 
            'ruolo_utente' => $ruolo,
            'sale' => $this->salaRepo->findByPalestra($palestra), 
            'allenatori' => $this->allenatoreRepo->findByPalestra($palestra),
            'attivita' => $this->attivitaRepo->findAll(), 
            'clienti' => $this->clienteRepo->findByPalestra($palestra),
            'selectedAp' => $selAp, 
            'codaAttesa' => $selAp ? $this->codaAttesaRepo->findByAttivitaPianificata($selAp) : [],   //se c'è un'attività pianificata selezionata, recupera la coda di attesa per quell'attività
            'selectedSp' => $this->recuperaSpSelezionata($ruolo, $idUt), 
            'codaCounts' => $codaCounts,
            'isPassata' => $isPassata,
            'nuovo' => HTTPMethods::get('nuovo') !== null ? 1 : 0,       //se è stato passato il parametro nuovo nella query string, allora mostra il form per creare una nuova attività pianificata
            'nuova_sessione' => HTTPMethods::get('nuova_sessione') !== null ? 1 : 0
        ];
        if ($ruolo === 'cliente') {
            $this->arricchisciDatiCliente($idUt, $apList, $datiView);
        }
        $this->view->mostraCalendario($datiView);
    }

    private function calcolaSettimana(string $dataStr): array   //calcola la settimana da visualizzare a partire dalla data passata come parametro, restituendo il lunedì della settimana, un array di oggetti DateTimeImmutable per ogni giorno della settimana e un array di stringhe delle date in formato 'Y-m-d'
    {
        try {
            $oggi = new \DateTimeImmutable($dataStr);
        } catch (\Throwable $e) {
            $oggi = new \DateTimeImmutable('today');
        }
        $giornoSettimanaCorrente = (int)$oggi->format('N');  // 1 (lunedì) - 7 (domenica)
        $lunedi = $oggi->modify('-' . ($giornoSettimanaCorrente - 1) . ' days');
        $giorniSettimana = [];
        $dateSettimanaStr = [];  // Array di stringhe delle date in formato 'Y-m-d'
        for ($i = 0; $i < 7; $i++) {
            $d = $lunedi->modify('+' . $i . ' days');
            $giorniSettimana[] = $d;
            $dateSettimanaStr[] = $d->format('Y-m-d');   //abbiamo questo perchè stiamo parlando del calendario e non del planner
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
        return $mesi[(int)$lunedi->format('n')] . ' ' . $lunedi->format('Y');         // 05 2001
    }

    private function caricaAttivitaPianificate(Palestra $palestra, string $ruolo, int $idUtente): array
    {
        if ($ruolo === 'allenatore') {
            $allenatore = $this->allenatoreRepo->findById($idUtente);
            return $allenatore ? $this->attivitaPianificataRepo->findByAllenatore($allenatore) : [];
        }
        return array_filter($this->attivitaPianificataRepo->findAll(), function(AttivitaPianificata $ap) use ($palestra) {   //filtra le attività pianificate per la palestra dell'utente loggato
            return $ap->getSala()->getPalestra()->getId() === $palestra->getId();     //restituisce l'array delle attività pianificate che appartengono alla palestra dell'utente loggato
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
            if (in_array($sp->getData()->format('Y-m-d'), $dateSettimanaStr)) {    //cin_array ontrolla se il valore esiste nell'array
                $sessioniPrivateSettimana[] = $sp;      //se la data della sessione privata è presente nell'array delle date della settimana, allora aggiungila all'array delle sessioni private della settimana
            }
        }
        return $sessioniPrivateSettimana;
    }

    private function costruisciGriglia(array $apList, array $spList, array $dateSettimanaStr): array      //serve a costruire la griglia del calendario, con le attività pianificate e le sessioni private, organizzate per ora e giorno della settimana
    {
        $grid = [];
        foreach (range(8, 20) as $ora) {
            $grid[$ora] = array_fill(1, 7, []);      // Inizializza le celle della griglia per ogni ora e giorno della settimana
        }
        foreach ($apList as $ap) {
            $dayIndex = array_search($ap->getGiorno()->format('Y-m-d'), $dateSettimanaStr);     // trova l'indice del giorno della settimana corrispondente alla data dell'attività pianificata
            if ($dayIndex !== false && isset($grid[$ap->getOrario()])) {   // se l'indice del giorno della settimana è valido e l'ora dell'attività pianificata è presente nella griglia, allora aggiungi l'attività pianificata alla griglia
                $grid[$ap->getOrario()][$dayIndex + 1][] = $ap;         // Aggiungi l'attività pianificata alla griglia (dayIndex + 1 perché l'array dei giorni parte da 1, la posizione 0 è riservata all'ora)
                                                                        // le parentesi [] servono a creare un array di attività pianificate per quella cella della griglia, in modo da poter gestire più attività pianificate nello stesso giorno e ora
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
        $idAp = HTTPMethods::get('id_ap') ? (int)HTTPMethods::get('id_ap') : 0;
        if ($idAp <= 0) {
            return null;
        }
        $ap = $this->attivitaPianificataRepo->findById($idAp);
        if ($ap && $ap->getAllenatore()->getPalestra()->getId() === $palestra->getId()) {
            return $ap;
        }
        return null;
    }

    private function recuperaSpSelezionata(string $ruolo, int $idUt): ?SessionePrivata
    {
        $selAllenatoreId = HTTPMethods::get('sel_allenatore') ? (int)HTTPMethods::get('sel_allenatore') : 0;
        $selOraInizio = trim(HTTPMethods::get('sel_ora_inizio', ''));
        $selOraFine = trim(HTTPMethods::get('sel_ora_fine', ''));
        if ($selAllenatoreId <= 0 || $selOraInizio === '' || $selOraFine === '') {
            return null;
        }
        $selAllenatore = $this->allenatoreRepo->findById($selAllenatoreId);
        if (!$selAllenatore) return null;
        try {
            $sp = $this->sessionePrivataRepo->findByChiave($selAllenatore, new DateTimeImmutableStringable($selOraInizio), new DateTimeImmutableStringable($selOraFine));
            if ($sp) {
                $isAuth = ($ruolo === 'allenatore' && $idUt === $sp->getAllenatore()->getId()) || ($ruolo === 'cliente' && $idUt === $sp->getAtleta()->getId());
                return $isAuth ? $sp : null;
            }
        } catch (\Throwable $e) {}
        return null;
    }

    private function arricchisciDatiCliente(int $idUtente, array $apList, array &$datiView): void
    {
        $cliente = $this->clienteRepo->findById($idUtente);
        if (!$cliente) 
            return;
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
            $this->view->mostraStatoOperazione(false, "Accesso negato. Nessuna palestra associata all'utente.", "login", "Torna al Login");
            return;
        }
        $idAp = (int)HTTPMethods::request('id_attivita_pianificata', 0);
        $ap = $this->attivitaPianificataRepo->findById($idAp);
        if (!$ap || $ap->getSala()->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Attività non trovata.", "calendario", "Torna al Calendario");
            return;
        }
        $rit = "calendario?data=" . $ap->getGiorno()->format('Y-m-d');
        
        $dataAttivita = $ap->getGiorno()->setTime($ap->getOrario(), 0, 0);
        if ($dataAttivita < new \DateTimeImmutable()) {
            $this->view->mostraStatoOperazione(false, "Impossibile prenotare o mettersi in coda per un'attività passata.", $rit, "Torna al Calendario");
            return;
        }

        $cliente = $this->recuperaClientePrenotazione($palestra, $rit);
        if ($cliente) {
            $this->eseguiPrenotazione($cliente, $ap, $rit);
        }
    }

    private function recuperaClientePrenotazione(Palestra $palestra, string $ritorno): ?Cliente
    {
        $ruolo = $this->session->getLoggedUserRole();
        $id = ($ruolo === 'cliente') ? $this->session->getLoggedUserId() : (int)HTTPMethods::request('id_cliente', 0);
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
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        $ruolo = $this->session->getLoggedUserRole();
        $idAp = (int)HTTPMethods::request('id_attivita_pianificata', 0);
        $ap = $idAp ? $this->attivitaPianificataRepo->findById($idAp) : null;
        if (!$ap) {
            $this->view->mostraStatoOperazione(false, "Attività non trovata.");
            return;
        }

        $cliente = $this->recuperaClientePerDisdetta($ruolo);
        if (!$cliente) {
            return;
        }
        $this->eseguiDisdetta($ap, $cliente);
    }

    private function recuperaClientePerDisdetta(string $ruolo): ?Cliente
    {
        $id = ($ruolo === 'cliente') ? $this->session->getLoggedUserId() : (int)HTTPMethods::request('id_cliente', 0);
        return $this->clienteRepo->findById($id);
    }

    private function eseguiDisdetta(AttivitaPianificata $ap, Cliente $cliente): void
    {
        $rit = "calendario?data=" . $ap->getGiorno()->format('Y-m-d');
        $inQueue = $this->codaAttesaRepo->findOneByClienteAndAttivita($cliente, $ap);
        if (!$this->clienteRepo->isIscrittoAAttivita($cliente, $ap)) {
            if ($inQueue) {
                $this->rimuoviDaCoda($inQueue, $rit);
            } else {
                $this->view->mostraStatoOperazione(false, "Il cliente non risulta iscritto o in coda.", $rit, "Torna al Calendario");
            }
            return;
        }
        try {
            $cliente->cancellaIscrizioneAttivita($ap);
            $ap->setPrenotati(max(0, $ap->getPrenotati() - 1));
            $this->clienteRepo->save($cliente);
            self::scorriCodaEnotifica($ap, $this->codaAttesaRepo, $this->clienteRepo, $this->messaggioRepo);
            $this->view->mostraStatoOperazione(true, "Iscrizione cancellata con successo.", $rit, "Torna al Calendario");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore disdetta: " . $e->getMessage(), $rit, "Torna al Calendario");
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

    public static function scorriCodaEnotifica(AttivitaPianificata $ap, CodaAttesaRepositoryInterface $codaRepo, ClienteRepositoryInterface $cliRepo, MessaggioRepositoryInterface $msgRepo): void
    {
        $codaPrimo = $codaRepo->findPrimoInCoda($ap);
        if ($codaPrimo) {
            $cli = $codaPrimo->getCliente();
            $cli->iscriviAAttivita($ap);
            $ap->setPrenotati($ap->getPrenotati() + 1);
            $codaRepo->delete($codaPrimo);
            $cliRepo->save($cli);
            $ogg = "Iscrizione automatica all'attività";
            $cont = "Ciao " . $cli->getNome() . ",\n\nti informiamo che si è liberato un posto e sei stato iscritto automaticamente all'attività: " . $ap->getAttivita()->getNome() . " in data " . $ap->getGiorno()->format('d/m/Y') . " alle ore " . $ap->getOrario() . ":00.\n\nSaluti,\nLo staff di GymFly";
            $msg = new Messaggio($ap->getAllenatore(), $ogg, $cont);
            $msg->aggiungiDestinatario($cli);
            $msgRepo->save($msg);
            @mail($cli->getEmail(), $ogg, $cont, "From: no-reply@gymfly.com\r\nReply-To: support@gymfly.com\r\nContent-Type: text/plain; charset=utf-8");
        }
    }

    // =========================================================================
    // 4. PRENOTA SESSIONE PRIVATA (/prenota-sessione-privata)
    // =========================================================================

    public function prenotaSessionePrivata(): void
    {
        $palestra = $this->recuperaPalestraUtente();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        if (HTTPMethods::method() === 'GET') {
            $this->view->redirect('calendario');
            return;
        }
        $this->eseguiPrenotazioneSessionePrivata($palestra);
    }

    private function eseguiPrenotazioneSessionePrivata(Palestra $palestra): void
    {
        $idCliente = (int)HTTPMethods::post('id_cliente', 0);
        $dataStr = trim(HTTPMethods::post('data', ''));
        $oraIn = trim(HTTPMethods::post('ora_inizio', ''));
        $oraFi = trim(HTTPMethods::post('ora_fine', ''));
        $rit = ($dataStr !== '') ? "calendario?data=" . $dataStr : "calendario";

        if ($idCliente <= 0 || $dataStr === '' || $oraIn === '' || $oraFi === '') {
            $this->view->mostraStatoOperazione(false, "Campi obbligatori mancanti.", $rit, "Torna al Calendario");
            return;
        }
        $cliente = $this->clienteRepo->findById($idCliente);
        if (!$cliente || $cliente->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Cliente non valido.", $rit, "Torna al Calendario");
            return;
        }
        $idAllenatore = $this->session->getLoggedUserId();
        $allenatore = $this->allenatoreRepo->findById($idAllenatore);
        $this->salvaSpEInviaMessaggio($cliente, $allenatore, $dataStr, $oraIn, $oraFi, $rit);
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
        $dataStr = trim(HTTPMethods::post('data', ''));
        $ritorno = ($dataStr !== '') ? "calendario?data=" . $dataStr : "calendario";
        $this->eseguiCreazionePianificata($palestra, $ritorno);
    }

    private function eseguiCreazionePianificata(Palestra $palestra, string $ritorno): void
    {
        $dataStr = trim(HTTPMethods::post('data', ''));
        $orario = (int)HTTPMethods::post('orario', 0);
        $idAllenatore = (int)HTTPMethods::post('id_allenatore', 0);
        $allenatore = $this->allenatoreRepo->findById($idAllenatore);

        if (!$allenatore || $allenatore->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Allenatore non valido o non appartenente alla palestra.", $ritorno);
            return;
        }

        $attivita = $this->ottieniOCreaAttivita((int)HTTPMethods::post('id_attivita', 0), $ritorno);
        $sala = $attivita ? $this->ottieniOCreaSala((int)HTTPMethods::post('id_sala', 0), $palestra, $ritorno) : null;
        if (!$attivita || !$sala) {
            return;
        }

        $ripetizione = HTTPMethods::postArray('ripetizione');
        $this->salvaPianificazioni(new \DateTime($dataStr), $ripetizione, $orario, $sala, $allenatore, $attivita, $ritorno);
    }

    private function ottieniOCreaAttivita(int $idAttivita, string $ritorno): ?Attivita
    {
        if ($idAttivita === 0) { // Nuova attività
            $nome = trim(HTTPMethods::post('nuova_attivita_nome', ''));
            $desc = trim(HTTPMethods::post('nuova_attivita_desc', ''));
            $max = (int)HTTPMethods::post('nuova_attivita_max', 0);
            if ($nome === '' || $max <= 0) {
                $this->view->mostraStatoOperazione(false, "Dati nuova attività non validi (Nome obbligatorio, Max > 0).", $ritorno);
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
            $nome = trim(HTTPMethods::post('nuova_sala_nome', ''));
            $max = (int)HTTPMethods::post('nuova_sala_max', 0);
            if ($nome === '' || $max <= 0) {
                $this->view->mostraStatoOperazione(false, "Nome e capienza massima validi obbligatori.", $ritorno);
                return null;
            }
            if ($this->salaRepo->existsByNomeAndPalestra($nome, $palestra)) {
                $this->view->mostraStatoOperazione(false, "La sala esiste già nella tua palestra.", $ritorno);
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
            $this->view->mostraStatoOperazione(true, "Attività pianificata con successo.", $rit, "Torna al Calendario");
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
        $this->eseguiRimozioneAttivitaPianificata($palestra);
    }

    private function eseguiRimozioneAttivitaPianificata(Palestra $palestra): void
    {
        $id = (int)HTTPMethods::request('id_attivita_pianificata', 0);
        $ap = $id ? $this->attivitaPianificataRepo->findById($id) : null;
        if (!$ap) {
            $this->view->mostraStatoOperazione(false, "Attività pianificata non trovata.");
            return;
        }
        if ($ap->getAllenatore()->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Accesso negato. L'attività appartiene a un'altra palestra.");
            return;
        }

        try {
            foreach ($ap->getUtenti() as $cliente) {
                $cliente->cancellaIscrizioneAttivita($ap);
                $this->clienteRepo->save($cliente);
            }
            $this->attivitaPianificataRepo->delete($ap);
            $this->view->mostraStatoOperazione(true, "Attività pianificata rimossa con successo.");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Impossibile rimuovere l'attività pianificata.");
        }
    }

    // =========================================================================
    // 6. DISDICI SESSIONE PRIVATA (/disdici-sessione-privata)
    // =========================================================================

    public function disdiciSessionePrivata(): void
    {
        $palestra = $this->recuperaPalestraUtente();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        $this->eseguiDisdettaSessionePrivata($palestra);
    }

    private function eseguiDisdettaSessionePrivata(Palestra $palestra): void
    {
        $idAllenatore = (int)HTTPMethods::request('id_allenatore', 0);
        $oraInStr = trim(HTTPMethods::request('ora_inizio', ''));
        $oraFiStr = trim(HTTPMethods::request('ora_fine', ''));
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

    private function recuperaPalestraUtente(): ?Palestra     //recupera la palestra in questo controller, utilizzando la funzione statica recuperaPalestraUtenteStatic per evitare ripetizioni di codice in altri controller
    {
        return self::recuperaPalestraUtenteStatic(
            $this->session,
            $this->utenteRepo,
            $this->palestraRepo,
            $this->clienteRepo
        );
    }

    public static function recuperaPalestraUtenteStatic(   //statico in modo tale da poter essere richiamato anche senza istanza della classe, utilizzato in altri controller per evitare ripetizioni di codice
        Session $session,
        UtenteRepositoryInterface $utenteRepo,
        PalestraRepositoryInterface $palestraRepo,
        ClienteRepositoryInterface $clienteRepo
    ): ?Palestra {
        $id = $session->getLoggedUserId();
        $ruolo = $session->getLoggedUserRole();
        if (!$id || !$ruolo) {
            return null;
        }
        if ($ruolo === 'amministratore') {
            $admin = $utenteRepo->findById($id);
            return ($admin instanceof Amministratore) ? $palestraRepo->findByAmministratore($admin) : null;
        } elseif ($ruolo === 'allenatore') {
            $trainer = $utenteRepo->findById($id);
            return ($trainer instanceof Allenatore) ? $trainer->getPalestra() : null;
        } elseif ($ruolo === 'cliente') {
            $cliente = $clienteRepo->findById($id);
            return $cliente ? $cliente->getPalestra() : null;
        }
        return null;
    }
}
