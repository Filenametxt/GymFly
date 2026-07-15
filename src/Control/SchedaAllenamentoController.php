<?php
namespace App\Control;

use App\Entity\Scheda;
use App\Entity\Allenamento;
use App\Entity\DettaglioAllenamento;
use App\Entity\Cliente;
use App\Entity\Allenatore;
use App\Entity\Esercizio;
use App\Entity\Messaggio;
use App\Entity\Repository\SchedaRepositoryInterface;
use App\Entity\Repository\AllenatoreRepositoryInterface;
use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\EsercizioRepositoryInterface;
use App\Entity\Repository\ProgressoRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrineSchedaRepository;
use App\Foundation\Persistence\Repository\DoctrineAllenatoreRepository;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineEsercizioRepository;
use App\Foundation\Persistence\Repository\DoctrineProgressoRepository;
use App\View\Interface\SchedaAllenamentoView;
use App\View\SchedaAllenamentoViewSmarty;
use App\Entity\ProgressoCarico;
use App\Entity\ProgressoRipetizioni;
use App\Entity\ProgressoDurata;
use App\Foundation\Session;
use Doctrine\ORM\EntityManagerInterface;

class SchedaAllenamentoController
{
    private SchedaRepositoryInterface $schedaRepo;
    private AllenatoreRepositoryInterface $allenatoreRepo;
    private ClienteRepositoryInterface $clienteRepo;
    private EsercizioRepositoryInterface $esercizioRepo;
    private ProgressoRepositoryInterface $progressoRepo;
    private SchedaAllenamentoView $view;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Session $session
    ) {
        $this->schedaRepo = new DoctrineSchedaRepository($this->entityManager);
        $this->allenatoreRepo = new DoctrineAllenatoreRepository($this->entityManager);
        $this->clienteRepo = new DoctrineClienteRepository($this->entityManager);
        $this->esercizioRepo = new DoctrineEsercizioRepository($this->entityManager);
        $this->progressoRepo = new DoctrineProgressoRepository($this->entityManager);
        $this->view = new SchedaAllenamentoViewSmarty();
    }

    /**
     * 1. Richiesta scheda ad allenatore (Caso 31)
     * GET: apriFormRichiestaScheda()
     */
    public function apriFormRichiestaScheda(): void
    {
        $idCliente = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();

        if (!$idCliente || $ruolo !== 'cliente') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Questa funzionalità è riservata ai clienti.", "login");
            return;
        }

        $cliente = $this->clienteRepo->findById($idCliente);
        if (!$cliente) {
            $this->view->mostraStatoOperazione(false, "Cliente non trovato.", "login");
            return;
        }

        // Impedisce l'invio di più richieste contemporanee se c'è già una richiesta pendente
        $schedaPendente = $this->schedaRepo->findPendenteByCliente($cliente);
        if ($schedaPendente !== null) {
            $this->view->mostraStatoOperazione(false, "Hai già una richiesta di scheda in attesa di essere creata dal tuo allenatore.", "dashboard-cliente");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Recupera gli allenatori della palestra del cliente
            $palestra = $cliente->getPalestra();
            $allenatori = $palestra 
                ? $this->allenatoreRepo->findByPalestra($palestra)
                : [];

            $this->view->mostraTemplate('richiedi_scheda.tpl', [
                'utente' => $cliente,
                'allenatori' => $allenatori
            ]);
            return;
        }

        // Se POST, delega a richiestaSchedaAllenatore
        $obiettivo = trim($_POST['obiettivo'] ?? '');
        $nAllenamenti = isset($_POST['n_allenamenti']) ? (int)$_POST['n_allenamenti'] : 3;
        $cfAllenatore = trim($_POST['cf_allenatore'] ?? '');

        $this->richiestaSchedaAllenatore($obiettivo, $nAllenamenti, $cfAllenatore);
    }

    /**
     * POST: richiestaSchedaAllenatore(obiettivo, nAllenamenti, cfAllenatore)
     */
    public function richiestaSchedaAllenatore(string $obiettivo, int $nAllenamenti, string $cfAllenatore): void
    {
        $idCliente = $this->session->getLoggedUserId();
        $cliente = $this->clienteRepo->findById($idCliente);

        if (!$cliente) {
            $this->view->mostraStatoOperazione(false, "Cliente non trovato.", "login");
            return;
        }

        if ($obiettivo === '' || $cfAllenatore === '') {
            $this->view->mostraStatoOperazione(false, "Tutti i campi del modulo sono obbligatori.", "richiedi-scheda");
            return;
        }

        $allenatore = $this->allenatoreRepo->findByCF($cfAllenatore);
        if (!$allenatore) {
            $this->view->mostraStatoOperazione(false, "Allenatore selezionato non trovato.", "richiedi-scheda");
            return;
        }

        // Limita a massimo 7 allenamenti
        if ($nAllenamenti < 1 || $nAllenamenti > 7) {
            $this->view->mostraStatoOperazione(false, "Il numero di allenamenti richiesto deve essere compreso tra 1 e 7.", "richiedi-scheda");
            return;
        }

        try {
            // Quando il cliente richiede la nuova scheda, quella presente attualmente deve essere eliminata
            $vecchieSchede = $this->schedaRepo->findByCliente($cliente);
            foreach ($vecchieSchede as $vs) {
                $cliente->setScheda(null);
                $this->entityManager->flush();
                $this->schedaRepo->delete($vs);
            }
            $this->entityManager->flush();

            // Crea una nuova scheda (richiesta/bozza) nel DB per salvare Obiettivo e pre-creare gli allenamenti
            $scheda = new Scheda(
                "Richiesta Nuova Scheda",
                new \DateTimeImmutable('today'),
                new \DateTimeImmutable('+1 month'),
                $obiettivo,
                $cliente,
                $allenatore
            );
            $this->entityManager->persist($scheda);

            // Pre-crea il numero di allenamenti richiesti di default (A, B, C, D...)
            $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
            for ($i = 0; $i < $nAllenamenti; $i++) {
                $nuovoAll = new Allenamento("Allenamento " . $letters[$i], "Informazioni sull'allenamento " . $letters[$i]);
                $scheda->addAllenamento($nuovoAll);
                $this->entityManager->persist($nuovoAll);
            }

            // NON associamo la scheda al cliente (id_scheda in Cliente rimane null)
            $this->entityManager->flush();

            // Simula invio e-mail all'allenatore
            error_log("EMAIL CONFIRMATION: Inviata email a " . $allenatore->getEmail() . " per richiesta scheda da parte del cliente " . $cliente->getEmail());

            $this->view->mostraStatoOperazione(true, "Richiesta inviata con successo al tuo Allenatore. La vecchia scheda è stata rimossa.", "dashboard-cliente");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore durante l'invio della richiesta: " . $e->getMessage(), "richiedi-scheda");
        }
    }

    /**
     * 2. Creazione della scheda (Caso 16, 17)
     * GET: apriFormCreazioneScheda()
     */
    public function apriFormCreazioneScheda(): void
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();

        if (!$idAllenatore || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }

        $allenatore = $this->entityManager->find(Allenatore::class, $idAllenatore);
        if (!$allenatore) {
            $this->view->mostraStatoOperazione(false, "Allenatore non trovato.", "login");
            return;
        }

        // Se POST o GET con cf, delega a selezionaUtentePerScheda(cf)
        if (isset($_REQUEST['cf'])) {
            $this->selezionaUtentePerScheda(trim($_REQUEST['cf']));
            return;
        }

        // Altrimenti reindirizza alla lista clienti per farne scegliere uno
        header("Location: clienti");
        exit;
    }

    /**
     * selezionaUtentePerScheda(cf)
     * Genera la scheda vuota associata a CF del cliente.
     */
    public function selezionaUtentePerScheda(string $cf): void
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $allenatore = $this->allenatoreRepo->findById($idAllenatore);

        $cliente = $this->clienteRepo->findByCF($cf);

        if (!$cliente || ($cliente->getPalestra() && $cliente->getPalestra()->getId() !== $allenatore->getPalestra()->getId())) {
            $this->view->mostraStatoOperazione(false, "Cliente non valido o appartenente ad altra palestra (Prevenzione IDOR).", "dashboard-allenatore");
            return;
        }

        // Trova la scheda di richiesta creata dal cliente
        $scheda = $this->schedaRepo->findPendenteByCliente($cliente);

        if (!$scheda) {
            // Se non c'è una richiesta pendente, creiamo una nuova scheda vuota (bozza)
            // Prima eliminiamo eventuali vecchie schede del cliente per evitare accumuli
            $vecchieSchede = $this->schedaRepo->findByCliente($cliente);
            foreach ($vecchieSchede as $vs) {
                $cliente->setScheda(null);
                $this->entityManager->flush();
                $this->schedaRepo->delete($vs);
            }
            $this->entityManager->flush();

            $scheda = new Scheda(
                "Nuovo Protocollo",
                new \DateTimeImmutable('today'),
                new \DateTimeImmutable('+1 month'),
                "Inserisci obiettivo",
                $cliente,
                $allenatore
            );
            $this->entityManager->persist($scheda);

            // Pre-crea 3 allenamenti di default (A, B, C)
            $letters = ['A', 'B', 'C'];
            for ($i = 0; $i < 3; $i++) {
                $nuovoAll = new Allenamento("Allenamento " . $letters[$i], "Informazioni sull'allenamento " . $letters[$i]);
                $scheda->addAllenamento($nuovoAll);
                $this->entityManager->persist($nuovoAll);
            }
        } else {
            // Modifica il nome da "Richiesta Nuova Scheda" a "Nuovo Protocollo" per iniziare la compilazione
            $scheda->setNome_scheda("Nuovo Protocollo");
        }

        try {
            $this->entityManager->flush();

            // Reindirizza al form di inserimento dei dati e dettagli della scheda (Passo 2)
            $redirectUrl = "modifica-scheda?id=" . $scheda->getId();
            if (isset($_REQUEST['azione_rapida'])) {
                $redirectUrl .= "&azione_rapida=1";
            }
            header("Location: " . $redirectUrl);
            exit();
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore nell'avvio della modifica scheda: " . $e->getMessage(), "dashboard-allenatore");
        }
    }

    /**
     * 3. Modifica/Salvataggio della scheda (Caso 20)
     * GET: apriFormModificaScheda(idScheda)
     */
    public function apriFormModificaScheda(): void
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();

        if (!$idAllenatore || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }

        $allenatore = $this->entityManager->find(Allenatore::class, $idAllenatore);
        if (!$allenatore) {
            $this->view->mostraStatoOperazione(false, "Allenatore non trovato.", "login");
            return;
        }

        $idScheda = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_POST['id_scheda'] ?? 0);

        if ($idScheda <= 0) {
            $palestra = $allenatore->getPalestra();
            $schede = [];
            if ($palestra) {
                $schede = $this->schedaRepo->findByPalestra($palestra);
            }
            if (!empty($schede)) {
                $redirectUrl = "modifica-scheda?id=" . $schede[0]->getId();
                if (isset($_GET['azione_rapida'])) {
                    $redirectUrl .= "&azione_rapida=1";
                }
                header("Location: " . $redirectUrl);
            } else {
                header("Location: dashboard-allenatore");
            }
            return;
        }

        $scheda = $this->schedaRepo->findById($idScheda);

        if (!$scheda) {
            $this->view->mostraStatoOperazione(false, "Scheda di allenamento non trovata.", "modifica-scheda");
            return;
        }

        // Controllo IDOR: l'allenatore deve appartenere alla stessa palestra del cliente della scheda
        if ($scheda->getCliente()->getPalestra()->getId() !== $allenatore->getPalestra()->getId()) {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Violazione sicurezza IDOR rilevata.", "dashboard-allenatore");
            return;
        }

        // Se richiesto, gestisce l'opzione "Copia da un altro Cliente" (clonazione)
        if (isset($_GET['copia_da'])) {
            $idSorgente = (int)$_GET['copia_da'];
            $sorgente = $this->schedaRepo->findById($idSorgente);
            if ($sorgente) {
                // Rimuove gli allenamenti correnti per fare spazio alla copia
                foreach ($scheda->getAllenamenti() as $all) {
                    $scheda->removeAllenamento($all);
                }
                $this->entityManager->flush();

                // Copia allenamenti e dettagli
                foreach ($sorgente->getAllenamenti() as $srcAll) {
                    $nuovoAll = new Allenamento($srcAll->getNome(), $srcAll->getDescrizione());
                    $scheda->addAllenamento($nuovoAll);
                    $this->entityManager->persist($nuovoAll);

                    /** @var DettaglioAllenamento $srcDet */
                    foreach ($srcAll->getDettagli() as $srcDet) {
                        $nuovoDet = new DettaglioAllenamento(
                            $srcDet->getEsercizio(),
                            $nuovoAll,
                            $srcDet->getSerie(),
                            $srcDet->getRipetizioni(),
                            $srcDet->getCarico(),
                            $srcDet->getTempo()
                        );
                        $nuovoAll->addDettaglio($nuovoDet);
                        $this->entityManager->persist($nuovoDet);
                    }
                }
                $this->entityManager->flush();
            }
        }

        $esercizi = $this->esercizioRepo->findAll();
        
        // Trova le altre schede attive nella palestra per l'azione "Copia da esistente"
        $altreSchede = $this->schedaRepo->findAltreByPalestra($allenatore->getPalestra(), $idScheda);

        $this->view->mostraTemplate('gestione_scheda.tpl', [
            'utente' => $allenatore,
            'scheda' => $scheda,
            'esercizi' => $esercizi,
            'altre_schede' => $altreSchede,
            'azione_rapida' => isset($_GET['azione_rapida']) ? 1 : 0
        ]);
    }

    /**
     * POST: modificaScheda(idScheda, campiModificati)
     * Compila e aggiorna la scheda con tutti i dati.
     */
    public function modificaScheda(): void
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $allenatore = $this->entityManager->find(Allenatore::class, $idAllenatore);

        $idScheda = (int)($_POST['id_scheda'] ?? 0);
        $scheda = $this->schedaRepo->findById($idScheda);

        if (!$scheda || $scheda->getCliente()->getPalestra()->getId() !== $allenatore->getPalestra()->getId()) {
            $this->view->mostraStatoOperazione(false, "Scheda di allenamento non trovata o accesso negato.", "dashboard-allenatore");
            return;
        }

        $nomeScheda = trim($_POST['nome_scheda'] ?? '');
        $dataInizioStr = $_POST['data_inizio'] ?? '';
        $dataFineStr = $_POST['data_fine'] ?? '';
        $obiettivo = trim($_POST['obiettivo'] ?? '');
        $azione = $_POST['azione'] ?? 'salva'; // 'salva' o 'invia'

        if ($nomeScheda === '' || $dataInizioStr === '' || $dataFineStr === '') {
            $this->view->mostraStatoOperazione(false, "Nome scheda, data inizio e fine sono obbligatori.", "modifica-scheda?id=" . $idScheda);
            return;
        }

        try {
            $dataInizio = new \DateTimeImmutable($dataInizioStr);
            $dataFine = new \DateTimeImmutable($dataFineStr);

            $scheda->setNome_scheda($nomeScheda);
            $scheda->setData_inizio($dataInizio);
            $scheda->setData_fine($dataFine);
            $scheda->setObiettivo($obiettivo);

            // Svuota gli allenamenti esistenti
            foreach ($scheda->getAllenamenti() as $all) {
                $scheda->removeAllenamento($all);
            }
            $this->entityManager->flush();

            // Costruisce la nuova lista di allenamenti ed esercizi
            $workoutsData = $_POST['workouts'] ?? [];
            $recuperoMap = [];

            foreach ($workoutsData as $wData) {
                $nomeWorkout = trim($wData['nome'] ?? 'Allenamento');
                $descrizioneWorkout = trim($wData['descrizione'] ?? '');

                $allenamento = new Allenamento($nomeWorkout, $descrizioneWorkout);
                $scheda->addAllenamento($allenamento);
                $this->entityManager->persist($allenamento);

                $dettagliData = $wData['dettagli'] ?? [];
                foreach ($dettagliData as $dData) {
                    $idEsercizio = (int)($dData['esercizio_id'] ?? 0);
                    $esercizio = $this->entityManager->find(Esercizio::class, $idEsercizio);

                     if ($esercizio) {
                         $serie = (int)($dData['serie'] ?? 1);
                         $ripetizioni = isset($dData['ripetizioni']) && $dData['ripetizioni'] !== '' ? (int)$dData['ripetizioni'] : null;
                         $carico = (float)($dData['carico'] ?? 0.0);
                         $tempo = isset($dData['tempo']) && trim($dData['tempo']) !== '' ? trim($dData['tempo']) : null;
 
                         $dettaglio = new DettaglioAllenamento(
                             $esercizio,
                             $allenamento,
                             $serie,
                             $ripetizioni,
                             $carico,
                             $tempo
                         );
                         $allenamento->addDettaglio($dettaglio);
                         $this->entityManager->persist($dettaglio);
                     }
                 }
             }
 
             // Flush per salvare i dati nel DB
             $this->entityManager->flush();

            // Sincronizza ed esegue il salvataggio specifico
            if ($azione === 'invia') {
                $this->inviaScheda($idScheda);
            } else {
                $this->salvaScheda($idScheda);
            }

        } catch (\InvalidArgumentException $e) {
            $this->view->mostraStatoOperazione(false, "Errore nei dati della scheda: " . $e->getMessage(), "modifica-scheda?id=" . $idScheda);
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Si è verificato un errore durante il salvataggio: " . $e->getMessage(), "modifica-scheda?id=" . $idScheda);
        }
    }

    /**
     * inviaScheda(idScheda)
     * Salva per allenatore e cliente ed invia email di conferma.
     */
    public function inviaScheda(int $idScheda): void
    {
        $scheda = $this->schedaRepo->findById($idScheda);
        if ($scheda) {
            $this->schedaRepo->save($scheda);
            $scheda->getCliente()->setScheda($scheda);
            $this->entityManager->flush();

            // Invia un messaggio interno al cliente
            $coach = $scheda->getAllenatore();
            $cliente = $scheda->getCliente();
            $oggetto = "Nuova Scheda di Allenamento";
            $contenuto = "Ciao " . $cliente->getNome() . ", ho realizzato la tua nuova scheda di allenamento: '" . $scheda->getNome_scheda() . "'. Puoi consultarla nella sezione dedicata.";
            
            $messaggio = new Messaggio($coach, $oggetto, $contenuto);
            $messaggio->aggiungiDestinatario($cliente);
            $this->entityManager->persist($messaggio);
            $this->entityManager->flush();

            $this->view->mostraStatoOperazione(true, "Scheda di allenamento salvata e inviata al cliente con successo.", "clienti", "Torna a Gestione Clienti");
        }
    }

    /**
     * salvaScheda(idScheda)
     * Salva come bozza per l'allenatore.
     */
    public function salvaScheda(?int $idScheda = null): void
    {
        if ($idScheda === null) {
            // Chiamata dal FrontController per POST generico su /salva-scheda
            $this->modificaScheda();
            return;
        }

        $scheda = $this->schedaRepo->findById($idScheda);
        if ($scheda) {
            $this->schedaRepo->save($scheda);
            // Non associamo la scheda al cliente (bozza/non ancora inviata)
            $this->entityManager->flush();

            $this->view->mostraStatoOperazione(true, "Scheda di allenamento salvata come bozza per l'allenatore.", "dashboard-allenatore");
        }
    }

    /**
     * eliminaScheda(idScheda)
     * Caso 17: Rimuove la scheda dal database.
     */
    public function eliminaScheda(): void
    {
        $idUtente = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();

        if (!$idUtente || ($ruolo !== 'amministratore' && $ruolo !== 'allenatore')) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }

        $idScheda = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $scheda = $this->schedaRepo->findById($idScheda);

        if (!$scheda) {
            $this->view->mostraStatoOperazione(false, "Scheda non trovata.", "dashboard-" . $ruolo);
            return;
        }

        // Controllo IDOR
        if ($ruolo === 'allenatore') {
            $allenatore = $this->entityManager->find(Allenatore::class, $idUtente);
            if ($scheda->getCliente()->getPalestra()->getId() !== $allenatore->getPalestra()->getId()) {
                $this->view->mostraStatoOperazione(false, "Accesso negato. IDOR rilevato.", "dashboard-allenatore");
                return;
            }
        }

        try {
            $cliente = $scheda->getCliente();
            $cliente->setScheda(null);
            $this->entityManager->flush();

            $this->schedaRepo->delete($scheda);
            $this->view->mostraStatoOperazione(true, "Scheda di allenamento rimossa con successo.", "dashboard-" . $ruolo);
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore nella rimozione della scheda: " . $e->getMessage(), "dashboard-" . $ruolo);
        }
    }

    /**
     * 4. Visualizzazione scheda da parte del cliente (Caso 27)
     * visualizzaScheda()
     */
    public function visualizzaScheda(): void
    {
        // Disattiva cache del browser per visualizzare sempre gli ultimi aggiornamenti del coach
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $idCliente = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();

        if (!$idCliente || $ruolo !== 'cliente') {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }

        $cliente = $this->entityManager->find(Cliente::class, $idCliente);
        if (!$cliente) {
            $this->view->mostraStatoOperazione(false, "Cliente non trovato.", "login");
            return;
        }

        $scheda = $cliente->getScheda();
        if (!$scheda) {
            $this->view->mostraStatoOperazione(false, "Non hai ancora nessuna scheda attiva.", "dashboard-cliente");
            return;
        }

        $this->view->mostraTemplate('visualizza_scheda.tpl', [
            'utente' => $cliente,
            'scheda' => $scheda
        ]);
    }

    /**
     * 6. Modifica scheda da parte del cliente (Caso 29)
     * apriFormModificaSchedaCliente() - Rende il form per la modifica autonoma
     */
    public function apriFormModificaSchedaCliente(): void
    {
        $idCliente = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();

        if (!$idCliente || $ruolo !== 'cliente') {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }

        $cliente = $this->entityManager->find(Cliente::class, $idCliente);
        if (!$cliente || !$cliente->getScheda()) {
            $this->view->mostraStatoOperazione(false, "Scheda non trovata o non attiva.", "dashboard-cliente");
            return;
        }

        $scheda = $cliente->getScheda();

        $idAllenamento = isset($_REQUEST['id_allenamento']) ? (int)$_REQUEST['id_allenamento'] : 0;
        $allenamento = $this->entityManager->find(Allenamento::class, $idAllenamento);

        if (!$allenamento || $allenamento->getScheda()->getId() !== $scheda->getId()) {
            $this->view->mostraStatoOperazione(false, "Allenamento non trovato o non associato alla tua scheda.", "visualizza-scheda");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraTemplate('modifica_dettagli.tpl', [
                'utente' => $cliente,
                'scheda' => $scheda,
                'allenamento' => $allenamento
            ]);
            return;
        }

        // Se POST, delega a modificaDatiScheda
        $dettagliModificati = $_POST['dettagli'] ?? [];
        $oggi = new \DateTimeImmutable('today');
        foreach ($dettagliModificati as $idDet => $data) {
            $ripetizioni = isset($data['ripetizioni']) && $data['ripetizioni'] !== '' ? (int)$data['ripetizioni'] : null;
            $carico = isset($data['carico']) ? (float)$data['carico'] : 0.0;
            $tempo = isset($data['tempo']) && trim($data['tempo']) !== '' ? trim($data['tempo']) : null;

            $dettaglio = $this->entityManager->find(DettaglioAllenamento::class, (int)$idDet);
            if ($dettaglio && $dettaglio->getAllenamento()->getScheda()->getId() === $scheda->getId()) {
                $oldReps = $dettaglio->getRipetizioni();
                $oldCarico = $dettaglio->getCarico();
                $oldTempo = $dettaglio->getTempo();

                $dettaglio->setRipetizioni($ripetizioni);
                if ($carico >= 0) {
                    $dettaglio->setCarico($carico);
                }
                $dettaglio->setTempo($tempo);

                // Salvataggio dei progressi storici se i valori cambiano
                if ($carico !== $oldCarico && $carico > 0) {
                    $progCarico = new ProgressoCarico($oggi, $cliente, $dettaglio->getEsercizio(), $carico);
                    $this->entityManager->persist($progCarico);
                }
                if ($ripetizioni !== $oldReps && $ripetizioni !== null && $ripetizioni > 0) {
                    $progReps = new ProgressoRipetizioni($oggi, $cliente, $dettaglio->getEsercizio(), (float)$ripetizioni);
                    $this->entityManager->persist($progReps);
                }
                if ($tempo !== $oldTempo && $tempo !== null) {
                    $durataVal = (float)$tempo;
                    if ($durataVal > 0) {
                        $progDurata = new ProgressoDurata($oggi, $cliente, $dettaglio->getEsercizio(), $durataVal);
                        $this->entityManager->persist($progDurata);
                    }
                }
            }
        }
        $this->entityManager->flush();

        $this->view->mostraStatoOperazione(true, "Dettagli tecnici della scheda aggiornati con successo.", "visualizza-scheda");
    }

    /**
     * 7. Visualizzazione progressi cliente relativi ad una scheda (richiesto da allenatore)
     */
    public function visualizzaProgressiCliente(): void
    {
        $idUtente = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();

        if (!$idUtente || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Questa pagina è riservata agli allenatori.", "login");
            return;
        }

        $allenatore = $this->entityManager->find(Allenatore::class, $idUtente);
        if (!$allenatore) {
            $this->view->mostraStatoOperazione(false, "Allenatore non trovato.", "login");
            return;
        }

        $idCliente = isset($_GET['id_cliente']) ? (int)$_GET['id_cliente'] : 0;
        $cliente = $this->clienteRepo->findById($idCliente);
        if (!$cliente) {
            $this->view->mostraStatoOperazione(false, "Cliente non trovato.", "clienti");
            return;
        }

        // Controllo IDOR: l'allenatore deve essere nella stessa palestra del cliente
        if ($cliente->getPalestra() && $cliente->getPalestra()->getId() !== $allenatore->getPalestra()->getId()) {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Violazione sicurezza rilevata.", "dashboard-allenatore");
            return;
        }

        $scheda = $cliente->getScheda();
        if (!$scheda) {
            // Cerca un'eventuale bozza/scheda compilata non ancora attiva se non c'è una attiva
            $schede = $this->schedaRepo->findByCliente($cliente);
            if (!empty($schede)) {
                $scheda = $schede[0];
            }
        }

        if (!$scheda) {
            $this->view->mostraStatoOperazione(false, "Il cliente selezionato non ha ancora nessuna scheda di allenamento associata.", "clienti");
            return;
        }

        $workoutsData = [];
        foreach ($scheda->getAllenamenti() as $allenamento) {
            $eserciziData = [];
            /** @var DettaglioAllenamento $dettaglio */
            foreach ($allenamento->getDettagli() as $dettaglio) {
                $esercizio = $dettaglio->getEsercizio();
                
                // Carica tutti i progressi legati a questo cliente e questo esercizio, ordinati per data
                $progressi = $this->progressoRepo->findByClienteAndEsercizio($cliente, $esercizio);

                $puntiCarico = [];
                $puntiReps = [];
                $puntiDurata = [];
                $storicoCompleto = [];

                foreach ($progressi as $p) {
                    $dataStr = $p->getData()->format('d/m/Y');
                    $dataGrafico = $p->getData()->format('d/m');

                    if ($p instanceof \App\Entity\ProgressoCarico) {
                        $val = $p->getNuovoCarico();
                        $puntiCarico[] = ['data' => $dataGrafico, 'valore' => $val];
                        $storicoCompleto[] = ['data' => $dataStr, 'tipo' => 'Carico', 'valore' => $val . ' Kg'];
                    } elseif ($p instanceof \App\Entity\ProgressoRipetizioni) {
                        $val = $p->getNuovoNRipetizioni();
                        $puntiReps[] = ['data' => $dataGrafico, 'valore' => $val];
                        $storicoCompleto[] = ['data' => $dataStr, 'tipo' => 'Ripetizioni', 'valore' => $val . ' rip.'];
                    } elseif ($p instanceof \App\Entity\ProgressoDurata) {
                        $val = $p->getNuovaDurata();
                        $puntiDurata[] = ['data' => $dataGrafico, 'valore' => $val];
                        $storicoCompleto[] = ['data' => $dataStr, 'tipo' => 'Durata', 'valore' => $val . ' sec'];
                    }
                }

                // Genera le coordinate SVG per ciascun grafico
                $svgPuntiCarico = $this->calcolaCoordinateGrafico($puntiCarico);
                $svgPuntiReps = $this->calcolaCoordinateGrafico($puntiReps);
                $svgPuntiDurata = $this->calcolaCoordinateGrafico($puntiDurata);

                // Ordina storico dal più recente al più vecchio per visualizzarlo in tabella
                usort($storicoCompleto, function ($a, $b) {
                    return strcmp($b['data'], $a['data']);
                });

                $eserciziData[] = [
                    'dettaglio' => $dettaglio,
                    'esercizio' => $esercizio,
                    'puntiCarico' => $svgPuntiCarico,
                    'puntiReps' => $svgPuntiReps,
                    'puntiDurata' => $svgPuntiDurata,
                    'storico' => $storicoCompleto,
                    'hasCarico' => count($puntiCarico) > 0,
                    'hasReps' => count($puntiReps) > 0,
                    'hasDurata' => count($puntiDurata) > 0
                ];
            }

            $workoutsData[] = [
                'allenamento' => $allenamento,
                'esercizi' => $eserciziData
            ];
        }

        $this->view->mostraTemplate('progressi_cliente.tpl', [
            'utente' => $allenatore,
            'cliente' => $cliente,
            'scheda' => $scheda,
            'workouts' => $workoutsData
        ]);
    }

    /**
     * Helper per calcolare le coordinate SVG basato sui dati grezzi
     */
    private function calcolaCoordinateGrafico(array $puntiRaw): array
    {
        if (count($puntiRaw) === 0) {
            return [];
        }

        $valori = array_column($puntiRaw, 'valore');
        $minVal = min($valori) - 2;
        if ($minVal < 0) {
            $minVal = 0;
        }
        $maxVal = max($valori) + 2;
        $range = $maxVal - $minVal ?: 1;

        $width = 380;
        $height = 80;
        $padX = 35;
        $padY = 15;

        $puntiCoo = [];
        $count = count($puntiRaw);
        foreach ($puntiRaw as $i => $pt) {
            $val = $pt['valore'];
            $x = $padX + ($i * ($width / ($count - 1 ?: 1)));
            $y = $padY + $height - (($val - $minVal) / $range * $height);
            $puntiCoo[] = [
                'x' => $x,
                'y' => $y,
                'valore' => $val,
                'data' => $pt['data']
            ];
        }
        return $puntiCoo;
    }
}
